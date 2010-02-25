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
	 * Filter condition: Task which have tracks of the person
	 *
	 * @param	Integer		$idPerson
	 * @param	Bool		$negate
	 * @return	Array		Or FALSE
	 */
	public static function Filter_timetrackedPerson($idPerson, $negate = false) {
		$idPerson	= intval($idPerson);
		$queryParts	= false;

		if( $idPerson !== 0 ) {
			$tables	= array(
				'ext_project_task',
				'ext_timetracking_track'
			);
			$compare= $negate ? '!=' : '=';
			$where	= '	ext_timetracking_track.id_task = ext_project_task.id AND
						ext_timetracking_track.id_person_create ' . $compare . ' ' . $idPerson;

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}


	public static function Filter_timetrackedRoles($groupIDs, $negate = false) {
		$queryParts	= false;
		$groupIDs	= TodoyuArray::intExplode(',', $groupIDs, true, true);

		if( sizeof($groupIDs) > 0 ) {
			$tables	= array(
				'ext_project_task',
				'ext_timetracking_track',
				'ext_contact_mm_person_role'
			);
			$where	= '	ext_timetracking_track.id_task = ext_project_task.id AND
						ext_timetracking_track.id_person_create = ext_contact_mm_person_role.id_person AND
						ext_contact_mm_person_role.id_role IN(' . implode(',', $groupIDs) . ')';

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}

}

?>