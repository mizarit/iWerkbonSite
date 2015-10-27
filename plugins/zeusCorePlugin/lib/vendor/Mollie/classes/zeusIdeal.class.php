<?php

/**
 * 
 * Handles full transaction method for iDeal by Mollie
 * 
 * @author ricardo.matters@mizar-it.nl
 *
 */
class zeusIdeal
{
  private $issuers = array();
  private $issuer_id = false;
  private $mollie_id = false;
  private $transaction_id = false;
  private $order_id = 0;
  private $order_amount = 0;
  
  /**
   * 
   * Main dispatcher for the transaction
   * 
   * @author ricardo.matters@mizar-it.nl
   *
   */
  public function __construct($mollie_id)
  {
    $this->mollie_id = $mollie_id;
    
    $this->order_id = $this->getRequestParameter('id');
    $this->order_amount =$this->getRequestParameter('amount');
    
    if (!$this->order_id) $this->quit('Er is geen geldig ordernummer meegegeven');
    if (!$this->order_amount) $this->quit('Er is geen geldig totaalbedrag meegegeven');
    
    if ($this->getRequestParameter('issuer')) {
      $this->issuer_id = $this->getRequestParameter('issuer'); 
    }
    
    if ($this->getRequestParameter('transaction_id')) {
      $this->transaction_id = $this->getRequestParameter('transaction_id'); 
    }
    
    $this->mollie = new zeusMollie($this->mollie_id);
    $this->mollie->setTestMode(false);
    
    $this->getIssuers();
    
    // main dispatcher
    if ($this->transaction_id) {
      $this->executeFinalizeTransaction();
    }
    elseif (!$this->issuer_id) {
      $this->executeSelectIssuer();
    }
    else {
      $this->executeStartTransation();
    }
  }
  
  /**
   * 
   * Returns all current issues
   * 
   * Could be optimized by adding some caching
   * 
   * @author ricardo.matters@mizar-it.nl
   *
   */
  private function getIssuers()
  {
    $xml = $this->getXmlResponse($this->mollie->getBanks());
    foreach ($xml->bank as $bank) {
      $this->issuers[(string)$bank->bank_id] = (string)$bank->bank_name;
    }
  }
  
  private function executeSelectIssuer()
  {
?>
<form action="#" method="POST">
  <fieldset style="border: none;">
    <legend style="display: none;">iDeal form</legend>
    <div class="form-row">
      <div class="form-label"><label for="issuer">Kies uw bank:</label></label>
        <select name="issuer" id="issuer" onchange="this.form.submit();">
<?php foreach ($this->issuers as $issuer_id => $issuer) { ?>
        <option value="<?php echo $issuer_id; ?>"><?php echo $issuer; ?></option>
<?php } ?>
      </select>
      <input type="hidden" name="amount" value="<?php echo $this->order_amount; ?>">
      <input type="hidden" name="id" value="<?php echo $this->order_id; ?>">
    </div>
  </fieldset>
</form>
	
	<?php
    
  }
  
  private function executeStartTransation()
  {
    $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $xml = $this->getXmlResponse($this->mollie->createPayment($this->issuer_id, 'SportsBall betaling', $this->order_amount*100, $url, $url));
    if (strpos($xml->order->message, 'success')) {
  
      $this->transaction_id = $xml->order->transaction_id;
 
    	$sql = "UPDATE orders SET Trackid='".$this->transaction_id."' WHERE ordernumber=".$this->order_id;
    	if (!mysql_query($sql)) {
    	  $this->quit('Er is iets niet goed gegaan bij het verwerken van de transactie');
    	}
	
      header("Location: {$xml->order->URL}");
	    $this->quit();
	    
    }
    else {
  	  $html  = <<<EOT
<h2>Er is een fout opgetreden.</h2>
<p>Klik hieronder om terug te gaan en opnieuw te proberen.</p>
<button onclick="history.back();">Terug</button>\n

EOT;
      $this->quit($html);
    }
  }
  
  private function executeFinalizeTransaction()
  {
    $xml = $this->getXmlResponse($this->mollie->checkPayment($this->transaction_id));
    
    if ($xml->order->payed == 'true') {
      
      $sql = "UPDATE orders SET Paystatus = 4 WHERE Trackid='".$xml->order->transaction_id."'";
      if (!mysql_query($sql)) {
    	  $this->quit('Er is iets niet goed gegaan bij het verwerken van de transactie');
    	}
    	
    	$message = <<<EOT
<h2>Uw betaling is gelukt.</h2>
<p>Uw bestelling wordt zo spoedig mogelijk in behandeling genomen.</p>\n
EOT;
  	}
  	else {
  	  $message = <<<EOT
<h2>Uw betaling is niet gelukt.</h2>
<p>De transactie is niet voltooid. Probeer het later opnieuw.</p>
<p>Als het probleem zich blijft voordoen kunt u contact openen met de beheerder.</p>\n
EOT;
    }
    
    $this->quit($message);
  }
  
  
  /**
   * Gets Mollie XML and prepares it as a simpleXML object
   *
   * @param string $mollie_response
   * @return SimpleXML
   */
  private function getXmlResponse($xml)
  {
    return simplexml_load_string(substr($xml, strpos($xml, '<?xml')));
  }
  
  
  /**
   * 
   * Gracefull exit
   *
   * @param unknown_type $message
   */
  public function quit($message = '')
  {
    if ($message != '') {
      echo $message;
    }
    exit;
  }
  
  /**
   * 
   * Get request parameter from any scope and escape is
   */
  private function getRequestParameter($key) 
  {
    $value = false;
    if (isset($_POST[$key])) {
      $value = $_POST[$key];
    }
    elseif (isset($_GET[$key])) {
      $value = $_GET[$key];
    }
    
    return addslashes($value);
  }
}
?>