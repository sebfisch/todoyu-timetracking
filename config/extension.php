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
 * Trackable task status
 */
Todoyu::$CONFIG['EXT']['timetracking']['trackableStatus'] = array(
	STATUS_PLANNING,
	STATUS_OPEN,
	STATUS_PROGRESS,
	STATUS_CONFIRM,
//	STATUS_DONE,
	STATUS_ACCEPTED,
	STATUS_REJECTED,
	STATUS_WARRANTY,
	STATUS_CUSTOMER
);


if( allowed('timetracking', 'general:use') ) {
		// Register tab for task
	TodoyuTaskManager::addTaskTab('timetracking', 'TodoyuTimetrackingTask::getTabLabel', 'TodoyuTimetrackingTask::getTabContent', 10);

	if( allowed('timetracking', 'task:track') ) {
			// Register context menu function for task
		TodoyuContextMenuManager::registerFunction('task', 'TodoyuTimetracking::getContextMenuItems', 100);
		TodoyuHookManager::registerHook('core', 'logout', 'TodoyuTimetracking::onLogout');
	}
}


Todoyu::$CONFIG['EXT']['timetracking']['headletBarClasses'] = array(
	0	=> 'green',
	80	=> 'yellow',
	90	=> 'orange',
	100	=> 'red'
);


Todoyu::$CONFIG['EXT']['timetracking']['headletLastTasks']	= 5;



?>