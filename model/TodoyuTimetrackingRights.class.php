<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Timetracking rights manager
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingRights {

	/**
	 * Deny access
	 * Shortcut for timetracking
	 *
	 * @param	String		$right		Denied right
	 */
	private static function deny($right) {
		TodoyuRightsManager::deny('timetracking', $right);
	}



	/**
	 * Check whether user has edit rights for this track
	 * Deny access if right is missing
	 *
	 * @param	Integer		$idTrack
	 */
	public static function restrictEdit($idTrack) {
		if( ! self::isEditAllowed($idTrack) ) {
			self::deny('track:edit');
		}
	}



	/**
	 * Check whether user has edit rights for this track
	 *
	 * @param	Integer		$idTrack
	 * @return	Boolean
	 */
	public static function isEditAllowed($idTrack) {
		$idTrack	= intval($idTrack);
		$track		= TodoyuTimetracking::getTrack($idTrack);

		if( $track->isCurrentPersonCreator() ) {
			if( allowed('timetracking', 'task:editOwn') ) {
				return true;
			}
		} else {
			return allowedAny('timetracking', 'editAll,editAllChargeable');
		}

		return false;
	}

}

?>