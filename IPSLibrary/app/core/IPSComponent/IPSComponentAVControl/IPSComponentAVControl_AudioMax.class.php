<?
	/**@addtogroup ipscomponent
	 * @{
	 *
	 * @file          IPSComponentAVControl_AudioMax.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	IPSUtils_Include ('IPSComponentAVControl.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentAVControl');
	IPSUtils_Include ("AudioMax.inc.php",                'IPSLibrary::app::hardware::AudioMax');

   /**
    * @class IPSComponentAVControl_AudioMax
    *
    * Definiert ein IPSComponentAVControl_AudioMax Object, das ein IPSComponentAVControl Object mit Hilfe der
	 * AudioMax MultiRoom Steuerung e-Service Online implementiert
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	class IPSComponentAVControl_AudioMax extends IPSComponentAVControl{

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung des IPSComponentAVControl_AudioMax
		 *
		 * @param integer $instanceId InstanceId des AudioMax
		 */
		public function __construct($instanceId) {
			$this->instanceId = (int)$instanceId;
		   if ($this->instanceId==null) {
		   	$this->instanceId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.AudioMax.AudioMax_Server');
			}
		}

		/**
		 * @public
		 *
		 * Funktion liefert String IPSComponent Constructor String.
		 * String kann dazu ben�tzt werden, das Object mit der IPSComponent::CreateObjectByParams
		 * wieder neu zu erzeugen.
		 *
		 * @return string Parameter String des IPSComponent Object
		 */
		public function GetComponentParams() {
			return get_class(this).','.$this->instanceId;
		}


		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der ausl�senden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleAVControl $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleAVControl $module) {
			$parameters    = explode(AM_COM_SEPARATOR, $value);
			if (count($parameters)<4) return;

			$type     = $parameters[0];
			$device   = $parameters[1];
			$command  = $parameters[2];
			if ($type<>AM_TYP_SET) return;

			switch($command) {
				case AM_CMD_POWER:
				case AM_CMD_ROOM:
				   for ($roomId=0;$roomId<AM_CONFIG_ROOM_COUNT;$roomId++) {
					   $status = AudioMax_GetMainPower($this->instanceId) and AudioMax_GetRoomPower($this->instanceId, $roomId);
						$module->SyncPower($status, $roomId, $this);
				   }
					break;
				case AM_CMD_AUDIO:
					if (count($parameters)<6) return;
					$roomId   = $parameters[3];
					$function = $parameters[4];
					$value    = $parameters[5];
					switch($function) {
						case AM_FNC_BALANCE:
						   $module->SyncBalance($value, $roomId, $this);
							break;
						case AM_FNC_VOLUME:
						   $module->SyncVolume($value, $roomId, $this);
							break;
						case AM_FNC_TREBLE:
						   $module->SyncTreble($value, $roomId, $this);
							break;
						case AM_FNC_MIDDLE:
						   $module->SyncMiddle($value, $roomId, $this);
							break;
						case AM_FNC_BASS:
						   $module->SyncBass($value, $roomId, $this);
							break;
						case AM_FNC_INPUTSELECT:
						   $module->SyncSource($value, $roomId, $this);
							break;
						case AM_FNC_INPUTGAIN:
							break;
						default:
							break;
					}
					break;
				default:
					break;
			}
		}

		/**
		 * @public
		 *
		 * Ein/Ausschalten eines Raumes/Ausgangs
		 *
		 * @param integer $outputId Ausgang der ge�ndert werden soll (Wertebereich 0 - x)
		 * @param boolean $value Wert f�r Power (Wertebereich false=Off, true=On)
		 */
		public function SetPower($outputId, $value) {
		   AudioMax_SetRoomPower($this->instanceId, $outputId, $value);
			if ($value) {
		   	AudioMax_SetMainPower($this->instanceId, $value);
			} else {
			   $allRoomesOff = true;
				for ($roomId=0;$roomId<AM_CONFIG_ROOM_COUNT;$roomId++) {
					$allRoomesOff = $allRoomesOff and !AudioMax_GetRoomPower($this->instanceId, $roomId);
				}
				if ($allRoomesOff) {
			   	AudioMax_SetMainPower($this->instanceId, $value);
				}
			}
		}

		/**
		 * @public
		 *
		 * Retourniert Power Zustand eines Raumes
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return boolean Wert der Lautst�rke (Wertebereich false=Off, true=On)
		 */
		public function GetPower($outputId) {
			return AudioMax_GetMainPower($this->instanceId) and
			       AudioMax_GetRoomPower($this->instanceId, $outputId);
		}


		/**
		 * @public
		 *
		 * Setzen des Eingangs/Source f�r einen Ausgang
		 *
		 * @param integer $outputId Ausgang der ge�ndert werden soll (Wertebereich 0 - x)
		 * @param integer $value Eingang der gesetzt werden soll (Wertebereich 0 - x)
		 */
		public function SetSource($outputId, $value) {
			AudioMax_SetRoomPower($this->instanceId, $outputId, $value);
		}

		/**
		 * @public
		 *
		 * Retourniert aktuellen Eingang eines Raumes
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return integer Eingang der gerade gew�hlt ist (Wertebereich 0 - x)
		 */
		public function GetSource($outputId) {
			return AudioMax_SetRoomPower($this->instanceId, $outputId);
		}

		/**
		 * @public
		 *
		 * Setzen der Lautst�rke f�r einen Ausgang
		 *
		 * @param integer $outputId Ausgang der ge�ndert werden soll (Wertebereich 0 - x)
		 * @param integer $value Wert der Lautst�rke (Wertebereich 0 - 100)
		 */
		public function SetVolume($outputId, $value) {
		   AudioMax_SetVolume($this->instanceId, $outputId, $value * AM_VAL_VOLUME_MAX / 100);
		}

		/**
		 * @public
		 *
		 * Retourniert aktuelle Lautst�rke eines Ausganges
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return integer Wert der Lautst�rke (Wertebereich 0 - 100)
		 */
		public function GetVolume($outputId) {
		   return AudioMax_GetVolume($this->instanceId, $outputId) * 100 / AM_VAL_VOLUME_MAX;
		}

		/**
		 * @public
		 *
		 * Setzen des Mutings f�r einen Ausgang
		 *
		 * @param integer $outputId Ausgang der ge�ndert werden soll (Wertebereich 0 - x)
		 * @param boolean $value Wert f�r Muting (Wertebereich true oder false)
		 */
		public function SetMute($outputId, $value) {
		   return AudioMax_SetMute($this->instanceId, $outputId, $value);
		}

		/**
		 * @public
		 *
		 * Liefert Muting Status eines Ausgangs
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return boolean Wert f�r Muting (Wertebereich true oder false)
		 */
		public function GetMute($outputId) {
		   return AudioMax_GetMute($this->instanceId, $outputId);
		}

		/**
		 * @public
		 *
		 * Setzen der Balance f�r einen Ausgang
		 *
		 * @param integer $outputId Ausgang der ge�ndert werden soll (Wertebereich 0 - x)
		 * @param integer $value Wert f�r Balance (Wertebereich: Links 0 - 50 , 51 - 100 Rechts)
		 */
		public function SetBalance($outputId, $value) {
		   AudioMax_SetBalance($this->instanceId, $outputId, $value * AM_VAL_BALANCE_MAX / 100);
		}

		/**
		 * @public
		 *
		 * Retourniert aktuelle Balance eines Ausgangs
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return integer Wert f�r Balance (Wertebereich: Links 0 - 50 , 51 - 100 Rechts)
		 */
		public function GetBalance($outputId) {
		   return AudioMax_GetBalance($this->instanceId, $outputId) * 100 / AM_VAL_BALANCE_MAX;
		}

		/**
		 * @public
		 *
		 * Setzen der H�hen f�r einen Ausgang
		 *
		 * @param integer $outputId Ausgang der ge�ndert werden soll (Wertebereich 0 - x)
		 * @param integer $value Wert f�r H�hen (Wertebereich 0 - 100)
		 */
		public function SetTreble($outputId, $value) {
		   AudioMax_SetTreble($this->instanceId, $outputId, $value * AM_VAL_TREBLE_MAX / 100);
		}

		/**
		 * @public
		 *
		 * Liefert Wert der H�hen eines Ausgangs
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return integer Wert der H�hen (Wertebereich 0 -100)
		 */
		public function GetTreble($outputId) {
		   return AudioMax_GetTreble($this->instanceId, $outputId) * 100 / AM_VAL_TREBLE_MAX;
		}

		/**
		 * @public
		 *
		 * Setzen der Mitten f�r einen Ausgang
		 *
		 * @param integer $outputId Ausgang der ge�ndert werden soll (Wertebereich 0 - x)
		 * @param integer $value Wert f�r Mitten (Wertebereich 0 - 100)
		 */
		public function SetMiddle($outputId, $value) {
		   AudioMax_SetMiddle($this->instanceId, $outputId, $value * AM_VAL_MIDDLE_MAX / 100);
		}

		/**
		 * @public
		 *
		 * Liefert Wert der Mitten eines Ausgangs
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return integer Wert der Mitten (Wertebereich 0 -100)
		 */
		public function GetMiddle($outputId) {
		   return AudioMax_GetMiddle($this->instanceId, $outputId) * 100 / AM_VAL_MIDDLE_MAX;
		}

		/**
		 * @public
		 *
		 * Setzen der B�sse f�r einen Ausgang
		 *
		 * @param integer $outputId Ausgang der ge�ndert werden soll (Wertebereich 0 - x)
		 * @param integer $value Wert f�r B�sse (Wertebereich 0 - 100)
		 */
		public function SetBass($outputId, $value) {
		   AudioMax_SetBass($this->instanceId, $outputId, $value * AM_VAL_BASS_MAX / 100);
		}

		/**
		 * @public
		 *
		 * Liefert Wert der B�sse eines Ausgangs
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return integer Wert der B�sse (Wertebereich 0 -100)
		 */
		public function GetBass($outputId) {
		   return AudioMax_GetBass($this->instanceId, $outputId) * 100 / AM_VAL_BASS_MAX;
		}

	}

	/** @}*/
?>