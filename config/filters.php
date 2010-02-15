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
 * Task where an user has tracked time
 */
$CONFIG['FILTERS']['TASK']['widgets']['timetrackedUser'] = array(
	'funcRef'	=> 'TodoyuTimetrackingTaskFilter::Filter_timetrackedUser',
	'label'		=> 'LLL:timetracking.filter.timetrackedUser',
	'optgroup'	=> 'LLL:timetracking.search.label',
	'widget'	=> 'textinput',
	'wConf' => array(
		'autocomplete'	=> true,
		'FuncRef'		=> 'TodoyuPersonFilterDataSource::autocompleteUsers',
		'FuncParams'	=> array(),
		'LabelFuncRef'	=> 'TodoyuPersonFilterDataSource::getLabel',
		'negation'		=> 'default'
	)
);


$CONFIG['FILTERS']['TASK']['widgets']['timetrackedGroups'] = array(
	'funcRef'	=> 'TodoyuTimetrackingTaskFilter::Filter_timetrackedGroups',
	'label'		=> 'LLL:timetracking.filter.timetrackedGroups',
	'optgroup'	=> 'LLL:timetracking.search.label',
	'widget'	=> 'select',
	'wConf'		=> array(
		'multiple'	=> true,
		'size'		=> 5,
		'FuncRef'	=> 'TodoyuTaskFilterDataSource::getUsergroupOptions'
	)
);



?>