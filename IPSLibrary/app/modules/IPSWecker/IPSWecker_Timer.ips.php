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

	/**@addtogroup IPSWecker
	 * @{
	 *
	 * @file          IPSWecker_Timer.ips.php
	 * @author        Andr� Czwalina
	 * @version
	* Version 1.00.5, 22.04.2012<br/>
	 *
	 *
	 */

	include_once "IPSWecker.inc.php";

	switch ($_IPS['SENDER']) {
		case 'TimerEvent':
			$eventId 	=  $_IPS['EVENT'];
			if (IPS_GetName($eventId) =='Timer_Event') {
					IPSWeckerChangeAktivCircle();
					break;
			}

			$CircleName = substr(IPS_GetName($eventId),0, strlen(IPS_GetName($eventId))-2);
			$CircleId 	= get_CirclyIdByCircleIdent($CircleName, WECKER_ID_WECKZEITEN);
			IF (IPS_GetKernelVersion() >= "3.10"){
	         $eventtimearr	= array();
				$eventtimearr	= IPS_GetEvent($eventId)['CyclicTimeFrom'];
				$eventTime 		= mktime($eventtimearr['Hour'], $eventtimearr['Minute'], 0);
			}else{
				$eventTime 		= IPS_GetEvent($eventId)['CyclicTimeFrom'];
			}

			$wecker     = AddConfiguration($CircleId);
			IPSLogger_Dbg(__file__, 'Event: Ausl�sung pr�fen f�r '.$wecker['Property']['Name'].' ('.$wecker['Circle']['Name'].')');

			if (function_exists($CircleName)) {
					IPSLogger_Dbg(__file__, 'Weckerfunktion '.$wecker['Circle']['Name'].' Existiert in IPSWecker_Custom.');
					$CircleTime 			= mktime(substr($wecker['ActiveTime'],0,2), substr($wecker['ActiveTime'],3,2), 0);
					$CircleNameMode= '';

					// --------------- Weckbedingungen -------------------
					if ($wecker['RetUrlaub'] == false and $wecker['RetFeiertag'] == false and $wecker['Active'] == true and $wecker['Circle'][c_Control_Global] == true){
							IPSLogger_Dbg(__file__, 'Weckbedingungen f�r Active, Global, Urlaub, Feiertag g�ltig.'.$wecker['Property'][c_Property_Name]);

							// --------------- FrostTime -------------------
							if (get_TimeToleranz($eventTime+($wecker['Property'][c_Property_FrostTime]*60), $CircleTime)){
									IPSLogger_Dbg(__file__, 'FrostTime ausl�sung '.$wecker['Property'][c_Property_Name]);
									if ( getAviableSensor($wecker['Property'][c_Property_FrostSensor]) == 2 ) {
											if ( getvalue($wecker['Property'][c_Property_FrostSensor])  < $wecker['Property'][c_Property_FrostTemp]) {
													IPSLogger_Dbg(__file__, 'Temperatur < FrostTemp = Frostwecken'.$wecker['Property'][c_Property_Name]);

													// --------------- Neue Eventzeit setzen -------------------
													if ($wecker['Circle'][c_Control_Schlummer] == true){
															IF (IPS_GetKernelVersion() == "3.10"){
																$Hour = date("H", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_SnoozeTime]*60));
																$Minute = date("i", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_SnoozeTime]*60));
															   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
															}else{
																IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_SnoozeTime]*60), 0);
															}
													}
													elseif($wecker['Circle'][c_Control_End] == true){
															IF (IPS_GetKernelVersion() == "3.10"){
																$Hour = date("H", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_EndTime]*60));
																$Minute = date("i", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_EndTime]*60));
															   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
															}else{
																IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_EndTime]*60), 0);
															}
													}
													IPSLogger_Dbg(__file__, 'Neue EventTime: '.Date('H:i',IPS_GetEvent($eventId)['CyclicTimeFrom']).' f�r '.IPS_GetName($eventId));
													// --------------- Aktion -------------------
													$eventMode = "FrostTime";
													IPSLogger_Inf(__file__, 'Wecker ausgel�st:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
													IPSWecker_Log('Wecker ausgel�st:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
													$CircleName($CircleId, $wecker['Property'][c_Property_Name], $eventMode);
											} else {
													IPSLogger_Dbg(__file__, 'Wecker auf Normalzeit, da kein Frost '.$wecker['Property'][c_Property_Name]);
													IF (IPS_GetKernelVersion() == "3.10"){
														$Hour = date("H", $CircleTime);
														$Minute = date("i", $CircleTime);
													   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
													}else{
														IPS_SetEventCyclicTimeBounds($eventId, $CircleTime, 0);
													}
											}
									} else {
											IPSLogger_Err(__file__, 'Frostsensor '.$wecker['Property'][c_Property_FrostSensor].' nicht vorhanden '.$wecker['Property'][c_Property_Name]);
											IPSWecker_Log('FEHLER: Frostsensor '.$wecker['Property'][c_Property_FrostSensor].' nicht vorhanden '.$wecker['Property'][c_Property_Name]);
											IF (IPS_GetKernelVersion() == "3.10"){
												$Hour = date("H", $CircleTime);
												$Minute = date("i", $CircleTime);
											   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
											}else{
												IPS_SetEventCyclicTimeBounds($eventId, $CircleTime, 0);
											}
									}
							}

							// --------------- AlarmTime -------------------
							if (get_TimeToleranz($eventTime, $CircleTime)){
									IPSLogger_Dbg(__file__, 'AlarmTime ausl�sung '.$wecker['Property'][c_Property_Name]);
									// --------------- Neue Eventzeit setzen -------------------
									if ($wecker['Circle'][c_Control_Schlummer] == true){
											IF (IPS_GetKernelVersion() == "3.10"){
												$Hour = date("H", $CircleTime+($wecker['Property'][c_Property_SnoozeTime]*60));
												$Minute = date("i", $CircleTime+($wecker['Property'][c_Property_SnoozeTime]*60));
											   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
											}else{
												IPS_SetEventCyclicTimeBounds($eventId, $CircleTime+($wecker['Property'][c_Property_SnoozeTime]*60), 0);
											}
									}
									elseif($wecker['Circle'][c_Control_End] == true){
											IF (IPS_GetKernelVersion() == "3.10"){
												$Hour = date("H", $CircleTime+($wecker['Property'][c_Property_EndTime]*60));
												$Minute = date("i", $CircleTime+($wecker['Property'][c_Property_EndTime]*60));
											   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
											}else{
												IPS_SetEventCyclicTimeBounds($eventId, $CircleTime+($wecker['Property'][c_Property_EndTime]*60), 0);
											}
									}
									elseif($wecker['Circle'][c_Control_Frost] == true){
											IF (IPS_GetKernelVersion() == "3.10"){
												$Hour = date("H", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60));
												$Minute = date("i", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60));
											   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
											}else{
												IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60), 0);
											}
									}

									IF (IPS_GetKernelVersion() >= "3.10"){
							         $neweventtimearr	= array();
										$neweventtimearr	= IPS_GetEvent($eventId)['CyclicTimeFrom'];
										$neweventTime 		= mktime($neweventtimearr['Hour'], $neweventtimearr['Minute'], 0);
									}else{
										$neweventTime 		= IPS_GetEvent($eventId)['CyclicTimeFrom'];
									}
									IPSLogger_Dbg(__file__, 'Neue EventTime: '.Date('H:i', $neweventTime).' f�r '.IPS_GetName($eventId));

									if ($wecker['Active'] == true){
											// --------------- Aktion -------------------
											$eventMode = "AlarmTime";
											IPSLogger_Inf(__file__, 'Wecker ausgel�st:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											IPSWecker_Log('Wecker ausgel�st:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											$CircleName($CircleId, $wecker['Property'][c_Property_Name], $eventMode);
									}
							}

							// --------------- SnoozeTime -------------------
							if ((get_TimeToleranz($eventTime, $CircleTime+($wecker['Property'][c_Property_SnoozeTime]*60)))
							or ( get_TimeToleranz($eventTime, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_SnoozeTime]*60)))){
									IPSLogger_Dbg(__file__, 'SnoozeTime ausl�sung '.$wecker['Property'][c_Property_Name]);
									// --------------- Neue Eventzeit setzen -------------------
									if ($wecker['Circle'][c_Control_End] == true){
											IF (IPS_GetKernelVersion() == "3.10"){
												$Hour = date("H", $CircleTime+($wecker['Property'][c_Property_EndTime]*60));
												$Minute = date("i", $CircleTime+($wecker['Property'][c_Property_EndTime]*60));
											   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
											}else{
												IPS_SetEventCyclicTimeBounds($eventId, $CircleTime+($wecker['Property'][c_Property_EndTime]*60), 0);
											}
									}
									elseif ($wecker['Circle'][c_Control_Frost] == true) {
											IF (IPS_GetKernelVersion() == "3.10"){
												$Hour = date("H", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60));
												$Minute = date("i", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60));
											   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
											}else{
												IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60), 0);
											}
									}
									else {
											IF (IPS_GetKernelVersion() == "3.10"){
												$Hour = date("H", $CircleTime);
												$Minute = date("i", $CircleTime);
											   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
											}else{
												IPS_SetEventCyclicTimeBounds($eventId, $CircleTime, 0);
											}
									}
									IF (IPS_GetKernelVersion() >= "3.10"){
							         $neweventtimearr	= array();
										$neweventtimearr	= IPS_GetEvent($eventId)['CyclicTimeFrom'];
										$neweventTime 		= mktime($neweventtimearr['Hour'], $neweventtimearr['Minute'], 0);
									}else{
										$neweventTime 		= IPS_GetEvent($eventId)['CyclicTimeFrom'];
									}
									IPSLogger_Dbg(__file__, 'Neue EventTime: '.Date('H:i', $neweventTime).' f�r '.IPS_GetName($eventId));
									if ($wecker['Circle'][c_Control_Schlummer] == true){
											// --------------- Aktion -------------------
											$eventMode = "SnoozeTime";
											IPSLogger_Inf(__file__, 'Wecker ausgel�st:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											IPSWecker_Log('Wecker ausgel�st:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											$CircleName($CircleId, $wecker['Property'][c_Property_Name], $eventMode);
									}
							}

							// --------------- EndTime -------------------
							if ((get_TimeToleranz($eventTime, $CircleTime+($wecker['Property'][c_Property_EndTime]*60)))
							or ( get_TimeToleranz($eventTime, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_EndTime]*60)))){
									IPSLogger_Dbg(__file__, 'EndTime ausl�sung '.$wecker['Property'][c_Property_Name]);
									// --------------- Neue Eventzeit setzen -------------------
									if ($wecker['Circle'][c_Control_Frost] == true){
											IF (IPS_GetKernelVersion() == "3.10"){
												$Hour = date("H", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60));
												$Minute = date("i", $CircleTime-($wecker['Property'][c_Property_FrostTime]*60));
											   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
											}else{
												IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60), 0);
											}
									}
									else {
											IF (IPS_GetKernelVersion() == "3.10"){
												$Hour = date("H", $CircleTime);
												$Minute = date("i", $CircleTime);
											   IPS_SetEventCyclicTimeFrom($eventId, intval($Hour), intval($Minute), 0);
											}else{
												IPS_SetEventCyclicTimeBounds($eventId, $CircleTime, 0);
											}
									}
									IF (IPS_GetKernelVersion() >= "3.10"){
							         $neweventtimearr	= array();
										$neweventtimearr	= IPS_GetEvent($eventId)['CyclicTimeFrom'];
										$neweventTime 		= mktime($neweventtimearr['Hour'], $neweventtimearr['Minute'], 0);
									}else{
										$neweventTime 		= IPS_GetEvent($eventId)['CyclicTimeFrom'];
									}
									IPSLogger_Dbg(__file__, 'Neue EventTime: '.Date('H:i', $neweventTime).' f�r '.IPS_GetName($eventId));
									if ($wecker['Circle'][c_Control_End] == true){
											// --------------- Aktion -------------------
											$eventMode = "EndTime";
											IPSLogger_Inf(__file__, 'Wecker ausgel�st:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											IPSWecker_Log('Wecker ausgel�st:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											$CircleName($CircleId, $wecker['Property'][c_Property_Name], $eventMode);
									}
							}
				}
			} else {
					IPSLogger_Err(__file__, "WeckerAktion $CircleName in IPSWecker_Custom existiert nicht. ".$wecker['Property'][c_Property_Name]);
			}
			break;
		case 'WebFront':
			break;
		case 'Execute':
			break;
		case 'RunScript':
			break;
		default:
			IPSLogger_Err(__file__, 'Unknown Sender '.$_IPS['SENDER']);
			break;
	}


	/** @}*/
?>
