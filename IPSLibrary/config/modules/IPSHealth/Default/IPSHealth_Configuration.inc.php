<?
	/*
	* This file is part of the IPSLibrary.
	*
	* The IPSLibrary is free software: you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published
	* by the Free Software Foundation, either version 3 of the License, or
	* (at your option) any later version.
	*
	* The IPSLibrary is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
	*/

	/**@defgroup IPSHealth_configuration IPSHealth Konfiguration
	* @ingroup IPSHealth
	* @{
	*
	* @file          IPSHealth_Configuration.inc.php
	* @author        Andr� Czwalina
	* @version
	* Version 1.00.3, 22.04.2012<br/>
	*
	* Konfigurations File f�r IPSHealth
	* c_HealthTimeout = Timeout zur Meldung in Sekunden
	* c_HealthVariables = Array der zu �berwachenden Variablen
	*
	*/
	IPSUtils_Include ("IPSHealth_Constants.inc.php",      "IPSLibrary::app::modules::IPSHealth");


	/**
	* Definiert die Anzahl der Meldungen, die im Applikation Logging Window angezeigt werden.
	*/
	define ("c_LogMessage_Count",			19);



	/**
	* SYSTEM - INFO
	*	Definiert die Zeit bis Meldung, des letzte Schreibzugriffs in die Datenbank Logging.
	*/
	define ("c_Warn_Schwellwert",     180);
	define ("c_Log_Content",     		'Alarm: die logging.db ist nicht mehr aktuell, die letzte �nderung vor: ');
	define ("c_Mail_Subject",     	'IPS Alert: logging.db nicht aktuell!');
	define ("c_Mail_Content",     	'IPS meldet: die IPS-DB wurde zu lange nicht aktualisiert, siehe folgende Detaildaten:');
	define ("c_Mail_Instanz",        23452 );
	define ("c_SYS_Logging",        	true );
	define ("c_SYS_HDD",             'HDD1'); //HDD auf dem IPS installiert ist

	/**
	*
	*
	* Beispiel:
	* @code
	* @endcode
	*
	* @return string Liefert Array mit Bew�sserungs Kreisen
	*/
//print_r(get_HealthConfiguration());

	function get_HealthConfiguration() {
		return array(

         c_HealthCircle.'1'   => array(
               c_CircleName      =>    '60 Sekunden �berwachung',
		      	c_HealthTimeout  	=>    65,
					c_HealthVariables =>		array(
						12665  ,
					),
			),

         c_HealthCircle.'2'   => array(
               c_CircleName      =>    '360 Sekunden �berwachung',
					c_HealthTimeout  	=>    360,
					c_HealthVariables =>		array(
						37772  ,
						19278,
						15530 ,
						15256 ,
						55970 ,
					),
			),

         c_HealthCircle.'3'   => array(
               c_CircleName      =>    '25 Stunden �berwachung',
					c_HealthTimeout  	=>    25*60*60,
					c_HealthVariables =>		array(
						40599 ,
					),
			),
		);
	}


	/** @}*/
?>