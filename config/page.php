<?php

	// add JS inits
if( TodoyuAuth::isLoggedIn() ) {
	TodoyuTimetrackingManager::addTimetrackingJsInitToPage();
}

?>