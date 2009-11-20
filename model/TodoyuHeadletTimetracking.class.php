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

class TodoyuHeadletTimetracking extends TodoyuHeadlet {

	/**
	 * Initialize headlet
	 *
	 */
	protected function init() {
		$this->setTemplate('ext/timetracking/view/headlet-timetracking.tmpl');

		TodoyuPage::addExtAssets('timetracking', 'headlet-timetracking');

		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.timetracking.Headlet.Timetracking.init.bind(Todoyu.Ext.timetracking.Headlet.Timetracking)');

		$this->setupData();
	}



	/**
	 * Set headlet data
	 *
	 */
	private function setupData()  {
		$data	= array();

		if( TodoyuTimetracking::isTrackingActive() ) {
			$task	= TodoyuTimetracking::getTask();



			$data['idTask']		= $task->id;
			$data['idProject']	= $task->id_project;
			$data['label']		= $task->getFullTitle();
			$data['tracked']	= TodoyuTimetracking::getTrackedTaskTimeTotal($task->id);
			$data['tracking']	= TodoyuTimetracking::getTrackedTime();
			$data['attributes']	= 'style="display:block"';

				// Get percent of task time
			$estWorkload	= intval($task->get('estimated_workload'));

			if( $estWorkload > 0 ) {
				$totalTracked		= TodoyuTimetracking::getTrackedTaskTimeTotal($task->id, false, true);
				$data['percent']	= round(($totalTracked/$estWorkload)*100, 0);
				$data['showPercent']= true;
			}

		} else {
			$data['attributes']	= 'style="display:none"';
		}

		$this->setData($data);
	}



	/**
	 * Render headlet
	 *
	 * @return	String
	 */
	public function render() {
		return parent::render();
	}

}

?>