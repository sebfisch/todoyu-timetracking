<?php

class TodoyuTimetrackingTaskTabActionController extends TodoyuActionController {
	
	public function updateAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuTimetrackingRenderer::renderTaskTab($idTask);
	}
	
	
	public function tracklistAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuTimetrackingRenderer::renderTaskTabList($idTask);
	}
	
	public function controlAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuTimetrackingRenderer::renderTaskTabControl($idTask);
	}
	
	public function edittrackAction(array $params) {
		$idTrack= intval($params['track']);

		return TodoyuTimetrackingRenderer::renderTaskTabForm($idTrack);
	}
	
	public function updatetrackAction(array $params) {
		$formData	= $params['timetrack'];
		$idTask		= intval($formData['id_task']);
		
		TodoyuTimetrackingTask::saveTabInlineForm($formData);

		return TodoyuTimetrackingRenderer::renderTaskTab($idTask);
	}
}


?>