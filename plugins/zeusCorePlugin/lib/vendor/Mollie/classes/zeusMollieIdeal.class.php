<?php

 /**
 * Mollie iDeal API
 *
 * For Mollie B.V.
 * More information? Go to www.mollie.nl
 *
 * LICENSE
 *
 * You can use this code freely, if you don't change this comment.
 * Do you make any changes, please provide them back to help us all
 *
 * @category   Mollie
 * @package    Mollie_Ideal
 * @copyright  Copyright (c) 2007 Concepto IT Solution (www.concepto.nl)
 * @author Patrick van Dissel <patrick AT concepto.nl>
 * @link http://www.concepto.nl Concepto IT Solution
 * @version 1.0.1
 */
 
class zeusMollieIdeal
{
    const MINIMUM_TRANSACTION_AMOUNT = 60;

    private $partnerId        = 0;
    private $transactionId    = 0;
    private $bankId           = 0;
    private $amount           = 0;
    private $description      = 'Betaling SportsBall';
    private $reportUrl        = 'report.php';
    private $returnUrl        = 'return.php';
    private $country          = 31;       // Default: The Netherlands (031)
    private $currency         = 'EUR';    // Default: Euro
    private $payed            = false;
    private $bankUrl          = '';
    private $statusMessage    = '';
    private $testmode    			= false;
    private $banks            = array();

    // Info about the bankaccount that made the payment
    // (only available after checking the payment)
    private $consumer         = array();


    /**
     * Constructor
     *
     * @param integer $partnerId
     */
    function __construct($partnerId)
    {
        $this->setPartnerID($partnerId);
    }
		

 # DEBUG
#
# Inhoud van arrays in html
public function print_r_html($data,$return_data=true)
{
    $data = print_r($data,true);
    $data = str_replace( " ","&nbsp;", $data);
    $data = str_replace( "\r\n","<br>\r\n", $data);
    $data = str_replace( "\r","<br>\r", $data);
    $data = str_replace( "\n","<br>\n", $data);

    if (!$return_data) echo $data;
    else return $data;

}


    /**
     * Prepare a Payment
     *
     * @param integer $bankId
     * @param string $description
     * @param integer $amount
     * @param string $reportUrl
     * @param string $returnUrl
     * @return boolean True on succes, false otherwise
     */
    public function createPayment($bankId, $description, $amount, $reportUrl, $returnUrl)
    {
        if (!$this->setBankid($bankId)
            || !$this->setDescription($description)
            || !$this->setAmount($amount)
            || !$this->setReportUrl($reportUrl)
            || !$this->setReturnUrl($returnUrl))
        {
            return 'Var not set';
        }

        $result = $this->sendToHost('www.mollie.nl', '/xml/ideal/',
                                    'a=fetch' .
                                    '&partnerid=' . urlencode($this->getPartnerId()) .
                                    '&bank_id=' .   urlencode($this->getBankId()) .
                                    '&amount=' .    urlencode($this->getAmount()) .
                                    '&reporturl=' . urlencode($this->getReportUrl()) .
                                    '&description=' . urlencode($this->getDescription()) .
                                    '&returnurl=' . urlencode($this->getReturnUrl()));
        if (empty($result)) {
            return 'no result';
        }

return $result;



        list($headers, $xml) = preg_split("/(\r?\n){2}/", $str, 2);

        try {
            $data = new SimpleXMLElement($str);
						
						
						
    		if ($data == false) {
    		    return false;
    		}

    		$data          = $data->order;
    		$transactionId = $data->transaction_id;
    		$amount        = $data->amount;
    		$currency      = $data->currency;
    		$bankUrl       = html_entity_decode($data->URL);
    		$status        = $data->message;

    		if (!$this->setTransactionId($transactionId)
                || !$this->setAmount($amount)
                || !$this->setCurrency($currency)
                || !$this->setBankUrl($bankUrl)
                || !$this->setStatus($status))
            {
                return false;
            }
        } catch (Exception $e) {
            return $e;
        }

        return true;
    }

    /**
     * Check if the payment was succesfull
     *
     * @param integer $transactionId
     * @return boolean True on succes, false otherwise
     */
    public function checkPayment($transactionId)
    {
        // set transaction id
        if (!$this->setTransactionId($transactionId)) {
            return false;
        }

        // check a payment with mollie
        $result = $this->sendToHost('www.mollie.nl', '/xml/ideal/',
                                    'a=check' .
                                    '&partnerid=' .      urlencode($this->getPartnerId()).
                                    '&transaction_id=' . urlencode($this->getTransactionId()).
                                    ($this->testmode ? '&testmode=true' : ''));
        if (empty($result)) {
		      return false;
				}



return $result;



        list($headers, $xml) = preg_split("/(\r?\n){2}/", $result, 2);

        try {
            $data = new SimpleXMLElement($xml);
    		if ($data == false) {
    		    return false;
    		}

    		$data     = $data->order;
    		$payed    = ($data->payed == 'true');
    		$consumer = (array)$data->consumer;
    		$amount   = $data->amount;
    		$status   = $data->message;
    		if (!$this->setPayed($payed)
    		    || !$this->setConsumer($consumer)
                || !$this->setAmount($amount)
                || !$this->setStatus($status)) {
                return false;
            }
        } catch (Exception $e) {
            return $e;
        }
        return true;
    }

    /**
     * Get the URL of the selected bank
     *
     * @return null|string Bank URL when exists, else null
     */
    public function getBankUrl()
    {
        if (is_null($this->bankUrl)) {
            return null;
        }
        return $this->bankUrl;
    }

    /**
     * Set Bank Url
     *
     * @param string $bankUrl
     * @return boolean
     */
    protected function setBankUrl($bankUrl)
    {
        if (!preg_match('|(\w+)://([^/:]+)(:\d+)?/(.*)|', $bankUrl)) {
            return false;
        }
        $this->bankUrl = $bankUrl;
        return true;
    }

    /**
     * Fetch the currently supported banks
     *
     * @static
     * @return boolean|array Array of banks, else boolean false
     */
    public function getBanks()
    {
        // Gets/refreshes banks from Mollie
		$result = self::sendToHost('www.mollie.nl', '/xml/ideal/', 'a=banklist'.($this->testmode ? '&partnerid='.$this->partnerId : ''));
		if (empty($result)) {
		    return false;
		}
return $result;
		list($headers, $xml) = preg_split("/(\r?\n){2}/", $result, 2);

		try {
            $data = new SimpleXMLElement($xml);
    		if ($data == false) {
    		    return false;
    		}

    		// build banks-array
    		$banksArray = array();
    		foreach ($data->bank as $bank) {
    			$banksArray["$bank->bank_id"] = "$bank->bank_name";
    		}
		} catch (Exception $e) {
            return $e;
        }
		return $banksArray;
    }

    /**
     * Check if the given bankId is valid
     *
     * @param integer $bankId
     * @return bool True on valid, False otherwise
     */
    public function checkBank($bankId)
    {
        $banksArray = self::getBanks();
        if (!is_array($banksArray)) {
					$banksArray = array('0031','ABN AMRO','0081','Fortis','0721','Postbank','0021','Rabobank','0751','SNS Bank','9999','TBM Bank');
            return $banksArray;
        }

        if (array_key_exists($bankId, $banksArray)) {
            return $banksArray.'<-- array_key';
        }
        return $banksArray.'<-- false';
    }

    /**
     * Send data to given host
     *
     * @param string $host Full webhost Url (like 'www.mollie.nl')
     * @param string $path Path of script
     * @param string $data Data to send
     * @return string
     */
    protected static function sendToHost($host, $path, $data) {
		// posts data to server
		$fp = @fsockopen($host, 80);
		$buf = '';
		if ($fp) {
			@fputs($fp, "POST $path HTTP/1.0\n");
			@fputs($fp, "Host: $host\n");
			@fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
			@fputs($fp, "Content-length: " . strlen($data) . "\n");
			@fputs($fp, "Connection: close\n\n");
			@fputs($fp, $data);
			while (!feof($fp)) {
				$buf .= fgets($fp, 128);
			}
			fclose($fp);
		}
		return $buf;
	}

    /**
     * Set the Partner Id
     *
     * @param integer $partnerId
     * @return boolean
     */
    protected function setPartnerId($partnerId)
    {
        if (!is_numeric($partnerId)) {
            return false;
        }
        $this->partnerId = $partnerId;
        return true;
    }

    /**
     * Get the Partner Id
     *
     * @return integer
     */
    protected function getPartnerId()
    {
        return $this->partnerId;
    }

    /**
     * Set transaction amount (price) in cents
     *
     * @param integer $amount Minimum amount is the cost of a transaction!
     * @return boolean
     */
    protected function setAmount($amount)
    {
        if (!is_numeric($amount) || $amount < self::MINIMUM_TRANSACTION_AMOUNT ) {
            return false;
        }
        $this->amount = $amount;
        return true;
    }

    /**
     * Get the Amount (price) in cents
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set Currency
     *
     * @param string $currency
     * @return boolean
     */
    protected function setCurrency($currency)
    {
        if (empty($currency)) {
            return false;
        }
        $this->currency = $currency;
        return true;
    }

    /**
     * Get the Currency
     *
     * @return string
     */
    protected function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the Country code
     *
     * @param integer $country
     * @return boolean
     */
    protected function setCountry($country)
    {
        if (!is_numeric($country)) {
            return false;
        }
        $this->country = $country;
        return true;
    }

    /**
     * Get the Country code
     *
     * @return integer
     */
    protected function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the Url where Mollie reports to if the status of one of our
     * payments changes
     *
     * Mollie adds the 'transaction_id' to this url
     *
     * @param string $reportUrl
     * @return boolean
     */
    protected function setReportUrl($reportUrl)
    {
        if (!preg_match('|(\w+)://([^/:]+)(:\d+)?/(.*)|', $reportUrl)) {
            return false;
        }
        $this->reportUrl = $reportUrl;
        return true;
    }

    /**
     * Get the Report Url
     *
     * @return string
     */
    protected function getReportUrl()
    {
        return $this->reportUrl;
    }

    /**
     * Set the Url where Mollie returns to when the payment is done
     *
     * Mollie add the 'transaction_id' to this url
     *
     * @param string $returnUrl
     * @return boolean
     */
    protected function setReturnUrl($returnUrl)
    {
        if (!preg_match('|(\w+)://([^/:]+)(:\d+)?/(.*)|', $returnUrl)) {
            return false;
        }
        $this->returnUrl = $returnUrl;
        return true;
    }

    /**
     * Get the Return Url
     *
     * @return string
     */
    protected function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * Set the Description of the transaction
     *
     * If longer than 30 characters, the description will
     * be trimmed at the 30th character
     *
     * @param unknown_type $description
     * @return unknown
     */
    protected function setDescription($description)
    {
        $description = trim($description);
        if (empty($description)) {
            return false;
        }
        $this->description = substr($description, 0, 29);
        return true;
    }

    /**
     * Get the transaction Description
     *
     * @return string
     */
    protected function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Payed status
     *
     * @param boolean $payed
     * @return boolean
     */
    protected function setPayed($payed)
    {
        if ($payed === false) {
            $this->payed = false;
            return false;
        }
        $this->payed = true;
        return true;
    }

    /**
     * Get the payed status
     *
     * @return boolean
     */
    protected function getPayed()
    {
        return $this->payed;
    }

    /**
     * Set Status message
     *
     * @param string $status
     * @return boolean
     */
    protected function setStatus($status)
    {
        if (empty($status)) {
            return false;
        }
        $this->statusMessage = $status;
        return true;
    }

    /**
     * Get the Status message
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->statusMessage;
    }

    /**
     * Set the Bank Id
     *
     * The supported Bank Id's can be fetched with the
     * getBanks() function
     *
     * @see getBanks
     * @param integer $bankId
     * @return boolean
     */
    protected function setBankId($bankId)
    {
        if (!is_numeric($bankId)) {
            return false;
        }
        $this->bankId = $bankId;
        return true;
    }

    /**
     * Get the Bank Id
     *
     * @return integer
     */
    protected function getBankId()
    {
        return $this->bankId;
    }

    /**
     * Set the Transaction Id
     *
     * @param integer $transactionId
     * @return boolean
     */
    protected function setTransactionId($transactionId)
    {
        if (empty($transactionId)) {
            return false;
        }
        $this->transactionId = $transactionId;
        return true;
    }

    /**
     * Get the Transaction Id
     *
     * @return integer
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set the Consumer information
     *
     * This is information about the bankaccount which payed
     * the transaction
     *
     * The array should have the following elements:
     * <ul>
     *      <li>consumerName</li>
     *      <li>consumerAccount</li>
     *      <li>consumerCity</li>
     * </ul>
     *
     * @param array $consumer
     * @return boolean
     */
    protected function setConsumer($consumer)
    {
        if (!is_array($consumer)) {
            return false;
        }
        $this->consumer = $consumer;
        return true;
    }

    /**
     * Set testmode
     *
     * This is information about the bankaccount which payed
     * the transaction, and is only available after executing
     * @see checkPayment() with a 'true' as the result!
     *
     * The array should have the following elements:
     * <ul>
     *      <li>consumerName</li>
     *      <li>consumerAccount</li>
     *      <li>consumerCity</li>
     * </ul>
     *
     * @return boolean
     */
    public function setTestMode($testmode)
    {
        $this->testmode = $testmode;
				return true;
    }
    
    /**
     * Get the Consumer information
     *
     * This is information about the bankaccount which payed
     * the transaction, and is only available after executing
     * @see checkPayment() with a 'true' as the result!
     *
     * The array should have the following elements:
     * <ul>
     *      <li>consumerName</li>
     *      <li>consumerAccount</li>
     *      <li>consumerCity</li>
     * </ul>
     *
     * @return array
     */
    public function getConsumer()
    {
        return $this->consumer;
    }
}
