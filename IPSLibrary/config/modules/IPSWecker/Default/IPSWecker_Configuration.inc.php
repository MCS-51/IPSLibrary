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

	/**@defgroup IPSWecker_configuration IPSWecker Konfiguration
	* @ingroup IPSWecker
	* @{
	*
	* @file          IPSWecker_Configuration.inc.php
	* @author        Andr� Czwalina
	* @version
	* Version 1.00.5, 22.04.2012<br/>
	*
	* Konfigurations File f�r IPSWecker
	*
	*/
	IPSUtils_Include ("IPSWecker_Constants.inc.php",      "IPSLibrary::app::modules::IPSWecker");


	/**
	* Definiert die Anzahl der Meldungen, die im Applikation Logging Window angezeigt werden.
	*/
	define ("c_LogMessage_Count",			19);

	/**
	* Definiert das Bundesland f�r die Feiertage
	*  Fuer c_Property_Bundesland bitte folgende Abkuerzungen benutzen:
	*  BW = Baden-W�rttemberg
	*  BY = Bayern
	*  BE = Berlin
	*  BB = Brandenburg
	*  HB = Bremen
	*  HH = Hamburg
	*  HE = Hessen
	*  MV = Mecklenburg-Vorpommern
	*  NI = Niedersachsen
	*  NW = Nordrhein-Westfalen
	*  RP = Rheinland-Pfalz
	*  SL = Saarland
	*  SN = Sachsen
	*  ST = Sachen-Anhalt
	*  SH = Schleswig-Holstein
	*  TH = Th�ringen
	*/
	define ("c_Property_Bundesland",  "HH");

	/**
	*
	* Definition der Weck Zeiten. Es k�nnen max. 9 (1..9) Wecker angelegt werden.
	* Die Konfiguration erfolgt in Form eines Arrays, f�r jeden Wecker wird ein Eintrag im Array erzeugt.
	*
	* Der Eintrag "c_Property_Name" spezifiziert den Namen Weckers, der im WebFront und in den Log's angezeigt
	* wird.
	*
	* Der Eintrag "c_Property_StopSensor" ist derzeit noch ohne Funktion
	*
	* Der Eintrag "c_Property_FrostTemp" Schwellwert Temperatur z.B. 3; Wird die Temperatur 3� unterschritten z.B. 2,9� wird fr�her geweckt.
	*
	* Der Eintrag "c_Property_FrostSensor" hier ist die ObjektID des Temperatursensor anzugeben.
	* Direkt die ObjektID des Wertes (Float). Nicht der Instanz.
	*
	* Der Eintrag "c_Property_FrostTime" hier ist die Zeit in Minuten einzutragen,
	* die fr�her geweckt werden soll, wenn der Wert "c_Property_FrostTemp" unterschritten wird.
	* IMMER Positive Zahlen angeben.
	* Die Zeitangabe mu� kleiner sein als "c_Property_SnoozeTime"
	*
	* Der Eintrag "c_Property_SnoozeTime" hier ist die Zeit in Minuten einzutragen, nachdem der Wecker eine weitere Aktion ausf�hrt.
	* z.B. Weckzeit 20:00, SnoozeTime 5. Dann wird um 20:00 geweckt und um 20:05 wird snooze ausgef�hrt. Um z.B. das Radio lauter zustellen etc.
	* Die Zeitangabe mu� kleiner sein als "c_Property_EndTime"
	*
	* Der Eintrag "c_Property_EndTime" hier ist die Zeit in Minuten einzutragen, nachdem der Wecker eine weitere Aktion ausf�hrt.
	* Diese Zeit ist dazu gedacht um z.B. den Wecker wieder auszuschalten. Falls man vergessen hat der Wecker w�hrend des Urlaubs abzuschalten.
	* Die Zeitangabe mu� gr��er sein als "c_Property_SnoozeTime"
	*
	* Alle Aktionen m�ssen in dem Script "IPSWecker_Cusom" als Callback Methode konfiguriert werden.
	*
	* Eine �nderung des Parameters c_Property_Name erfordert ein Ausf�hren der Installation, ebenso wie das hinzuf�gen eines
	* weiteren Weckers.
	*
	* Alle weiteren Parameter k�nnen ohne Installation ver�ndert werden.
	*
	* Beispiel:
	* @code
		function get_WeckerConfiguration() {
			return array(
				c_WeckerCircle.'1  =>	array(
					c_Property_Name           =>   'Woche',      WeckerName
					c_Property_StopSensor	  =>   '',           ObjectID des Stopsensor. Sobald Variable aktualisiert wird, wird das StopEvent in IPSWecker_Custom f�r den Wecker ausgel�st.
					c_Property_FrostTemp		  =>   2,            Sobald diese Temperatur unterschitten wird wird fr�her geweckt
					c_Property_FrostSensor	  =>   53094  ,      ObjectID des Temperatursensor. KEINE INSTANZ-ObjectID
					c_Property_FrostTime		  =>   15,           Zeit (Minuten) die fr�her geweckt werden soll
					c_Property_SnoozeTime     =>   5,            Zeit (Minuten) nach Weckzeit f�r Weckernachdruck (Lautst�rke erh�hung, Licht an, Wasser marsch etc.)
					c_Property_EndTime  		  =>   60,           Zeit (Minuten) nach Weckzeit f�r weitere Aktion. Gedacht zum abschalten von Licht Radio, falls man nicht da war zum wecken.
					c_Property_Schichtgruppe  =>   '',           Schichtbetrieb. Alle Schichtzeiten die zusammen geh�ren m�ssen die gleich Nr. Eintragen. '' = deaktiviert z.B. Papa die '1', Mama die'2'
					c_Property_Schichtzyklus  =>   array(),      Kalenderwochen als Integer in dem dieser Wecker automatisch eingeschaltet wird. Voraussetzung ist Schichtgruppe ist nicht ''.
  					c_Property_GoogleKalender =>   array(        Google Kalender Konfiguration
					  c_Property_User_ID =>'',                   UserID = Google Kalender Email Adresse
					  c_Property_MagicCookie=>'',                Google Kalender Magic Cookie
					  ),
		       ));
		  }
	* @endcode
	*
	* ACHTUNG GOOGLE KALENDER WIRD ZUR ZEIT NICHT AUSGEWERTET! Kommt vielleicht irgendwann
	*/

	function get_WeckerConfiguration() {
		return array(
			c_WeckerCircle.'1'  =>	array(
				c_Property_Name           =>   'Woche',
				c_Property_StopSensor	  =>   '',
				c_Property_FrostTemp		  =>   2,
				c_Property_FrostSensor	  =>   ''  ,
				c_Property_FrostTime		  =>   10,
				c_Property_SnoozeTime     =>   5,
				c_Property_EndTime  		  =>   60,
            c_Property_Schichtgruppe  =>   '',
				c_Property_Schichtzyklus  =>   array(),
				c_Property_GoogleKalender =>   array(c_Property_User_ID =>'', c_Property_MagicCookie=>'',),
			),
			c_WeckerCircle.'2'  =>	array(
				c_Property_Name           =>   'Wochenende',
				c_Property_StopSensor	  =>   '',
				c_Property_FrostTemp		  =>   2,
				c_Property_FrostSensor	  =>   '' ,
				c_Property_FrostTime		  =>   10,
				c_Property_SnoozeTime  	  =>   5,
				c_Property_EndTime  		  =>   60,
            c_Property_Schichtgruppe  =>   '',
				c_Property_Schichtzyklus  =>   array(),
				c_Property_GoogleKalender =>   array(c_Property_User_ID =>'', c_Property_MagicCookie=>'',),
			),
		);
	}


	/** @}*/
?>
