<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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
	 * Add timetracking infos to task info data. -More time tracked than estimated? add marking CSS class
	 *
	 * @param	Array		$taskInfos
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function addTimetrackingInfosToTaskInfos(array $taskInfos, $idTask) {
		$idTask	= intval($idTask);

		if( self::isTaskOvertimed($idTask) ) {
			$taskInfos['estimated_workload']['className'] .= ' overtimed';
		}

		return $taskInfos;
	}



	/**
	 * Add billable time to taskHeaderExtra
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
	 * Calculates the string given in format hh:mm:ss (hh:mm) in seconds
	 *
	 * @param	String	$string
	 * @return	Integer
	 */
	public static function calculateTrackedTimeFromString($string)	{
		$timeArray = explode(':', $string);

		return is_array($timeArray) ? ($timeArray[0] * TodoyuTime::SECONDS_HOUR + $timeArray[1] * TodoyuTime::SECONDS_MIN + $timeArray[2]) : 0;
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

		return $trackedTime > $task->getEstimatedWorkload();
	}



	/**
	 * Add time tracking JS init to page
	 */
	public static function addTimetrackingJsInitToPage() {
		if( TodoyuTimetracking::isTrackingActive() && ! TodoyuRequest::isAjaxRequest() ) {
			$idTask			= TodoyuTimetracking::getTaskID();
			$taskData		= TodoyuTimetracking::getTask()->getTemplateData();
			$trackedTotal	= TodoyuTimeTracking::getTrackedTaskTime($idTask);
			$trackedToday	= TodoyuTimetracking::getTrackedTaskTimeOfDay($idTask, NOW, personid());
			$trackedCurrent	= TodoyuTimetracking::getTrackedTime();

			$init	= 'Todoyu.Ext.timetracking.initWithTask.bind(Todoyu.Ext.timetracking, ' . json_encode($taskData) . ', ' . $trackedTotal . ', ' . $trackedToday . ', ' . $trackedCurrent . ')';
		} else {
			$init	= 'Todoyu.Ext.timetracking.initWithoutTask.bind(Todoyu.Ext.timetracking)';
		}

		TodoyuPage::addJsOnloadedFunction($init, 100);
	}



	/**
	 * Formhook
	 * Add time tracking fields to quickTask
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
	 * Set task default data: check whether quickTask preset contains start_timetracking
	 *
	 * @param	Array	$data
	 * @return	Array
	 */
	public static function setTaskDefaultData($data) {
		$idProject	= intval($data['id_project']);

		if( $idProject > 0 ) {
			$project	= TodoyuProjectManager::getProject($idProject);

				// Get presets from taskpreset set (if assigned) or extension config
			$idTaskpreset	= $project->getTaskpresetID();
			if( intval($idTaskpreset) > 0 ) {
				$presets	= TodoyuTaskpresetManager::getTaskpresetData($idTaskpreset);
				$presets['title']	= $presets['tasktitle'];
			} else {
				$presets	= TodoyuExtConfManager::getExtConf('project');
			}

			$data['start_tracking']	= intval($presets['start_timetracking']);
		}

		return $data;
	}



	/**
	 * Formhook: Handle (save) special fields added to quickTask by time tracking
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



	/**
	 * Remove form field if the user only can edit the chargeable time
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idTrack
	 */
	public static function hookModifyTrackFields(TodoyuForm $form, $idTrack) {
		$idTrack	= intval($idTrack);

		if( TodoyuAuth::isAdmin() ) {
			return false;
		}

		if( $idTrack !== 0 ) {
			$track	= TodoyuTimetracking::getTrack($idTrack);

			if( allowed('timetracking', 'task:editAllChargeable') && ! $track->isCurrentPersonCreator() ) {
				$form->removeField('date_track', true);
				$form->removeField('workload_tracked', true);
				$form->removeField('comment', true);
			}
		}
	}



	/**
	 * Check whether a track is editable for the current person
	 *
	 * @param	Integer		$idTrack
	 * @param	Array		$trackData
	 * @return	Boolean
	 */
	public static function isTrackEditable($idTrack, array $trackData = null) {
		$idTrack	= intval($idTrack);

		if( is_null($trackData) ) {
			$trackData	= TodoyuTimetracking::getTrackData($idTrack);
		}

		$idTask	= intval($trackData['id_task']);
		$task	= TodoyuTaskManager::getTask($idTask);

			// Locked overrules admin right
		if( $task->isLocked() ) {
			return false;
		}

			// If not locked, admin can edit the track
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

			// Check rights and ownership
		if( ($trackData['id_person_create'] == personid() && allowed('timetracking','task:editOwn'))
			|| allowed('timetracking', 'task:editAllChargeable') || allowed('timetracking','task:editAll')
		) {
			return true;
		}

		return false;
	}


	/**
	 * Callback to render content for all requested task tabs
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$info		List of task IDs to render
	 * @return	Array		Content of task tab for requested tasks
	 */
	public static function callbackTaskTab($idTask, array $info) {
		$taskIDs	= TodoyuArray::intval($info);
		$response	= array();

		foreach($taskIDs as $idTask) {
			$response[$idTask] = TodoyuTimetrackingRenderer::renderTaskTab($idTask);
		}

		return $response;
	}



	/**
	 * Callback to render the content for the tracking headlet
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$info		Don't care
	 * @return	String		Content of the headlet
	 */
	public static function callbackHeadletOverlayContent($idTask, $info) {
		$headlet	= new TodoyuHeadletTimetracking();

		return $headlet->renderOverlayContent();
	}



	/**
	 * Add to attributes array of project preset data list
	 *
	 * @param	Integer		$idProject
	 * @return	Array
	 */
	public static function getProjectPresetDataAttributes($idProject) {
		$idProject	= intval($idProject);
		$info		= array();

		$project		= TodoyuProjectManager::getProject($idProject);
		$idTaskPreset	= $project->get('id_taskpreset');

		if( $idTaskPreset > 0 ) {
			$taskPreset	= TodoyuTaskpresetManager::getTaskpresetData($idTaskPreset);

				// Taskpreset set title
			$info[]	= array(
				'label'		=> Label('timetracking.taskpreset.start_tracking'),
				'value'		=> intval($taskPreset['start_tracking']) ? Label('core.yes') : Label('core.no'),
				'position'	=> 80
			);
		}

		return $info;
	}

}

?>