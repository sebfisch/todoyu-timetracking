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
 * Renderer for timetracking extension
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */

class TodoyuTimetrackingRenderer {

	/**
	 * Render timetracking tab in task when tab is active
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTaskTab($idTask) {
		$idTask		= intval($idTask);
		$control	= TodoyuTimetrackingRenderer::renderTaskTabControl($idTask);
		$list		= TodoyuTimetrackingRenderer::renderTaskTabList($idTask);

		$tmpl	= 'ext/timetracking/view/tasktab.tmpl';
		$data	= array(
			'idTask'	=> $idTask,
			'control'	=> $control,
			'list'		=> $list
		);

		return render($tmpl, $data);
	}



	/**
	 * Render controll box in the task tab
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTaskTabControl($idTask) {
		$idTask	= intval($idTask);
		$task	= TodoyuTaskManager::getTask($idTask);

		$tmpl	= 'ext/timetracking/view/tasktab-control.tmpl';
		$data	= array(
			'idTask' => $idTask,
			'totalTrackedTime'	=> TodoyuTimetracking::getTrackedTaskTimeTotal($idTask),
			'trackable'			=> TodoyuTimetracking::isTrackableStatus($task->getStatus())
		);

		if( TodoyuTimetracking::isTaskRunning($idTask) ) {
			$data['buttonLabel']	= 'LLL:core.stop';
			$data['function']		= 'stop';
			$data['class']			= 'stopTracking';
			$data['running']		= true;
			$data['trackedTime']	= TodoyuTimetracking::getTrackedTime();
		} else {
			$data['buttonLabel']	= 'LLL:core.start';
			$data['function']		= 'start';
			$data['class']			= 'startTracking';
			$data['running']		= false;
			$data['trackedTime']	= 0;
		}

		return render($tmpl, $data);
	}



	/**
	 * Render track list in task tab
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTaskTabList($idTask) {
		$idTask	= intval($idTask);
		$tracks	= TodoyuTimetrackingTask::getTaskTracks($idTask);

		$tmpl	= 'ext/timetracking/view/tasktab-list.tmpl';
		$data	= array(
			'idTask'	=> $idTask,
			'tracks'	=> $tracks
		);

		return render($tmpl, $data);
	}



	/**
	 * Render form to edit a track in task tab
	 *
	 * @param	Integer		$idTrack
	 * @return	String
	 */
	public static function renderTaskTabForm($idTrack) {
		$idTrack	= intval($idTrack);

			// Construct form object
		$xmlPath	= PATH_EXT_TIMETRACKING . '/config/form/track.xml';
		$form		= TodoyuFormManager::getForm($xmlPath, $idTrack);

			// Load form data
		$formData	= TodoyuTimetracking::getTrack($idTrack);
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $idTrack);

			// Set form data
		$form->setFormData($formData);
		$form->setRecordID($idTrack);

			// Render
		return $form->render();
	}



	/**
	 *
	 * @param	Integer	$idTrack
	 * @return	String
	 */
	public static function renderTaskTrack($idTrack)	{
		$idTrack	= intval($idTrack);

		$tmpl	= 'ext/timetracking/view/tasktab-track.tmpl';

		$data = array(
			'track'		=> array_merge(
				TodoyuTimetracking::getTrack($idTrack),
				TodoyuTimetracking::getTrackPersonData($idTrack)
			)
		);

		return render($tmpl, $data);
	}

}

?>