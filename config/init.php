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

if( allowed('timetracking', 'general:use') ) {
	require_once( PATH_EXT_TIMETRACKING . '/config/hooks.php');
}

/**
 * Trackable task status
 */
Todoyu::$CONFIG['EXT']['timetracking']['trackableStatus'] = array(
	STATUS_OPEN,
	STATUS_PROGRESS,
	STATUS_CONFIRM
);


if( allowed('timetracking', 'general:use') ) {
		// Register tab for task
	TodoyuTaskManager::addTaskTab('timetracking', 'TodoyuTimetrackingTaskManager::getTabLabel', 'TodoyuTimetrackingTaskManager::getTabContent', 10);

	if( allowed('timetracking', 'task:track') ) {
			// Register context menu function for task
		TodoyuContextMenuManager::addFunction('task', 'TodoyuTimetracking::getContextMenuItems', 100);
		TodoyuHookManager::registerHook('core', 'logout', 'TodoyuTimetracking::onLogout');
	}
}


Todoyu::$CONFIG['EXT']['timetracking']['headletBarClasses'] = array(
	0	=> '#cadb98', // green
	80	=> 'yellow',
	90	=> 'orange',
	100	=> 'red',
);


Todoyu::$CONFIG['EXT']['timetracking']['headletLastTasks']	= 5;

?>