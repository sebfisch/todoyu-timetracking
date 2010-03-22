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
 *	Ext: timetracking
 */

Todoyu.Ext.timetracking = {

	PanelWidget: {},

	Headlet: {},

	_callbacks: {
		'toggle': [],
		'clock': []
	},

	task: {},
	trackedTime: 0,
	trackingTime: 0,


	init: function() {

	},


	/**
	 * Init task timetracking
	 *
	 */
	initWithoutTask: function() {
		this.Task.init();
		this.PageTitle.init();
	},



	/**
	 * Init task timetracking, start tracking time of given task
	 *
	 * @param	String		taskJSON			Task data as JSON
	 * @param	Integer		trackedTime			Already tracked and saved time
	 * @param	Integer		trackingTime		Currently tracking time which is not saved yet
	 * @param	Integer		estimatedTime		Total estimated time for task
	 */
	initWithTask: function(taskJSON, trackedTime, trackingTime) {
		this.task			= taskJSON;
		this.trackedTime	= trackedTime;
		this.trackingTime	= trackingTime;

		this.initWithoutTask();
		this.start(this.getTaskID(), true);
	},



	/**
	 * Start tracking time on given task
	 *
	 * @param	Integer	idTask
	 * @param	Boolean	noRequest
	 */
	start: function(idTask, noRequest) {
			// If initial request
		if( noRequest === true ) {
				// Start click ticking
			this.Clock.start();
		} else {
				// Remove running styles from task
				// @todo	Move to task part
			this.removeAllRunningStyles();
				// Send request to server (and than start the clock)
			this.sendTrackRequest(idTask, true);
		}
	},



	/**
	 * Stop tracking time to given task
	 */
	stop: function() {
		this.removeAllRunningStyles();
		this.sendTrackRequest(this.task.id, false);
	},



	/**
	 * Send track request
	 */
	sendTrackRequest: function(idTask, start) {
		var url		= Todoyu.getUrl('timetracking', 'track');
		var options	= {
			'parameters': {
				'action':	start ? 'start' : 'stop',
				'task':		idTask
			},
			'onComplete': this.onTrackingRequestSended.bind(this, idTask, start)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when tracking request has been sent (started or stoped)
	 *
	 * @param Integer	idTask
	 * @param Boolean	started
	 * @param Object	response
	 */
	onTrackingRequestSended: function(idTask, started, response) {
		if( started ) {
			this.task			= response.getTodoyuHeader('taskData').evalJSON();
			this.trackedTime	= Todoyu.Helper.intval(response.getTodoyuHeader('trackedTime'));
			this.trackingTime	= 0;

			this.fireStartCallbacks();
			this.Clock.start();
		} else {
			this.fireStopCallbacks();
			this.Clock.stop();
			this.reset();
		}
	},



	/**
	 * Fire all registered start callbacks
	 */
	fireStartCallbacks: function() {
		this._callbacks.toggle.each(function(func){
			func(this.getTaskID(), true);
		}.bind(this));
	},



	/**
	 * Fire all registered stop callbacks
	 */
	fireStopCallbacks: function() {
		this._callbacks.toggle.each(function(func){
			func(this.getTaskID(), false);
		}.bind(this));
	},



	/**
	 * Toggle timetracking	of given task
	 *
	 * @param	Integer		idTask
	 */
	toggle: function(idTask) {
		if( this.isTrackingTask(idTask) ) {
			this.stop();
		} else {
			this.start(idTask);
		}
	},



	/**
	 * Check wether time is being currently tracked
	 */
	isTracking: function() {
		return this.task.id > 0;
	},



	/**
	 * Check if given task is tracked
	 *
	 * @param	Integer		idTask
	 */
	isTrackingTask: function(idTask) {
		return this.getTaskID() == idTask;
	},



	/**
	 * Register new callback function to be evoked on timetrack toggeling
	 */
	registerToggleCallback: function(func) {
		this._callbacks.toggle.push(func);
	},



	/**
	 * Register new callback to be evoked on clock event
	 *
	 * @param unknown_type func
	 */
	registerClockCallback: function(func) {
		this._callbacks.clock.push(func);
	},




	/**
	 * Fire all registered clock callbacks
	 */
	fireClockCallbacks: function() {
		this._callbacks.clock.each(function(func){
			func(this.task.id, this.trackingTime);
		}.bind(this));
	},



	/**
	 * Reset timetracking - stop track, reinit. time
	 */
	reset: function() {
		this.task 			= {};
		this.trackingTime	= 0;
		this.trackedTime	= 0;
	},



	/**
	 * Handle clockUpdate event
	 */
	onClockTick: function() {
		this.trackingTime++;

		this.fireClockCallbacks();
	},


	
	/**
	 * Get ID of currently tracked task
	 */
	getTaskID: function() {
		return this.task.id;
	},



	/**
	 * Get task data (all or single value)
	 *
	 * @param	String		key
	 */
	getTaskData: function(key) {
		return key === undefined ? this.task : this.task.key;
	},



	/**
	 * Get parts of current time
	 */
	getTimeParts: function() {
		return Todoyu.Time.getTimeParts(this.trackingTime);
	},



	/**
	 * Get current tracked time formated
	 */
	getTimeFormatted: function() {
		return Todoyu.Time.timeFormatSeconds(this.trackingTime);
	},



	/**
	 * Get tracked seconds of current task
	 */
	getTrackingTime: function() {
		return this.trackingTime;
	},



	/**
	 * Get already tracked time
	 */
	getTrackedTime: function() {
		return this.trackedTime;
	},

	getTotalTime: function() {
		return this.getTrackedTime() + this.getTrackingTime();
	},


	/**
	 * Get estimated workload of a task in seconds
	 */
	getEstimatedTime: function() {
		return Todoyu.Helper.intval(this.task.estimated_workload);
	},



	/**
	 * Check if estimated workload is set
	 */
	hasEstimatedTime: function() {
		return this.getEstimatedTime() > 0;
	},



	/**
	 * Get percent of time already tracked
	 */
	getPercentOfTime: function() {
		if( this.hasEstimatedTime() ) {
			return Math.round((this.getTotalTime()/this.getEstimatedTime())*100);
		} else {
			return 0;
		}
	},



	/**
	 * Remove the 'running' class from all DIV elements (no task is marked as running anymore)
	 */
	removeAllRunningStyles: function() {
		$$('div.running').invoke('removeClassName', 'running');
	}

};