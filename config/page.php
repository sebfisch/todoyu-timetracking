<?php

	// add JS inits
if( allowed('timetracking', 'use') ) {
	TodoyuTimetrackingManager::addTimetrackingJsInitToPage();
}

?>