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

	/**@addtogroup IPSHealth
	 * @{
	 *
	 * @file          IPSHealth_ChangeSettings.ips.php
	 * @author        Andr� Czwalina
	 * @version
	* Version 1.00.1, 22.04.2012<br/>
	 *
	 *
	 * Script wird f�r das WebFront um �nderungen an den Variablen vorzunehmen
	 *
	 */

	include_once "IPSHealth.inc.php";

	if ($_IPS['SENDER']=='WebFront') {
		$instanceId   = $_IPS['VARIABLE'];
		$ControlId   = get_CirclyIdByControlId($instanceId);
		$ControlType = get_ControlType($instanceId);

		switch($ControlType) {
			case c_Property_DBNeuagg:
			   DB_Reaggregieren($ControlId, $instanceId, $_IPS['VALUE']);
			   break;


			default:
				IPSLogger_Err(__file__, "Error Unknown ControlType $ControlType");
				break;
	   }

	}

	/** @}*/
?>