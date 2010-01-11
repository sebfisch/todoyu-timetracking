<?php

	// add JS inits
if( allowed('timetracking', 'general:use') ) {
	TodoyuTimetrackingManager::addTimetrackingJsInitToPage();
}

?>