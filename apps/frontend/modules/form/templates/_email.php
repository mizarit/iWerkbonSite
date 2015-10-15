Beste beheerder,

Er is een formulier ingevuld op de website door <?php echo $object->getName(); ?>.

Formulier: <?php echo $object->getTitle(); ?> 

<?php
foreach (unserialize($object->getData()) as $key => $value) {
  echo str_pad($key, 20, ' ', STR_PAD_RIGHT).$value."\n";
}

?>

Verzonden op <?php echo date('d-m-Y') ?> om <?php echo date('H:i:s') ?> vanaf <?php echo $_SERVER['REMOTE_ADDR'] ?> 

Met vriendelijke groet,

Health Challenge

info@health-challenge.nl
 