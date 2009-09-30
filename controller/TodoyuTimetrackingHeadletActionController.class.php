<?php

class TodoyuTimetrackingHeadletActionController extends TodoyuActionController {
	
	public function updateAction(array $params) {
		$headlet	= new TodoyuHeadletTimetracking();

		return $headlet->render();
	}

}


?>