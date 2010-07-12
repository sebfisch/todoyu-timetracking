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
 * Manager for timetracking
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingManager {

	/**
	 * @var	String		Working table
	 */
	const TABLE		= 'ext_timetracking_track';



	/**
	 * Add time tracking specific information to task array
	 *
	 * @param	Array		$taskData		Task data array
	 * @param	Integer		$idTask			Task ID
	 * @param	Integer		$infoLevel		Task infolevel
	 */
	public static function addTimetrackingInfosToTask(array $taskData, $idTask, $infoLevel = 0) {
		$idTask		= intval($idTask);
		$infoLevel	= intval($infoLevel);

		if( TodoyuTimetracking::isTaskRunning($idTask) ) {
			$taskData['class'] .= ' running';
		}

		if( $infoLevel >= 3 ) {
			$task	= TodoyuTaskManager::getTask($idTask);
			$taskData['tracked_time']	= TodoyuTimetracking::getTrackedTaskTime($task->getID(), $task->getStartDate(), $task->getEndDate());
			$taskData['billable_time']	= TodoyuTimetracking::getTrackedTaskTime($task->getID(), $task->getStartDate(), $task->getEndDate(), true);
		}

		return $taskData;
	}



	/**
	 * Add billing time to taskHeaderExtra
	 * Hook: dataModifier
	 *
	 * @param	Array		$extras
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function addTimetrackingHeaderExtrasToTask(array $extras, $idTask) {
		$time	= TodoyuTimeTracking::getTrackedTaskTime($idTask, 0, 0, true);

		$extras['billableTime']	= array(
			'key'		=> 'billingtime',
			'content'	=> TodoyuTime::sec2hour($time)
		);

		return $extras;
	}



	/**
	 * calculates the string given in format hh:mm:ss (hh:mm) in seconds
	 *
	 * @param	String	$string
	 * @return	Integer
	 */
	public static function calculateTrackedTimeFromString($string)	{
		$timeArray = explode(':', $string);

		$time	= is_array($timeArray) ? ($timeArray[0] * 3600 + $timeArray[1] * 60 + $timeArray[2]) : 0;

		return $time;
	}



	/**
	 * Save workload record
	 *
	 * @param	Array $data
	 */
	public static function saveWorkloadRecord(array $data)	{
		Todoyu::db()->doInsert(self::TABLE, $data);
	}



	/**
	 * Get project task info icons
	 *
	 * @param	Array		$icons
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskIcons(array $icons, $idTask) {
		$idTask	= intval($idTask);

		if( self::isTaskOvertimed($idTask) ) {
			$icons['overtimed'] = array(
				'id'		=> 'task-' . $idTask . '-overtimed',
				'class'		=> 'overtimed',
				'label'		=> 'LLL:timetracking.task.attr.overtimed',
				'position'	=> 20
			);
		}

		return $icons;
	}



	/**
	 * Check whether task is over-timed
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTaskOvertimed($idTask) {
		$idTask		= intval($idTask);

		$trackedTime= TodoyuTimetracking::getTrackedTaskTimeTotal($idTask);
		$task		= TodoyuTaskManager::getTask($idTask);

		return $trackedTime > intval($task->get('estimated_workload'));
	}



	/**
	 * Get Estimated workload of given task
	 *
	 * @param	Integer	$idTask
	 * @return	Integer
	 */
	public static function getEstimatedTaskWorkload($idTask = 0) {
		$idTask		= intval($idTask);
		$workload	= 0;

		if( $idTask === 0 && TodoyuTimetracking::isTrackingActive() ) {
			$idTask	= TodoyuTimetracking::getTaskID();
		}

		if( $idTask !== 0 ) {
			$task		= TodoyuTaskManager::getTask($idTask);
			$workload	= intval($task->get('estimated_workload'));
		}

		return $workload;
	}



	/**
	 * Add time tracking JS init to page
	 */
	public static function addTimetrackingJsInitToPage() {
		if( TodoyuTimetracking::isTrackingActive() && ! TodoyuRequest::isAjaxRequest() ) {
			$idTask			= TodoyuTimetracking::getTaskID();
			$taskData		= TodoyuTimetracking::getTask()->getTemplateData();
			$trackedTime	= TodoyuTimeTracking::getTrackedTaskTime($idTask);
			$trackingTime	= TodoyuTimetracking::getTrackedTime();
			$estimatedTime	= self::getEstimatedTaskWorkload($idTask);

			$init	= 'Todoyu.Ext.timetracking.initWithTask.bind(Todoyu.Ext.timetracking, ' . json_encode($taskData) . ', ' . $trackedTime . ', ' . $trackingTime . ', ' . $estimatedTime . ')';
		} else {
			$init	= 'Todoyu.Ext.timetracking.initWithoutTask.bind(Todoyu.Ext.timetracking)';
		}

		TodoyuPage::addJsOnloadedFunction($init, 100);
	}



	/**
	 * Formhook
	 * Add time tracking fields to quick task
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idTask
	 */
	public static function addWorkloadFieldToQuicktask(TodoyuForm $form, $idTask) {
		$xmlPath	= 'ext/timetracking/config/form/quicktask-tracked.xml';
		$insertForm	= TodoyuFormManager::getForm($xmlPath);

		$workloadDone	= $insertForm->getField('workload_done');
		$startTracking	= $insertForm->getField('start_tracking');

		$form->getFieldset('main')->addField('workload_done', $workloadDone, 'after:id_worktype');
		$form->getFieldset('main')->addField('start_tracking', $startTracking, 'after:workload_done');
	}



	/**
	 * Formhook: Handle (save) special fields added to quick task by time tracking
	 *
	 * @param	Array		$data
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function handleQuicktaskFormSave(array $data, $idTask) {
		$idTask			= intval($idTask);
		$workloadDone	= intval($data['workload_done']);
		
			// Save already done workload
		if( $workloadDone > 0 ) {
			self::addTrackedWorkload($idTask, $workloadDone);
		}
		unset($data['workload_done']);

			// 'Start tracking' checked? set status accordingly
		if( intval($data['start_tracking']) === 1 ) {
			$data['status'] = STATUS_PROGRESS;
		}
		unset($data['start_tracking']);

		return $data;
	}



	/**
	 * Add already tracked (seconds of) workload to workload record of given task.
	 *
	 * @param	Integer	$idTask
	 * @param	Integer	$workload
	 */
	protected static function addTrackedWorkload($idTask, $workload) {
		$idTask		= intval($idTask);
		$workload	= intval($workload);

		$data	= array(
			'id_person_create'	=> TodoyuAuth::getPersonID(),
			'id_task'			=> $idTask,
			'date_create'		=> NOW,
			'date_update'		=> NOW,
			'date_track'		=> NOW,
			'workload_tracked'	=> $workload
		);

		self::saveWorkloadRecord($data);
	}



	/**
	 * Hook when quick task is saved
	 * Check whether the option 'start tracking' was checked when saving
	 * Start tracking on server and send tracking header
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idProject
	 * @param	Array		$data
	 */
	public static function hookQuickTaskSaved($idTask, $idProject, array $data) {
		if( intval($data['start_tracking']) === 1 ) {
			TodoyuTimetracking::startTask($idTask);

			TodoyuHeader::sendTodoyuHeader('startTracking', 1);
		}
	}

}

?>