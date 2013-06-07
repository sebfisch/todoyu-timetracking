<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * Timetracking project filter
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingProjectFilter {

	/**
	 * Filter for tasks being currently tracked
	 *
	 * @param	Integer		$value
	 * @param	Boolean		$negate
	 * @return	Array					Query parts
	 */
	public static function Filter_isBeingTracked($value, $negate = false) {
		$tables	= array(
			'ext_project_project',
			'ext_project_task',
			'ext_timetracking_active'
		);

		$where		= (!$negate ?
			'	  ext_project_task.id		= ext_timetracking_active.id_task' :
			' NOT ext_project_task.id		= ext_timetracking_active.id_task')
		.   ' AND ext_project_project.id	= ext_project_task.id_project';

		$queryParts	= array(
			'tables'	=> $tables,
			'where'		=> $where
		);

		return $queryParts;
	}

}

?>