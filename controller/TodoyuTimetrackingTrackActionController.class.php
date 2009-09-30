<?php

class TodoyuTimetrackingTrackActionController extends TodoyuActionController {
	
	public function startAction(array $params) {
		$idTask	= intval($params['task']);
		
		TodoyuTimetracking::startTask($idTask);
	}
	
	
	public function stopAction(array $params) {
		TodoyuTimetracking::stopTask();
	}
	
}


?>