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
 * Timetracking functions for task
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */

class TodoyuTimetrackingTask {

	/**
	 * Working table
	 *
	 */
	const TABLE = 'ext_timetracking_track';



	/**
	 * Get timetracking task tracks
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
					ext_user_user u';
		$where	= '	t.id_task = ' . $idTask . ' AND
					t.id_user = u.id';
		$order	= '	t.date_create DESC';

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}



	/**
	 * Get timetracking tab label
	 *
	 * @param	Integer	$idTask
	 * @return	String
	 */
	public static function getTabLabel($idTask) {
		return Label('timetracking.title');
	}



	/**
	 * Get timetracking tab content
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
	 * Save timetracking tab inline form
	 *
	 * @param	Array	$formData
	 */
	public static function saveTabInlineForm($formData) {
		$idTrack	= intval($formData['id']);
		$data		= array('date_update'		=> NOW,
							'date_create'		=> TodoyuTime::parseDate($formData['date_create']),
							'workload_tracked'	=> TodoyuTime::parseTime($formData['workload_tracked']),
							'workload_chargeable'=>TodoyuTime::parseTime($formData['workload_chargeable']),
							'comment'			=> $formData['comment']);

		TodoyuTimetracking::updateTrack($idTrack, $data);
	}



	/**
	 * Modify task data array just before task is rendered
	 * Hook: taskDataBeforeRendering
	 *
	 * @param	Array		$taskData
	 * @param	Integer		$idTask
	 * @return	Array		$taskData
	 */
	public static function modifyTaskRenderData(array $taskData, $idTask) {
		$idTask	= intval($idTask);

		if( TodoyuTimetracking::isTaskRunning($idTask) ) {
			$taskData['task']['class'] .= ' running';
		}

		return $taskData;
	}

}

?>