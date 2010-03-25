<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

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