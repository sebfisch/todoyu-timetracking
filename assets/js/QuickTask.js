/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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
 * Quicktask modifications of timetracking
 */
Todoyu.Ext.timetracking.QuickTask = {
	
	ext: Todoyu.Ext.timetracking,
	
	fieldStart:		'quicktask-0-field-start-tracking',
	fieldDone:		'quicktask-0-field-task-done',
	
	
	/**
	 * Initialize
	 * Add hooks on quicktask for timetracking
	 */
	init: function() {
			// Callend when quicktask is opened (empty form)
		Todoyu.Hook.add('QuickTaskOpen', this.onQuickTaskOpen.bind(this));		
			// Called when a quicktask is saved
		Todoyu.Hook.add('QuickTaskSaved', this.onQuickTaskSaved.bind(this));
	},
	
	
	
	/**
	 * Hook called when quicktask popup is loaded
	 * Install observers on fields
	 * 
	 * @param	{Ajax.Response}		response		Popup Ajax request response
	 */
	onQuickTaskOpen: function(response) {
		$(this.fieldStart).observe('change', this.preventStartDone.bindAsEventListener(this, 'start'));
		$(this.fieldDone).observe('change', this.preventStartDone.bindAsEventListener(this, 'done'));
	},
	
	
	
	/**
	 * Hook called after quicktask was saved
	 * Start timetracking if php hook has sent the header
	 * 
	 * @param	{Number}			idTask
	 * @param	{Number}			idProject
	 * @param	Ajax.Reponse	response
	 */
	onQuickTaskSaved: function(idTask, idProject, response) {
		if( response.getTodoyuHeader('startTracking') == 1 ) {
			this.ext.Task.start(idTask);
		}
	},


	
	/**
	 * Make sure only one of the options in quicktask form is checked
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