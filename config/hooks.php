<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
	TodoyuHookManager::registerHook('project', 'taskIcons', 'TodoyuTimetrackingManager::getTaskIcons');

	TodoyuHookManager::registerHook('project', 'taskinfo', 'TodoyuTimetrackingManager::addTimetrackingInfosToTask');

	TodoyuHookManager::registerHook('project', 'taskHeaderExtras', 'TodoyuTimetrackingManager::addTimetrackingHeaderExtrasToTask');

		// Quicktask: add timetracking fields
	TodoyuFormHook::registerBuildForm('ext/project/config/form/quicktask.xml', 'TodoyuTimetrackingManager::addWorkloadFieldToQuicktask');
		// Quicktask: Save timetracking fields
	TodoyuFormHook::registerSaveData('ext/project/config/form/quicktask.xml', 'TodoyuTimetrackingManager::handleQuicktaskFormSave');

		// Quicktask: Saved hook
	TodoyuHookManager::registerHook('project', 'QuickTaskSaved', 'TodoyuTimetrackingManager::hookQuickTaskSaved');
}

?>