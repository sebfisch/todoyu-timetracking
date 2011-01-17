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
 * Timetracking headlet
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuHeadletTimetracking extends TodoyuHeadletTypeOverlay {

	/**
	 * Initialize headlet
	 */
	protected function init() {
		$this->setJsHeadlet('Todoyu.Ext.timetracking.Headlet.Timetracking');

			// Add active class if tracking is running
		if( TodoyuTimetracking::isTrackingActive() ) {
			$this->addButtonClass('tracking');
		}

			// Get bar classes and init JS object
		$barClassJSON	= self::getBarClassesJSON();
		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.timetracking.Headlet.Timetracking.setBarClasses.bind(Todoyu.Ext.timetracking.Headlet.Timetracking,' . $barClassJSON . ')', 160);
	}



	/**
	 * Render content for overlay box
	 *
	 * @return	String
	 */
	public function renderOverlayContent() {
		if( TodoyuTimetracking::isTrackingActive() ) {
			return $this->renderOverlayContentActive();
		} else {
			return $this->renderOverlayContentInactive();
		}
	}



	/**
	 * Get progress bar classes as json array
	 *
	 * @return	String
	 */
	public static function getBarClassesJSON() {
		$barClasses			= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['timetracking']['headletBarClasses']);
		krsort($barClasses);

		return json_encode($barClasses);
	}



	/**
	 * Render overlay content for active timetracking
	 *
	 * @return	String
	 */
	private function renderOverlayContentActive() {
		$task	= TodoyuTimetracking::getTask();

		$tmpl	= 'ext/timetracking/view/headlet-timetracking-active.tmpl';
		$data	= array(
			'id'		=> $this->getID(),
			'task'		=> $task->getTemplateData(2),
			'tracked'	=> TodoyuTimetracking::getTrackedTaskTimeTotal($task->id),
			'tracking'	=> TodoyuTimetracking::getTrackedTime()
		);


			// Get percent of task time
		if( $task->hasEstimatedWorkload() ) {
			$totalTracked		= TodoyuTimetracking::getTrackedTaskTimeTotal($task->id, false, true);
			$data['percent']	= round(($totalTracked/$task->getEstimatedWorkload())*100, 0);
			$data['showPercent']= true;
		}

		return render($tmpl, $data);
	}



	/**
	 * Render overlay content for inactive timetracking
	 *
	 * @return	String
	 */
	private function renderOverlayContentInactive() {
		$tmpl	= 'ext/timetracking/view/headlet-timetracking-inactive.tmpl';

		$data	= array(
			'id'	=> $this->getID(),
			'tasks'	=> $this->getLastTrackedTasks()
		);

		return render($tmpl, $data);
	}



	/**
	 * Get the last tracked tasks for the inactive box
	 *
	 * @return	Array
	 */
	private function getLastTrackedTasks() {
		$numTasks	= intval(Todoyu::$CONFIG['EXT']['timetracking']['headletLastTasks']);

		$query	= '	SELECT
						track.date_track,
						MAX(track.date_track) as last_update,
						SUM(track.workload_tracked) as trackedtime,
						task.id,
						task.id_project,
						task.tasknumber,
						task.title,
						task.tasknumber,
						task.type,
						task.status,
						project.title as projecttitle
					FROM
						`ext_timetracking_track` track
					LEFT JOIN
						`ext_project_task` task
							ON track.id_task = task.id
					LEFT JOIN
						`ext_project_project` project
							ON task.id_project = project.id
					WHERE
						track.id_person_create	= ' . personid() . ' AND
						task.type				= ' . TASK_TYPE_TASK . ' AND
						task.deleted			= 0 AND
						track.date_track		<= ' . NOW . '
					GROUP BY
						track.id_task
					ORDER BY
						last_update DESC
					LIMIT
						0,' . $numTasks;

		$resource	= Todoyu::db()->query($query);
		$tasks		= Todoyu::db()->resourceToArray($resource);

		foreach($tasks as $index => $task)	{
			$tasks[$index]['isTrackable'] = TodoyuTimetracking::isTrackable(TASK_TYPE_TASK, $task['status'], $task['id']);

			if(intval($tasks[$index]['id']) === 0)	{
				unset($task[$index]);
			}
		}

		return $tasks;
	}



	/**
	 * Get healet label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Label('timetracking.headlet.label');
	}

}

?>