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

		if( TodoyuTimetracking::isTrackingActive() ) {
			$this->addButtonClass('active');
		}

		$barClassJSON	= self::getBarClassesJSON();
		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.timetracking.Headlet.Timetracking.setBarClasses.bind(Todoyu.Ext.timetracking.Headlet.Timetracking,' . $barClassJSON . ')');
	}


	public function renderOverlayContent() {
		if( TodoyuTimetracking::isTrackingActive() ) {
			return $this->renderOverlayContentActive();
		} else {
			return $this->renderOverlayContentInactive();
		}
	}


	public function getBarClassesJSON() {
		$barClasses			= TodoyuArray::assure($GLOBALS['CONFIG']['EXT']['timetracking']['headletBarClasses']);
		krsort($barClasses);

		return json_encode($barClasses);
	}


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


	private function renderOverlayContentInactive() {
		$tmpl	= 'ext/timetracking/view/headlet-timetracking-inactive.tmpl';
		$data	= array(
			'id'	=> $this->getID(),
			'tasks'	=> $this->getLastTrackedTasks()
		);

//		TodoyuDebug::printLastQueryInFirebug();
//
//		$trackedTasks	= $this->getLastTrackedTasks();
//
//		TodoyuDebug::printHtml($trackedTasks);


		return render($tmpl, $data);
	}


	private function getLastTrackedTasks() {
		$fields	= '	t.id,
					t.title,
					t.status,
					t.tasknumber,
					t.id_project';
		$tables	= '	ext_project_task t,
					ext_timetracking_track tr,
					ext_project_project p';
		$where	= '	t.id	= tr.id_task AND
					tr.id_person_create	= ' . personid();
		$group	= '	t.id';
		$order	= '	tr.date_update DESC';
		$limit	= ' 0,5';

		return Todoyu::db()->getArray($fields, $tables, $where, $group, $order, $limit);
	}


	public static function renderRunningTaskInfo() {
		$task	= TodoyuTimetracking::getTask();

		return $task->getTitle();



				$task	= TodoyuTimetracking::getTask();

			$data['task']			= $task->getTemplateData();

//			$data['idTask']			= $task->id;
//			$data['idProject']		= $task->id_project;
//			$data['label']			= $task->getFullTitle();
//			$data['labelTitle']		= $task->getTitle();
//			$data['labelProject']	= $task->getProject()->getTitle();
//			$data['labelCustomer']	= $task->getProject()->getCompany()->getTitle();
//			$data['labelCustomerShort']	= $task->getProject()->getCompany()->getShortLabel();
//			$data['taskNumber']		= $task->tasknumber;
//			$data['tracked']		= TodoyuTimetracking::getTrackedTaskTimeTotal($task->id);
//			$data['tracking']		= TodoyuTimetracking::getTrackedTime();
//			$data['attributes']		= 'style="display:block"';
//
//				// Get percent of task time
//			$estWorkload	= intval($task->estimated_workload);
//
//			if( $estWorkload > 0 ) {
//				$totalTracked		= TodoyuTimetracking::getTrackedTaskTimeTotal($task->id, false, true);
//				$data['percent']	= round(($totalTracked/$estWorkload)*100, 0);
//				$data['showPercent']= true;
//			}

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