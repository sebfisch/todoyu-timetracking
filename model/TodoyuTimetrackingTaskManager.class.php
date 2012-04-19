<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
class TodoyuTimetrackingTaskManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_timetracking_track';



	/**
	 * Get Timetracking Task
	 *
	 * @param	Integer		$idTask
	 * @return TodoyuTimetrackingTask
	 */
	public static function getTask($idTask) {
		$idTask	= intval($idTask);

		return new TodoyuTimetrackingTask($idTask);
	}



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
		$tables	=	self::TABLE . ' t,
					ext_contact_person u';
		$where	= '		t.id_task			= ' . $idTask .
				  ' AND	t.id_person_create	= u.id';
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
		return Todoyu::Label('timetracking.ext.title');
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
	 * @param	Array	$data
	 */
	public static function updateTrack(array $data) {
		$idTrack	= intval($data['id']);

		TodoyuTimetracking::updateRecord($idTrack, $data);
	}



	/**
	 * Set task default data: check whether quickTask preset contains start_timetracking
	 *
	 * @param	Array		$data
	 * @param	Integer		$type
	 * @param	Integer		$idProject
	 * @param	Integer		$idParentTask
	 * @param	Boolean		$isQuickTask
	 * @return	Array
	 */
	public static function hookTaskDefaultData($data, $type, $idProject, $idParentTask, $isQuickTask) {
		$type			= intval($type);
		$idProject		= intval($idProject);
		$idParentTask	= intval($idParentTask);

		if( $idProject === 0 ) {
			$idProject = intval($data['id_project']);
		}

		if( $idProject !== 0 && $isQuickTask ) {
			$project	= TodoyuTimetrackingProjectManager::getProject($idProject);

			if( $project->hasTaskPreset() ) {
				$taskPreset	= $project->getTaskPreset();
				$presetData	= $taskPreset->getTimetrackingPresetData();
				$data		= array_merge($data, $presetData);
			}
		}

		return $data;
	}

}

?>