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
		$idTask		= intval($formData['id_task']);

		TodoyuTimetrackingTask::saveTabInlineForm($formData);

		return TodoyuTimetrackingRenderer::renderTaskTab($idTask);
	}
}


?>