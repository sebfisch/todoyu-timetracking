<?php

	// Add Javascript init to page body
if( allowed('timetracking', 'general:use') ) {
	TodoyuTimetrackingManager::addTimetrackingJsInitToPage();
}

	// Add headlet if tracking is allowed
if( allowed('timetracking', 'task:track') ) {
//	TodoyuHeadManager::addHeadlet('TodoyuHeadletTimetracking', 0);
}

?>