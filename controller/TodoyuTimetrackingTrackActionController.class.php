<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
 * Timetrack task action controller
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingTrackActionController extends TodoyuActionController {

	/**
	 * Start timetracking for task
	 *
	 * @param	Array		$params
	 */
	public function startAction(array $params) {
		$idTask	= intval($params['task']);

		TodoyuTimetracking::startTask($idTask);

		$task			= TodoyuTaskManager::getTask($idTask);

		$trackedTotal	= TodoyuTimeTracking::getTrackedTaskTime($idTask);
		$trackedToday	= TodoyuTimetracking::getTrackedTaskTimeOfDay($idTask, NOW);

		TodoyuHeader::sendTodoyuHeader('trackedTotal', $trackedTotal);
		TodoyuHeader::sendTodoyuHeader('trackedToday', $trackedToday);

		TodoyuHeader::sendTodoyuHeader('taskData', $task->getTemplateData());
	}



	/**
	 * Stop currently tracked task
	 *
	 * @param	Array		$params
	 */
	public function stopAction(array $params) {
		TodoyuTimetracking::stopTask();
	}

}

?>