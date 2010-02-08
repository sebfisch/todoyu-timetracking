<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 snowflake productions gmbh
*  All rights reserved
*
*  This script is part of the todoyu project.
*  The todoyu project is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License, version 2,
*  (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html) as published by
*  the Free Software Foundation;
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Timetracking task filter
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingTaskFilter {

	/**
	 * Filter condition: Task which have tracks of the user
	 *
	 * @param	Integer		$idUser
	 * @param	Bool		$negate
	 * @return	Array		Or FALSE
	 */
	public static function Filter_timetrackedUser($idUser, $negate = false) {
		$idUser		= intval($idUser);
		$queryParts	= false;

		if( $idUser !== 0 ) {
			$tables	= array(
				'ext_project_task',
				'ext_timetracking_track'
			);
			$compare= $negate ? '!=' : '=';
			$where	= '	ext_timetracking_track.id_task = ext_project_task.id AND
						ext_timetracking_track.id_user_create ' . $compare . ' ' . $idUser;

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}


	public static function Filter_timetrackedGroups($groupIDs, $negate = false) {
		$queryParts	= false;
		$groupIDs	= TodoyuArray::intExplode(',', $groupIDs, true, true);

		if( sizeof($groupIDs) > 0 ) {
			$tables	= array(
				'ext_project_task',
				'ext_timetracking_track',
				'ext_user_mm_user_group'
			);
			$where	= '	ext_timetracking_track.id_task = ext_project_task.id AND
						ext_timetracking_track.id_user_create = ext_user_mm_user_group.id_user AND
						ext_user_mm_user_group.id_group IN(' . implode(',', $groupIDs) . ')';

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}

}

?>