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
 * Task track
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingTrack extends TodoyuBaseObject {

	/**
	 *
	 * @param	Integer		$idTrack
	 */
	public function __construct($idTrack) {
		parent::__construct($idTrack, 'ext_timetracking_track');
	}



	/**
	 * Get tracking person
	 *
	 * @return	TodoyuPerson
	 */
	public function getTrackingPerson() {
		return $this->getPerson('create');
	}



	/**
	 * Get date of tracking
	 *
	 * @return	Integer
	 */
	public function getDateTrack() {
		return intval($this->get('date_track'));
	}



	/**
	 * Get task ID on which was tracked
	 *
	 * @return	Integer
	 */
	public function getTaskID() {
		return intval($this->get('id_task'));
	}



	/**
	 * Get task on which was tracked
	 *
	 * @return	TodoyuTask
	 */
	public function getTask() {
		return TodoyuTaskManager::getTask($this->getTaskID());
	}



	/**
	 * Get amount of tracked workload of track
	 *
	 * @return	Integer
	 */
	public function getWorkloadTracked() {
		return intval($this->get('workload_tracked'));
	}



	/**
	 * Get amount of chargeable workload of track
	 *
	 * @return	Integer
	 */
	public function getWorkloadChargeable() {
		return intval($this->get('workload_chargeable'));
	}



	/**
	 * Get track comment
	 *
	 * @return	Mixed
	 */
	public function getComment() {
		return $this->get('comment');
	}
}

?>