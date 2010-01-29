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
 * Manager for timetracking
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingManager {

	/**
	 * Working table
	 *
	 */
	const TABLE		= 'ext_timetracking_track';



	/**
	 * Add timetracking specific information to task array
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


	public static function XXXaddTrackedWorkloadToTask($date, $idTask, $workloadTracked, $workloadChargeable = 0, $comment = '', $idUser = 0) {
		$date				= intval($date);
		$idTask				= intval($idTask);
		$workloadTracked	= intval($workloadTracked);
		$workloadChargeable	= intval($workloadChargeable);

		$trackedTime		= TodoyuTimetracking::getTrackedTaskTimeOfDay($idTask, $date, $idUser);

		if( $trackedTime === 0 ) {
			$data	= array(
				'date_update'		=> NOW,
				'date_create'		=> NOW,
				'id_user_create'	=> userid($idUser),
				'id_task'			=> $idTask,
				'workload_tracked'	=> $workloadTracked,
				'workload_chargeable'=>$workloadChargeable,
				'comment'			=> $comment
			);

			Todoyu::db()->addRecord(self::TABLE, $data);
		} else {

		}
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
				'label'		=> 'LLL:task.attr.overtimed',
				'position'	=> 20
			);
		}

		return $icons;
	}



	/**
	 * Check whether task is overtimed
	 *
	 * @param	Integer	$idTask
	 * @return	Boolean
	 */
	public static function isTaskOvertimed($idTask) {
		$idTask		= intval($idTask);

		$trackedTime= TodoyuTimetracking::getTrackedTaskTimeTotal($idTask);
		$task		= TodoyuTaskManager::getTask($idTask);

		return $trackedTime > intval($task->get('estimated_workload'));
	}



	/**
	 * Get Estimate
	 *
	 * @param unknown_type $idTask
	 * @return unknown
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
	 * Add timetracking JS init to page
	 *
	 */
	public static function addTimetrackingJsInitToPage() {
		if( TodoyuTimetracking::isTrackingActive() && ! TodoyuRequest::isAjaxRequest() ) {
			$idTask	= TodoyuTimetracking::getTaskID();
			$time	= TodoyuTimetracking::getTrackedTime();
			$estWork= self::getEstimatedTaskWorkload($idTask);

			$init	= 'Todoyu.Ext.timetracking.initWithTask.bind(Todoyu.Ext.timetracking, ' . $idTask . ', ' . $time . ', ' . $estWork . ')';
		} else {
			$init	= 'Todoyu.Ext.timetracking.init.bind(Todoyu.Ext.timetracking)';
		}

		TodoyuPage::addJsOnloadedFunction($init);
	}



	/**
	 * Calculates the billable time from the tracked time
	 *
	 * @todo	RIGHT PLACE FOR THIS FUNCTION?
	 * @param	Integer	$trackedWorkload
	 */
	public static function calculateBillableTime($trackedWorkload)	{
		$trackedWorkload		= intval($trackedWorkload);
		$fifteenMinutesInSeconds= 900;

		return ceil( $trackedWorkload / $fifteenMinutesInSeconds) * $fifteenMinutesInSeconds;
	}




	/**
	 * Formhook
	 * Add timetracking fields to quicktask
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

		$form->getField('task_done')->setAttribute('onchange', 'Todoyu.Ext.project.QuickTask.preventStartDone(\'done\', this)');
	}



	/**
	 * Formhook
	 * Handle (save) special fields added to quicktask by timetracking
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
	 *	@param	Integer	$idTask
	 *	@param	Integer	$workload
	 */
	protected static function addTrackedWorkload($idTask, $workload) {
		$idTask		= intval($idTask);
		$workload	= intval($workload);

		$data	= array(
			'id_user_create'	=> TodoyuAuth::getUserID(),
			'id_task'			=> $idTask,
			'date_create'		=> NOW,
			'date_update'		=> NOW,
			'date_track'		=> NOW,
			'workload_tracked'	=> $workload
		);

		self::saveWorkloadRecord($data);
	}

}

?>