<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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

/**
 * Presets for timetracking context menus
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */

	// Add menu items to the task context menu
Todoyu::$CONFIG['EXT']['timetracking']['ContextMenu']['Task'] = array(
	'timetrackstart'	=> array(
		'key'		=> 'timetrackstart',
		'label'		=> 'timetracking.start',
		'jsAction'	=> 'Todoyu.Ext.timetracking.Task.start(#ID#)',
		'class'		=> 'task-ctxmenu task-timetrackstart',
		'position'	=> 99
	),
	'timetrackstop'	=> array(
		'key'		=> 'timetrackstop',
		'label'		=> 'timetracking.stop',
		'jsAction'	=> 'Todoyu.Ext.timetracking.Task.stop(#ID#)',
		'class'		=> 'task-ctxmenu task-timetrackstop',
		'position'	=> 99
	)
);

?>