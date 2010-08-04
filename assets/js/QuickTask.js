/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
 * Quick task modifications to time tracking
 */
Todoyu.Ext.timetracking.QuickTask = {

	/**
	 * Extension backlink
	 *
	 * @var	{Object}	ext
	 */
	ext: Todoyu.Ext.timetracking,

	fieldStart:		'quicktask-0-field-start-tracking',

	fieldDone:		'quicktask-0-field-task-done',



	/**
	 * Initialize: Add hooks for time tracking to quick task 
	 */
	init: function() {
			// Called when quick task is opened (empty form)
		Todoyu.Hook.add('quickTaskOpen', this.onQuickTaskOpen.bind(this));
			// Called when a quick task is saved
		Todoyu.Hook.add('quickTaskSaved', this.onQuickTaskSaved.bind(this));
	},



	/**
	 * Hook called when quick task popUp is loaded
	 * Install observers on fields
	 * 
	 * @param	{Ajax.Response}		response		PopUp AJAX request response
	 */
	onQuickTaskOpen: function(response) {
		$(this.fieldStart).observe('change', this.preventStartDone.bindAsEventListener(this, 'start'));
		$(this.fieldDone).observe('change', this.preventStartDone.bindAsEventListener(this, 'done'));
	},



	/**
	 * Hook called after quick task was saved
	 * Start time tracking if PHP hook has sent the header
	 * 
	 * @param	{Number}			idTask
	 * @param	{Number}			idProject
	 * @param	{Ajax.Reponse}		response
	 */
	onQuickTaskSaved: function(idTask, idProject, response) {
		if( response.getTodoyuHeader('startTracking') == 1 ) {
			this.ext.Task.start(idTask);
		}
	},



	/**
	 * Make sure only one of the options in quick task form is checked
	 * - start tracking
	 * - task done
	 * 
	 * @param	{Event}		event
	 * @param	{String}	key
	 */
	preventStartDone: function(event, key) {
		if( key === 'start' ) {
			if( $(this.fieldDone).checked ) {
				$(this.fieldDone).checked = false;
			}
		}
		if( key === 'done' ) {
			if( $(this.fieldStart).checked ) {
				$(this.fieldStart).checked = false;
			}
		}
	}

};