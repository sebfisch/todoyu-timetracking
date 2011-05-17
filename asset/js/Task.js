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
 * @module	Timetracking
 */

Todoyu.Ext.timetracking.Task = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.timetracking,



	/**
	 * Initialize timetracking: register clock callbacks
	 *
	 * @method	init
	 */
	init: function() {
		this.registerClockCallbacks();
	},



	/**
	 * Start timetracking of given task
	 *
	 * @method	start
	 * @param	{Number}	idTask
	 */
	start: function(idTask) {
		this.ext.start(idTask);
	},



	/**
	 * Stop timetracking of given task, reset timetrack button style
	 *
	 * @method	stop
	 * @param	{Number}	idTask
	 */
	stop: function(idTask) {
		this.ext.stop();
	},



	/**
	 * Register timetracking clock callbacks
	 *
	 * @method	registerClockCallbacks
	 */
	registerClockCallbacks: function() {
		this.ext.addToggle('tasktab', this.onTrackingToggle.bind(this), this.onTrackingToggleUpdate.bind(this));
		this.ext.addTick(this.onClockTick.bind(this));
	},



	/**
	 * Callback if tracking is toggled
	 *
	 * @method	onTrackingToggle
	 * @param	{Number}	idTask
	 * @param	{Boolean}	start
	 * @return	{Array}		List of tasks to update
	 */
	onTrackingToggle: function(idTask, start) {
		var info = [];
		var idTaskCurrent	= this.ext.getTaskID();

		this.ext.removeAllRunningStyles();

		if( start ) {
			this.setRunningStyle(idTask, start);

				// Update task status
			if( Todoyu.Ext.project.Task.isLoaded(idTask) ) {
				if( Todoyu.Ext.project.Task.getStatus(idTask) == 2 ) { // Open
					Todoyu.Ext.project.Task.setStatus(idTask, 3); // In Progress
				}
			}

			if( this.isTaskTrackingTabLoaded(idTask) ) {
				info.push(idTask);
			}

			if( idTaskCurrent !== idTask && this.isTaskTrackingTabLoaded(idTaskCurrent) ) {
				info.push(idTaskCurrent);
			}
		} else {
			if( this.isTaskTrackingTabLoaded(idTask) ) {
				info.push(idTask);
			}
		}

		return info;
	},



	/**
	 * Update task timetracking tabs with data from tracking request
	 *
	 * @method	onTrackingToggleUpdate
	 * @param	{Number}		idTask
	 * @param	{Object}		data
	 * @param	{Ajax.Response}	response
	 */
	onTrackingToggleUpdate: function(idTask, data, response) {
		if( typeof(data) === 'object' ) {
			$H(data).each(function(pair){
				this.setTabContent(pair.key, pair.value);
			}.bind(this));
		}
	},



	/**
	 * Callback if clock ticked (every second
	 *
	 * @method	onClockTick
	 * @param	{Number}	idTask
	 * @param	{Number}	trackedTotal
	 * @param	{Number}	trackedToday
	 * @param	{Number}	trackedCurrent
	 */
	onClockTick: function(idTask, trackedTotal, trackedToday, trackedCurrent) {
		var el = $('task-' + idTask + '-timetrack-currentsession');
		if( el ) {
			el.update(Todoyu.Time.timeFormatSeconds(trackedCurrent));
		}
	},



	/**
	 * Check whether given task's timetracking tab is loaded
	 *
	 * @method	isTaskTrackingTabLoaded
	 * @param	{Number}	idTask
	 * @return	{Boolean}
	 */
	isTaskTrackingTabLoaded: function(idTask) {
		return Todoyu.exists('task-' + idTask + '-tabcontent-timetracking');
	},



	/**
	 * Set task style 'running', indicating visually that it is currently not / being timetracked
	 *
	 * @method	setRunningStyle
	 * @param	{Number}	idTask
	 * @param	{Boolean}	running
	 */
	setRunningStyle: function(idTask, running) {
		if( Todoyu.exists('task-' + idTask) ) {
			if( running ) {
				$('task-' + idTask).addClassName('running');
			} else {
				$('task-' + idTask).removeClassName('running');
			}
		}
	},



	/**
	 * Update timetracking tab (contains start / stop button, list of prev. tracked times, etc.) of given task.
	 *
	 * @method	updateTab
	 * @param	{Number}	idTask
	 */
	updateTab: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			parameters: {
				action:	'update',
				'task':		idTask
			}
		};
		var target	= 'task-' + idTask + '-tabcontent-timetracking';

		if( Todoyu.exists(target) ) {
			Todoyu.Ui.update(target, url, options);
		}
	},



	/**
	 * Set new HTML content for tab content
	 *
	 * @method	setTabContent
	 * @param	{Number}	idTask
	 * @param	{String}	html
	 */
	setTabContent: function(idTask, html) {
		var target	= 'task-' + idTask + '-tabcontent-timetracking';

		if( Todoyu.exists(target) ) {
			$(target).update(html);
		}
	},



	/**
	 * Update timetracking list of given task
	 *
	 * @method	updateTrackList
	 * @param	{Number}	idTask
	 */
	updateTrackList: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			parameters: {
				action:	'tracklist',
				'task':		idTask
			}
		};
		var target	= 'task-' + idTask + '-timetracks' ;

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Update control box in timetracking tab of given task
	 *
	 * @method	updateTabControl
	 * @param	{Number}	idTask
	 */
	updateTabControl: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			parameters: {
				action:	'control',
				'task':		idTask
			}
		};
		var target	= 'task-' + idTask + '-timetrack-control' ;

		if( Todoyu.exists(target) ) {
			Todoyu.Ui.update(target, url, options);
		}
	},



	/**
	 * Get track edit form
	 *
	 * @method	editTrack
	 * @param	{Number}	idTask
	 * @param	{Number}	idTrack
	 */
	editTrack: function(idTask, idTrack) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			parameters: {
				action:		'edittrack',
				'track':	idTrack
			},
			onComplete: this.onEditFormLoaded.bind(this, idTask, idTrack)
		};
		var target 	= 'task-' + idTask + '-track-' + idTrack;

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Event handler: edit form loaded
	 *
	 * @method	onEditFormLoaded
	 * @param	{Number}		idTask
	 * @param	{Number}		idTrack
	 * @param	{Ajax.Response}	response
	 */
	onEditFormLoaded: function(idTask, idTrack, response) {
		var field	= 'timetrack-' + idTrack + '-field-workload-tracked';

		if( Todoyu.exists(field) ) {
			$(field).select();
		}
	},



	/**
	 * Save edited track
	 *
	 * @method	saveTrack
	 * @param	{Number}	idTask
	 * @param	{Number}	idTrack
	 */
	saveTrack: function(idTask, idTrack) {
		$('timetrack-' + idTrack + '-form').request({
			parameters: {
				action: 'updatetrack'
			},
			onComplete: this.onTrackSaved.bind(this, idTask, idTrack)
		});
	},



	/**
	 * Event handler: being evoked after edited track has been saved
	 *
	 * @method	onTrackSaved
	 * @param	{Number}		idTask
	 * @param	{Number}		idTrack
	 * @param	{Ajax.Response}	response
	 */
	onTrackSaved: function(idTask, idTrack, response) {
		this.updateTab(idTask);
	},



	/**
	 * Cancel track editing
	 *
	 * @method	cancelTrackEditing
	 * @param	{Number}	idTask
	 * @param	{Number}	idTrack
	 */
	cancelTrackEditing: function(idTask, idTrack) {
		this.updateTrack(idTask, idTrack);
	},



	/**
	 * Update timetracking tab content
	 *
	 * @method	updateTrackContent
	 * @param	{Number}	idTask
	 * @param	{Number}	idTrack
	 * @param	{String}	tabContent
	 */
	updateTrackContent: function(idTask, idTrack, tabContent) {
		$('task-' + idTask + '-track-' + idTrack).replace(tabContent);
	},



	/**
	 * Toggle timetracks list visibility
	 *
	 * @method	toggleList
	 * @param	{Number}	idTask
	 */
	toggleList: function(idTask) {
		Todoyu.Ui.toggle('task-' + idTask + '-timetracks');
	},



	/**
	 * Updates a single track
	 *
	 * @method	updateTrack
	 * @param	{Number}	idTask
	 * @param	{Number}	idTrack
	 */
	updateTrack: function(idTask, idTrack){
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			parameters: {
				action:	'trackcontent',
				'idTrack':	idTrack
			}
		};

		var target 	= 'task-' + idTask + '-track-' + idTrack;

		Todoyu.Ui.replace(target, url, options);
	}

};