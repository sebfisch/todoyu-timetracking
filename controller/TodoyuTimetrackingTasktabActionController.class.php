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
 * Timetrack tasktab action controller
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingTaskTabActionController extends TodoyuActionController {

	/**
	 * Get task tab content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuTimetrackingRenderer::renderTaskTab($idTask);
	}



	/**
	 * Get tracklist content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function tracklistAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuTimetrackingRenderer::renderTaskTabList($idTask);
	}



	/**
	 * Get tab controll panel content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function controlAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuTimetrackingRenderer::renderTaskTabControl($idTask);
	}



	/**
	 * Get form to edit a task track
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function edittrackAction(array $params) {
		$idTrack= intval($params['track']);

		return TodoyuTimetrackingRenderer::renderTaskTabForm($idTrack);
	}



	/**
	 * Save updated task track data
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updatetrackAction(array $params) {
		$formData	= $params['timetrack'];
		$idTrack	= intval($formData['id']);

		TodoyuTimetrackingTask::saveTabInlineForm($formData);

		return TodoyuTimetrackingRenderer::renderTaskTrack($idTrack);
	}



	/**
	 * Renders track by id
	 * 
	 * @param	Array	$params
	 * @return	String
	 */
	public function trackcontentAction(array $params)	{
		$idTrack	= intval($params['idTrack']);
		
		return TodoyuTimetrackingRenderer::renderTaskTrack($idTrack);
	}
}

?>