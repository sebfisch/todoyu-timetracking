<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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
 * Renderer for projectbilling related pages in sysmanager
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingSysmanagerRenderer {

	/**
	 * Hooked-in when rendering taskpreset record form in sysmanager
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idRecord
	 * @param	Array			$params
	 * @return	TodoyuForm
	 */
	public static function onBuildFormTaskpreset(TodoyuForm $form, $idRecord, $params) {
			// Add billing-type to default settings of extconf form
		$xmlPath	= 'ext/timetracking/config/form/admin/extconf-project.xml';
		$form->getFieldset('quicktaskSpecific')->addElementsFromXMLAfter($xmlPath, 'quicktask_duration_days');

		return $form;
	}

}

?>