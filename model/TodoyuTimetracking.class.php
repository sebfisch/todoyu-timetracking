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
 * Timetracking
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */

class TodoyuTimetracking {

	/**
	 * Session key
	 *
	 */
	const SESS_KEY 	= 'timetracking';

	/**
	 * Working table
	 *
	 */
	const TABLE		= 'ext_timetracking_track';




	/**
	 * Get current tracking record
	 *
	 * @return	Array		Or FALSE if no task is tracked
	 */
	private static function getCurrentTracking() {
		$field	= '*';
		$table	= 'ext_timetracking_tracking';
		$where	= 'id_person_create	= ' . TodoyuAuth::getPersonID();
		$order	= 'date_create DESC';

		return Todoyu::db()->getRecordByQuery($field, $table, $where, '', $order);
	}


	private static function setCurrentTracking($idTask) {
		$data	= array(
			'id_task'	=> intval($idTask)
		);

		TodoyuRecordManager::addRecord('ext_timetracking_tracking', $data);
	}

	private static function removeCurrentTracking() {
		$table	= 'ext_timetracking_tracking';
		$where	= 'id_person_create	= ' . TodoyuAuth::getPersonID();

		Todoyu::db()->doDelete($table, $where);
	}


	/**
	 * Get ID of current running task
	 *
	 * @return	ID
	 */
	public static function getTaskID() {
		$record	= self::getCurrentTracking();

		return intval($record['id_task']);
	}



	/**
	 * Get current tracked task
	 *
	 * @return	Task
	 */
	public static function getTask() {
		return TodoyuTaskManager::getTask(self::getTaskID());
	}



	/**
	 * Get data array of current tracked task
	 *
	 * @return	Array
	 */
	public static function getTaskArray() {
		return TodoyuTaskManager::getTaskData(self::getTaskID());
	}



	/**
	 * Get starttime of current timetracking
	 *
	 * @return	Integer
	 */
	public static function getCurrentTrackingStart() {
		$record	= self::getCurrentTracking();

		return intval($record['date_create']);
	}



	/**
	 * Check if task is currently running
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTaskRunning($idTask) {
		$idTask		= intval($idTask);
		$idCurrent	= self::getTaskID();

		return $idTask === $idCurrent;
	}



	/**
	 * Check if timetracking is active
	 *
	 * @return	Boolean
	 */
	public static function isTrackingActive() {
		return self::getTaskID() > 0;
	}



	/**
	 * Start timetracking for a task
	 *
	 * @param	Integer		$idTask
	 */
	public static function startTask($idTask) {
		$idTask	= intval($idTask);

			// Stop current task if one is running
		if( self::isTrackingActive() ) {
			self::stopTask();
		}

		$task 	= TodoyuTaskManager::getTask($idTask);
		$status	= $task->getStatus();

			// Check if current task status allows more timetracking
		if( self::isTrackableStatus($status) ) {
				// Update task status to progess
			TodoyuTaskManager::updateTaskStatus($idTask, STATUS_PROGRESS);
				// Register task as tracked in session
			self::setRunningTask($idTask);
		} else {
				// Return error status
			//echo "NOT TRACKABLE";
		}
	}



	/**
	 * Stop current timetracking
	 *
	 * @return	Boolean		Has a tracking been stopped?
	 */
	public static function stopTask() {
		if( self::isTrackingActive() ) {
			$idTask		= self::getTaskID();
			$dayWorkload= self::getDayWorkloadRecord($idTask, NOW);
			$trackedTime= self::getTrackedTime();

			if( $dayWorkload === false ) {
				self::addTracking($idTask, $trackedTime);
			} else {
				$workload = $dayWorkload['workload_tracked'] + $trackedTime;
				self::updateTracking($dayWorkload['id'], $workload);
			}

			self::removeCurrentTracking();

			return true;
		} else {
			//echo "NOT ACTIVE";
		}

		return false;
	}



	/**
	 * Get time of a specific task between start and end time
	 *
	 * @param	Integer		$idTask:		TaskID
	 * @param	String		$fields:		Fields to be returned
	 * @param	Integer		$starttime: 	Starting timestamp
	 * @param	Integer		$endtime: 		Ending timestamp
	 * @return	Integer		Returns seconds
	 */
	public static function getTrackedTaskTime($idTask = 0, $dateStart = 0, $dateEnd = 0, $checkChargeableTime = false, $idUser = 0, $addCurrentTracking = false) {
		$idTask		= intval($idTask);
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$idUser		= intval($idUser);

		if( $dateEnd === 0 || $dateEnd >= $dateStart ) {
			$dateEnd = NOW;
		}

		$fields	= 'workload_tracked';
		$table	= self::TABLE;

		$where	= '	date_track BETWEEN ' . $dateStart . ' AND ' . $dateEnd	;

		if( $idTask > 0 ) {
			$where .= ' AND id_task	= ' . $idTask;
		}

			// If check is only for a single user, limit result
		if( $idUser !== 0 ) {
			$where .= ' AND id_person_create = ' . $idUser;
		}

			// If check for chargeable time is requested, get this column to
		if( $checkChargeableTime ) {
			$fields .= ', workload_chargeable';
		}


		$tracks	= Todoyu::db()->getArray($fields, $table, $where);
		$time	= 0;

		foreach($tracks as $track) {
			$time += $checkChargeableTime && $track['workload_chargeable'] != 0 ? $track['workload_chargeable'] : $track['workload_tracked'];
		}

			// If task is running, add
		if( $addCurrentTracking && ($idTask === 0 || self::isTaskRunning($idTask)) ) {
			$time += self::getTrackedTime();
		}

		return $time;
	}




	/**
	 * Get total tracked time of a task
	 *
	 * @param	Integer		$idTask				Task ID
	 * @param	Boolean		$checkChargeable	Count chargeable time if available
	 * @return	Integer
	 */
	public static function getTrackedTaskTimeTotal($idTask, $checkChargeable = false, $addCurrentTracking = false) {
		$idTask	= intval($idTask);

		return self::getTrackedTaskTime($idTask, 0, 9999999999, $checkChargeable, 0, $addCurrentTracking);
	}



	/**
	 * Get time the task was running on a specific day
	 *
	 * @param	Integer		$idTask: ID of a task
	 * @param	Integer		$timestamp: timestamp of a specific day. Default will be today
	 * @return	String
	 *
	 */
	public static function getTrackedTaskTimeOfDay($idTask, $timestamp = 0, $idUser = 0) {
		$idTask		= intval($idTask);
		$timestamp	= intval($timestamp);
		$idUser		= intval($idUser);

		if( $timestamp === 0 ) {
			$timestamp = NOW;
		}

		$dayRange= TodoyuTime::getDayRange($timestamp);

		return self::getTrackedTaskTime($idTask, $dayRange['start'], $dayRange['end'], false, $idUser, true);
	}



	/**
	 * Get tracked time of all tasks tracked today by current user
	 *
	 * @return	Integer
	 */
	public static function getTodayTrackedTime() {
		return self::getTrackedTaskTimeOfDay(0, NOW, personid());
	}



	/**
	 * Get all tracks of an user in a date range
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @param	Integer		$idUser
	 * @return	Array
	 */
	public static function getUserTracks($dateStart, $dateEnd, $idUser = 0) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$idUser		= personid($idUser);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= ' id_person_create	= ' . $idUser . ' AND
					date_track BETWEEN ' . $dateStart . ' AND ' . $dateEnd;

		return Todoyu::db()->getArray($fields, $table, $where);
	}



	/**
	 * Get tracked time of current tracking
	 *
	 * @return	Integer
	 */
	public static function getTrackedTime() {
		$startTime	= self::getCurrentTrackingStart();

		return $startTime === 0 ? 0 : NOW - $startTime;
	}



	/**
	 * Get tracking record
	 *
	 * @param	Integer		$idTrack
	 * @return	Array
	 */
	public static function getTrack($idTrack) {
		$idTrack	= intval($idTrack);

		return TodoyuRecordManager::getRecordData(self::TABLE, $idTrack);
	}



	/**
	 * Loads user firstname and lastname of given track
	 *
	 * @param	Integer	$idTrack
	 * @return	Array
	 */
	public static function getTrackUserData($idTrack)	{
		$idTrack= intval($idTrack);

		$fields	= '	u.firstname,
					u.lastname';
		$tables	= 	self::TABLE . ' t,
					ext_contact_person u';
		$where	= '	t.id 				= ' . $idTrack . ' AND
					t.id_person_create 	= u.id';
		$order	= '	t.date_track DESC';

		return Todoyu::db()->getRecordByQuery($fields, $tables, $where, '', $order);
	}



	/**
	 * Add new track record
	 *
	 * @param	Array		$data
	 * @return	Integer		Track record ID
	 */
	public static function addRecord(array $data) {
		$data['date_track']	= NOW;

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update a track record
	 *
	 * @param	Integer		$idTrack
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateRecord($idTrack, array $data) {
		$idTrack = intval($idTrack);

		return TodoyuRecordManager::updateRecord(self::TABLE, $idTrack, $data) === 1;
	}



	/**
	 * Add a new timetracking record to database
	 *
	 * @param	Integer		$idTask				Task ID
	 * @param	Integer		$trackedTime		Tracked seconds
	 * @return	Integer
	 */

	private static function addTracking($idTask, $timeTracked, $timeChargeable = 0) {
		$idTask			= intval($idTask);
		$timeTracked	= intval($timeTracked);
		$timeChargeable	= intval($timeChargeable);

		$data = array(
			'date_track'		=> NOW,
			'id_task'			=> $idTask,
			'workload_tracked'	=> $timeTracked,
			'workload_chargeable'=>$timeChargeable
		);

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update a timetrack record. Happens if a task has been tracked again or the workload has been modified manualy
	 *
	 * @param	Integer		$idRecord
	 * @param	Integer		$workloadTracked
	 * @param	Integer		$chargeableWorkload
	 * @param	String		$comment
	 * @return	Boolean
	 */
	private static function updateTracking($idTrack, $workloadTracked, $chargeableWorkload = null, $comment = null) {
		$idTrack		= intval($idTrack);
		$workloadTracked= intval($workloadTracked);

		$data = array(
			'date_update'		=> NOW,
			'date_track'		=> NOW,
			'workload_tracked'	=> $workloadTracked
		);

		if( !is_null($chargeableWorkload) ) {
			$data['workload_chargeable'] = intval($chargeableWorkload);
		}
		if( !is_null($comment) ) {
			$data['comment'] = $comment;
		}

		return self::updateRecord($idTrack, $data);
	}



	/**
	 * Check if status is allowed for more timetracking
	 *
	 * @param	Integer		$status
	 * @return	Boolean
	 */
	public static function isTrackableStatus($status) {
		return in_array($status, TodoyuArray::assure($GLOBALS['CONFIG']['EXT']['timetracking']['trackableStatus']));
	}



	/**
	 * Check if an item is trackable. At the moment, only task are trackable, but not containers
	 *
	 * @param	Integer		$type
	 * @param	Integer		$status
	 * @return	Bool
	 */
	public static function isTrackable($type, $status) {
		$type	= intval($type);
		$status	= intval($status);

		if( $type === TASK_TYPE_TASK ) {
			return self::isTrackableStatus($status);
		} else {
			return false;
		}
	}



	/**
	 * Save currently tracked task in session
	 *
	 * @param	Integer		$idTask
	 */
	private static function setRunningTask($idTask) {
		$idTask	= intval($idTask);

		self::setCurrentTracking($idTask);
	}



	/**
	 * Get stored workload of a task for a day. Only one record is created for a task per day
	 *
	 * @param	Integer		$idTask		@todo	check use
	 * @param	Integer		$timestamp
	 * @return	Array		Or FALSE of no record found
	 */
	private static function getDayWorkloadRecord($idTask, $timestamp = false) {
		$idTask	= intval($idTask);
		$range	= TodoyuTime::getDayRange($timestamp);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= '	id_person_create	= ' . personid() . ' AND
					id_task			= ' . $idTask . ' AND
					date_track BETWEEN ' . $range['start'] . ' AND ' . $range['end'];

		return Todoyu::db()->getRecordByQuery($fields, $table, $where);
	}



	/**
	 * Get context menu items for a task
	 *
	 * @param	Integer		$idTask		Task ID
	 * @param	Array		$items		Current items
	 * @return	Array
	 */
	public static function getContextMenuItems($idTask, array $items) {
		$idTask	= intval($idTask);
		$task	= TodoyuTaskManager::getTask($idTask);

		if( $task->isTask() && self::isTrackableStatus($task->getStatus()) ) {
			if( self::isTaskRunning($idTask) ) {
				$items['timetrackstop'] = $GLOBALS['CONFIG']['EXT']['timetracking']['ContextMenu']['Task']['timetrackstop'];
			} else {
				$items['timetrackstart'] = $GLOBALS['CONFIG']['EXT']['timetracking']['ContextMenu']['Task']['timetrackstart'];
			}
		}

		return $items;
	}



	/**
	 * Get tracked task IDs
	 *
	 * @param	Integer		$timeStart
	 * @param	Integer		$timeEnd
	 * @param	Integer		$idUser
	 * @return	Array
	 */
	public static function getTrackedTaskIDs($timeStart = 0, $timeEnd = 0, $idUser = 0) {
		$idUser		= personid($idUser);
		$timeStart	= intval($timeStart);
		$timeEnd	= intval($timeEnd);

		if( $timeEnd === 0 ) {
			$timeEnd = NOW;
		}

		$field	= 'id_task';
		$table	= self::TABLE;
		$where	= '	date_update BETWEEN ' . $timeStart . ' AND ' . $timeEnd . ' AND
					id_person_create	= ' . $idUser;
		$group	= 'id_task';
		$order	= 'date_create';

		return Todoyu::db()->getColumn($field, $table, $where, $group, $order);
	}



	/**
	 * Hook. Called when user logs out
	 * If configured, stop tracking
	 *
	 */
	public static function onLogout() {
		$extConf	= TodoyuExtConfManager::getExtConf('timetracking');

			// Check if timetracking stop if configured for logout
		if( intval($extConf['stopOnLogout']) === 1 ) {
				// Check if timetracking is active
			if( self::isTrackingActive() ) {
					// Stop current task (and save tracked time)
				self::stopTask();
			}
		}
	}
}



?>