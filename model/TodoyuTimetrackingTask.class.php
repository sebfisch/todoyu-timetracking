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
 * Time tracking functions for task
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingTask {

	/**
	 * Default table for database requests
	 */
	const TABLE = 'ext_timetracking_track';



	/**
	 * Get time tracking task tracks
	 *
	 * @param	Integer	$idTask
	 * @return	Array
	 */
	public static function getTaskTracks($idTask) {
		$idTask	= intval($idTask);

		$fields	= '	t.*,
					u.firstname,
					u.lastname';
		$tables	= 	self::TABLE . ' t,
					ext_contact_person u';
		$where	= '	t.id_task 			= ' . $idTask . ' AND
					t.id_person_create 	= u.id';
		$order	= '	t.date_track DESC';

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}



	/**
	 * Get time tracking tab label
	 *
	 * @param	Integer	$idTask
	 * @return	String
	 */
	public static function getTabLabel($idTask) {
		return Label('timetracking.title');
	}



	/**
	 * Get time tracking tab content
	 *
	 * @param	Integer	$idTask
	 * @param	Boolean $active
	 * @return	String
	 */
	public static function getTabContent($idTask, $active = false) {
		$idTask		= intval($idTask);

		return TodoyuTimetrackingRenderer::renderTaskTab($idTask);
	}



	/**
	 * Save time tracking tab inline form
	 *
	 * @param	Array	$formData
	 */
	public static function saveTabInlineForm(array $formData) {
		$idTrack	= intval($formData['id']);
		$data		= array(
			'date_track'			=> TodoyuTime::parseDate($formData['date_track']),
			'workload_tracked'		=> TodoyuTime::parseDuration($formData['workload_tracked']),
			'workload_chargeable'	=> TodoyuTime::parseDuration($formData['workload_chargeable']),
			'comment'				=> $formData['comment']
		);

		TodoyuTimetracking::updateRecord($idTrack, $data);
	}

}

?>