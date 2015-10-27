<?php

function Send($phone, $content){
		// initialiseer object
		$sms = new Mollie();
		
		// Kies een gateway
		$sms->setGateway(1);
		
		// Stel gebruikersnaam en wachtwoord van Mollie.nl in
		$user = 'PlekjeVrij';
		$pass = '3452EK19';
		$sms->setLogin($user, $pass);
		
		// Stel stuff in
		$sms->setTariff('20');
		$sms->setMember(true);
		$sms->setOriginator('PlekjeVrij');
//		$sms->setShortcode('1008');
//		$sms->setKeyword('WIN REIS');
		
		// format date
		$datum = date("Y-m-d H:i:s");
		
		// Format the phonenumber
		$sphone = str_replace('-','',$phone);
		$number = ltrim( $sphone, '0' );
		$bphone = '31'.$number;

		// Voeg een ontvanger toe aan het bericht
		$sms->addRecipients($bphone);
		// Verstuur het SMS-bericht
		$sms->sendSMS($content);
		
			if ($sms->success){
		// insert new bid
				$html = 'SMS succesvol verstuurd.';
			}else{
				$html = 'Er is iets mis gegaan. De administrator is op de hoogte gesteld.';
				mail('j.guyt@emconcepts.eu','Er is een SMS-fout opgetreden bij PlekjeVrij','(foutcode: '.$sms->resultcode.' - '.$sms->resultmessage.')');
			} #END if/else
	return $html;
	} #END function Send