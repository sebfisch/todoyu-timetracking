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

Todoyu.Ext.timetracking.Task = {

	/**
	 * Ext shortcut
	 */
	ext:	Todoyu.Ext.timetracking,



	/**
	 * Initialize timetracking: register clock callbacks
	 */
	init: function() {
		this.registerClockCallbacks();
	},



	/**
	 * Start timetracking of given task
	 *
	 * @param {Integer}t idTask
	 */
	start: function(idTask) {
		this.ext.start(idTask);		
	},



	/**
	 * Stop timetracking of given task, reset timetrack button style
	 *
	 * @param {Integer} idTask
	 */
	stop: function(idTask) {
		this.ext.stop();
	},



	/**
	 * Register timetracking clock callbacks
	 */
	registerClockCallbacks: function() {
		this.ext.registerToggleCallback(this.onClockToggle.bind(this));
		this.ext.registerClockCallback(this.onClockTick.bind(this));
	},



	/**
	 * Event handler 'onClockToggle': evoked on toggle (start / stop) of clock with current running timetracking
	 *
	 * @param	{Integer}		idTask
	 * @param	{Boolean}		start
	 */
	onClockToggle: function(idTask, start) {
		if( start ) {
			this.setRunningStyle(idTask, true);
			Todoyu.Ext.project.Task.setStatus(idTask, 3); // In Progress
			/*
			if(Todoyu.getArea() == 'portal'){
				Todoyu.Ext.portal.Task.refresh(idTask);
			} else {
				Todoyu.Ext.project.Task.refresh(idTask);
			}
			*/
		} else {
			//this.setRunningStyle(idTask, false);
			if( this.isTaskTrackingTabLoaded(idTask) ) {
				this.updateTab(idTask);
			}
		}
	},



	/**
	 * Event handler: 'onClockTick': evoked on each tick of the clock showing the current time of the current running timetrack
	 *
	 * @param	{Integer}	idTask
	 * @param	{Time}  	time
	 */
	onClockTick: function(idTask, time) {
		var el = $('task-' + idTask + '-timetrack-currentsession');
		if( el ) {
			el.update(this.ext.getTimeFormatted());
		}
	},



	/**
	 * Check whether given task's timetracking tab is loaded
	 * 
	 * @param	{Integer}	idTask
	 * @return	{Boolean}
	 */
	isTaskTrackingTabLoaded: function(idTask) {
		return Todoyu.exists('task-' + idTask + '-tabcontent-timetracking');
	},



	/**
	 * Set task style 'running', indicating visually that it is currently not / being timetracked
	 * 
	 * @param	{Integer}	idTask
	 * @param	{Boolean}	running
	 */
	setRunningStyle: function(idTask, running) {		
		if( Todoyu.exists('task-' + idTask) ) {
			if( running ) {
				$('task-' + idTask).addClassName('running');
			} else {
				$('task-' + idTask).removeClassName('running');
			}			
			this.updateTab(idTask);
		}
	},
	


	/**
	 * Update timetracking tab (contains start / stop button, list of prev. tracked times, etc.) of given task. 
	 * 
	 * @param	{Integer}	idTask
	 */
	updateTab: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			'parameters': {
				'action':	'update',
				'task':		idTask
			}
		};
		var target	= 'task-' + idTask + '-tabcontent-timetracking';
		
		if( Todoyu.exists(target) ) {
			Todoyu.Ui.update(target, url, options);
		}
	},



	/**
	 * Update timetracking list of given task
	 * 
	 * @param	{Integer}	idTask
	 */
	updateTrackList: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			'parameters': {
				'action':	'tracklist',
				'task':		idTask
			}
		};
		var target	= 'task-' + idTask + '-timetracks' ;

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Update controll box in timetracking tab of given task 
	 * 
	 * @param	{Integer}	idTask
	 */
	updateTabControl: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			'parameters': {
				'action':	'control',
				'task':		idTask
			}
		};
		var target	= 'task-' + idTask + '-timetrack-control' ;

		if(Todoyu.exists(target))	{
			Todoyu.Ui.update(target, url, options);
		}
	},



	/**
	 * Get track edit form
	 * 
	 * @param	{Integer}	idTask
	 *  @param	{Integer}	idTrack
	 */
	editTrack: function(idTask, idTrack) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			'parameters': {
				'action':	'edittrack',
				'track':	idTrack
			},
			'onComplete': this.onEditFormLoaded.bind(this, idTask, idTrack)
		};
		var target 	= 'task-' + idTask + '-track-' + idTrack;

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Event handler: edit form loaded
	 * 
	 * @param	{Integer}	idTask
	 * @param	{Integer}	idTrack
	 * @param	{Object}	response
	 */
	onEditFormLoaded: function(idTask, idTrack, response) {
		$('timetrack-' + idTrack + '-field-workload-tracked').select();
	},



	/**
	 * Save edited track
	 * 
	 * @param	{Integer}	idTask
	 * @param	{Integer}	idTrack
	 */
	saveTrack: function(idTask, idTrack) {
		$('timetrack-' + idTrack + '-form').request({
			'parameters': {
				'action': 'updatetrack'
			},
			'onComplete': this.onTrackSaved.bind(this, idTask, idTrack)
		});
	},



	/**
	 * Event handler: being evoked after edited track has been saved
	 * 
	 * @param	{Integer}	idTask
	 * @param	{Integer}	idTrack
	 * @param	{Object}	response
	 */
	onTrackSaved: function(idTask, idTrack, response) {
		this.updateTrackContent(idTask, idTrack, response.responseText);
		
			// Add the zebra for the list
		var tracks	= $('task-' + idTask + '-timetracks').select('li');
				
		tracks.each(function(item, index){
			item[index%2?'removeClassName':'addClassName']('odd');
		});
	},



	/**
	 * Cancel track editing
	 * 
	  * @param	{Integer}	idTask
	 */
	cancelTrackEditing: function(idTask, idTrack) {
		this.updateTrack(idTask, idTrack);
	},



	/**
	 * Update timetracking tab content
	 * 
	 * @param	{Integer}	idTask
	 * @param	{String}	tabContent
	 */
	updateTrackContent: function(idTask, idTrack, tabContent) {
		$('task-' + idTask + '-track-' + idTrack).replace(tabContent);
	},



	/**
	 * Toggle timetracks list visibility
	 * 
	 * @param	{Integer}	idTask
	 */
	toggleList: function(idTask) {
		Todoyu.Ui.toggle('task-' + idTask + '-timetracks');
	},



	/**
	 * Updates a single track
	 * 
	 * @param	{Integer}	idTask
	 * @param	{Integer}	idTrack
	 */
	updateTrack: function(idTask, idTrack){
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			'parameters': {
				'action':	'trackcontent',
				'idTrack':	idTrack
			}
		};

		var target 	= 'task-' + idTask + '-track-' + idTrack;

		Todoyu.Ui.replace(target, url, options);
	}

};