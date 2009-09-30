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
 * Presets for timetracking context menus
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */

	// Add menu items to the task context menu
$CONFIG['EXT']['timetracking']['ContextMenu']['Task'] = array(
	'timetrackstart'	=> array(
		'key'		=> 'timetrackstart',
		'label'		=> 'LLL:timetracking.startTimetracking',
		'jsAction'	=> 'Todoyu.Ext.timetracking.Task.start(#ID#)',
		'class'		=> 'task-ctxmenu task-timetrackstart',
		'position'	=> 99
	),
	'timetrackstop'	=> array(
		'key'		=> 'timetrackstop',
		'label'		=> 'LLL:timetracking.stopTimetracking',
		'jsAction'	=> 'Todoyu.Ext.timetracking.Task.stop(#ID#)',
		'class'		=> 'task-ctxmenu task-timetrackstop',
		'position'	=> 99
	)
);

?>