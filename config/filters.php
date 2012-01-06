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
 * Task where a person has tracked time
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['timetrackedPerson'] = array(
	'funcRef'	=> 'TodoyuTimetrackingTaskFilter::Filter_timetrackedPerson',
	'label'		=> 'timetracking.ext.filter.timetrackedPerson',
	'optgroup'	=> 'timetracking.ext.search.label',
	'widget'	=> 'text',
	'require'	=> 'timetracking.general:use',
	'wConf' => array(
		'autocomplete'	=> true,
		'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
		'FuncParams'	=> array(),
		'LabelFuncRef'	=> 'TodoyuContactPersonFilterDataSource::getLabel',
		'negation'		=> 'default'
	)
);


Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['timetrackedRoles'] = array(
	'funcRef'	=> 'TodoyuTimetrackingTaskFilter::Filter_timetrackedRoles',
	'label'		=> 'timetracking.ext.filter.timetrackedRoles',
	'optgroup'	=> 'timetracking.ext.search.label',
	'widget'	=> 'select',
	'require'	=> 'timetracking.general:use',
	'wConf'		=> array(
		'multiple'	=> true,
		'size'		=> 5,
		'FuncRef'	=> 'TodoyuRoleDatasource::getRoleOptions'
	)
);

?>