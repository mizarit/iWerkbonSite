Beste <?php echo $sf_params->get('name'); ?>,

Hartelijk dank voor uw registratie. Uw inloggegevens zijn:

Gebruikersnaam: <?php echo $sf_params->get('username'); ?> 
Wachtwoord:     <?php echo $sf_params->get('password'); ?> 

U dient u account nog te activeren voor u deze kunt gebruiken. Volg om uw account te activeren onderstaande link:

http://<?php echo $_SERVER['HTTP_HOST'] ?><?php echo url_for('community/activate?u='.$object->getId().'&code='.$code); ?> 

Werkt de link niet? Kopiëer deze dan en plak hem zelf in de adresbalk van uw browser.

Met vriendelijke groet,

Het Star-people team.

StarPeople Bewustzijnsgroep
http://www.star-people.nl
info@star-people.nl
 