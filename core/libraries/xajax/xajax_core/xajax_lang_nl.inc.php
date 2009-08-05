<?php
/*
	File: xajax_lang_de.inc.php

	Contains the debug and error messages output by xajax translated to Dutch.

	Title: xajax class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
	
	Translations provided by: (Thank you!)
	- Jeffrey <walkingsoul@gmail.com>
*/

/*
	@package xajax
	@version $Id: xajax_lang_de.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

//SkipAIO

$objLanguageManager =& xajaxLanguageManager::getInstance();
$objLanguageManager->register('de', array(
	'LOGHDR:01' => '** xajax Foutmelding logboek - ',
	'LOGHDR:02' => " **\n",
	'LOGHDR:03' => "\n\n\n",
	'LOGERR:01' => "** Logboek fouten **\n\nxajax was niet in staat om te schrijven naar het logboek:\n",
	'LOGMSG:01' => "** PHP Foutmeldingen: **",
	'CMPRSJS:RDERR:01' => 'Het xajax ongecomprimeerde Javascript bestand kon niet worden gevonden in de: <b>',
	'CMPRSJS:RDERR:02' => '</b>.  map. ',
	'CMPRSJS:WTERR:01' => 'Het xajax gecomprimeerde Javascript bestand kon niet worden geschreven in de: <b>',
	'CMPRSJS:WTERR:02' => '</b> map.  Fout ',
	'CMPRSPHP:WTERR:01' => 'Naar het xajax gecomprimeerde bestand <b>',
	'CMPRSPHP:WTERR:02' => '</b> kon niet worden geschreven.  Fout ',
	'CMPRSAIO:WTERR:01' => 'Naar het xajax gecomprimeerde bestand <b>',
	'CMPRSAIO:WTERR:02' => '/xajaxAIO.inc.php</b> kon niet worden geschreven.  Fout ',
	'DTCTURI:01' => 'xajax Fout: xajax kon de Request URI niet automatisch identificeren.',
	'DTCTURI:02' => 'Alstublieft, specificeer de Request URI expliciet bij het initiëren van het xajax object.',
	'ARGMGR:ERR:01' => 'Misvormd object argument ontvangen: ',
	'ARGMGR:ERR:02' => ' <==> ',
	'ARGMGR:ERR:03' => 'De binnenkomende xajax data kon niet wordt geconverteerd van UTF-8.',
	'XJXCTL:IAERR:01' => 'Ongeldig attribuut [',
	'XJXCTL:IAERR:02' => '] voor element [',
	'XJXCTL:IAERR:03' => '].',
	'XJXCTL:IRERR:01' => 'Ongeldige object aanvraag doorgegeven aan xajaxControl::setEvent',
	'XJXCTL:IEERR:01' => 'Ongeldig attribuut (event name) [',
	'XJXCTL:IEERR:02' => '] voor element [',
	'XJXCTL:IEERR:03' => '].',
	'XJXCTL:MAERR:01' => 'Ontbrekend attribuut [',
	'XJXCTL:MAERR:02' => '] voor element [',
	'XJXCTL:MAERR:03' => '].',
	'XJXCTL:IETERR:01' => "Ongeldige eind-tag; zou 'forbidden' of 'optional' moeten zijn..\n",
	'XJXCTL:ICERR:01' => "Ongeldige klasse gespecificeerd voor html control.; zou %inline, %block of %flow moeten zijn.\n",
	'XJXCTL:ICLERR:01' => 'Ongeldige (control) doorgegeven aan addChild; Zou moeten worden afgeleid van xajaxControl.',
	'XJXCTL:ICLERR:02' => 'Ongeldige (control) doorgegeven aan addChild [',
	'XJXCTL:ICLERR:03' => '] voor element [',
	'XJXCTL:ICLERR:04' => "].\n",
	'XJXCTL:ICHERR:01' => 'Ongeldige parameter doorgegeven aan xajaxControl::addChildren; Array moet bestaan uit xajaxControl objecten.',
	'XJXCTL:MRAERR:01' => 'Ontbrekend attribuut [',
	'XJXCTL:MRAERR:02' => '] voor element [',
	'XJXCTL:MRAERR:03' => '].',
	'XJXPLG:GNERR:01' => 'Retourneer plugin zou de getName functie moeten overschrijven.',
	'XJXPLG:PERR:01' => 'Retourneer plugin zou de proces functie moeten overschrijven.',
	'XJXPM:IPLGERR:01' => 'Poging om ongeldige plugin te registreren: : ',
	'XJXPM:IPLGERR:02' => ' afleiding moet komen van xajaxRequestPlugin of xajaxResponsePlugin.',
	'XJXPM:MRMERR:01' => 'Localisatie van registratie methode faalde voor het volgende: : ',
	'XJXRSP:EDERR:01' => 'Doorgeven van karakter decodering naar de xajaxResponse constructie is verouderd. De nieuwe functie luidt: $xajax->configure("characterEncoding", ...);',
	'XJXRSP:MPERR:01' => 'Ongeldige of ontbrekende plugin naam gedetecteerd in een aanvraag naar xajaxResponse::plugin',
	'XJXRSP:CPERR:01' => "De parameter \$sType in addCreate is verouderd..  De nieuwe functie luidt addCreateInput()",
	'XJXRSP:LCERR:01' => "Het xajax antwoord object kon de commando's niet laden, gezien de meegegeven data geen geldige array is.",
	'XJXRSP:AKERR:01' => 'Ongeldige ge-encodeerde tag naam in array',
	'XJXRSP:IEAERR:01' => 'Ungeeignet kodiertes Array.',
	'XJXRSP:NEAERR:01' => 'Niet gecodeerde array gedetecteerd.',
	'XJXRSP:MBEERR:01' => 'De xajax output kon niet worden geconverteerd naar HTML entities, gezien mb_convert_encoding niet beschikbaar is.',
	'XJXRSP:MXRTERR' => 'Fout: Kann keine verschiedenen Typen in einer einzelnen Antwort verarbeiten.',
	'XJXRSP:MXCTERR' => 'Fout: Kan geen meerdere typen verwisselen in een enkele teruggave.',
	'XJXRSP:MXCEERR' => 'Fout: Kan geen meerdere karakter decoderingen verwerken in een enkele teruggave.',
	'XJXRSP:MXOEERR' => 'Fout: kan geen output entities (true/false) in een enkele teruggave verwerken.',
	'XJXRM:IRERR' => 'Een ongeldig antwoord is geretourneerd tijdens het verwerken van deze aanvraag.',
	'XJXRM:MXRTERR' => 'Fout: Kan geen meerdere typen verwisselen tijdens het verwerken van een enkele aanvraag: '
	));

//EndSkipAIO