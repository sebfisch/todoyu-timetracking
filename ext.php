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
 * Extension main file for project extension
 *
 * @package		Todoyu
 * @subpackage	Search
 */

	// Declare ext ID, path
define('EXTID_TIMETRACKING', 119);
define('PATH_EXT_TIMETRACKING', PATH_EXT . '/timetracking');

	// Register module locales
TodoyuLanguage::register('timetracking', PATH_EXT_TIMETRACKING . '/locale/ext.xml');

	// Request configurations
require_once( PATH_EXT_TIMETRACKING . '/config/extension.php');
require_once( PATH_EXT_TIMETRACKING . '/config/hooks.php');

?>