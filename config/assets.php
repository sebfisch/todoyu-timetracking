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
 * Assets (JS, CSS, SWF, etc.) requirements for timetracking extension
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */




$CONFIG['EXT']['timetracking']['assets'] = array(
		// default: loaded all over the installation always
	'default' => array(
		'js' => array(
			array(
				'file'		=> 'ext/timetracking/assets/js/Ext.js',
				'position'	=> 100
			),
			array(
				'file'		=> 'ext/timetracking/assets/js/Clock.js',
				'position'	=> 101
			)
		),
		'css' => array(
			array(
				'file'		=> 'ext/timetracking/assets/css/ext.css',
				'position'	=> 100
			)
		)
	),


		// public assets: basis assets for this extension
	'public' => array(
		'js' => array(

		),
		'css' => array(

		)
	),

	'headlet-timetracking' => array(
		'js' => array(
			array(
				'file'		=> 'ext/timetracking/assets/js/HeadletTimetracking.js',
				'position'	=> 110
			)
		),
		'css' => array(
			array(
				'file'		=> 'ext/timetracking/assets/css/headlet-timetracking.css',
				'position'	=> 110
			)
		)
	)

);


$CONFIG['EXT']['project']['assets']['public']['js'][] = array(
	'file'		=> 'ext/timetracking/assets/js/Task.js',
	'position'	=> 101
);
$CONFIG['EXT']['portal']['assets']['public']['js'][] = array(
	'file'		=> 'ext/timetracking/assets/js/Task.js',
	'position'	=> 101
);

?>