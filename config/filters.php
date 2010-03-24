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