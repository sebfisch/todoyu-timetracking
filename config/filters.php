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
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Task where an person has tracked time
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['timetrackedPerson'] = array(
	'funcRef'	=> 'TodoyuTimetrackingTaskFilter::Filter_timetrackedPerson',
	'label'		=> 'LLL:timetracking.filter.timetrackedPerson',
	'optgroup'	=> 'LLL:timetracking.search.label',
	'widget'	=> 'textinput',
	'wConf' => array(
		'autocomplete'	=> true,
		'FuncRef'		=> 'TodoyuPersonFilterDataSource::autocompletePersons',
		'FuncParams'	=> array(),
		'LabelFuncRef'	=> 'TodoyuPersonFilterDataSource::getLabel',
		'negation'		=> 'default'
	)
);


Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['timetrackedRoles'] = array(
	'funcRef'	=> 'TodoyuTimetrackingTaskFilter::Filter_timetrackedRoles',
	'label'		=> 'LLL:timetracking.filter.timetrackedRoles',
	'optgroup'	=> 'LLL:timetracking.search.label',
	'widget'	=> 'select',
	'wConf'		=> array(
		'multiple'	=> true,
		'size'		=> 5,
		'FuncRef'	=> 'TodoyuRoleDatasource::getRoleOptions'
	)
);

?>