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
 * Timetracking headlet
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuHeadletTimetracking extends TodoyuHeadletTypeOverlay {

	/**
	 * Initialize headlet
	 *
	 */
	protected function init() {
		$this->setJsHeadlet('Todoyu.Ext.timetracking.Headlet.Timetracking');

		TodoyuPage::addExtAssets('timetracking', 'headlet-timetracking');

			// Add active class if tracking is running
		if( TodoyuTimetracking::isTrackingActive() ) {
			$this->addButtonClass('active');
		}

			// Get bar classes and init js object
		$barClassJSON	= self::getBarClassesJSON();
		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.timetracking.Headlet.Timetracking.setBarClasses.bind(Todoyu.Ext.timetracking.Headlet.Timetracking,' . $barClassJSON . ')');
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
	public function getBarClassesJSON() {
		$barClasses			= TodoyuArray::assure($GLOBALS['CONFIG']['EXT']['timetracking']['headletBarClasses']);
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
		$estWorkload	= intval($task->estimated_workload);

		if( $estWorkload > 0 ) {
			$totalTracked		= TodoyuTimetracking::getTrackedTaskTimeTotal($task->id, false, true);
			$data['percent']	= round(($totalTracked/$estWorkload)*100, 0);
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
		$numTasks	= intval($GLOBALS['CONFIG']['EXT']['timetracking']['headletLastTasks']);

		$fields	= '	t.id,
					t.title,
					t.status,
					t.tasknumber,
					t.id_project,
					MAX(tr.date_update) as last_update';
		$tables	= '	ext_project_task t,
					ext_timetracking_track tr';
		$where	= '	t.id	= tr.id_task AND
					tr.id_person_create	= ' . personid();
		$group	= '	t.id';
		$order	= '	last_update DESC';
		$limit	= ' 0,' . $numTasks;

		return Todoyu::db()->getArray($fields, $tables, $where, $group, $order, $limit);
	}



	/**
	 * Get healet label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return 'Zeiterfassung';
	}

}

?>