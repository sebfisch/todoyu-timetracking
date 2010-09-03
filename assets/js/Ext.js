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
	trackedTotal: 0,
	trackedToday: 0,
	trackedCurrent: 0,


	/**
	 * Initialize timetracking extension
	 */
	init: function() {
		this.QuickTask.init();
	},


	/**
	 * Init task timetracking
	 */
	initWithoutTask: function() {
		this.Task.init();
		this.PageTitle.init();
	},



	/**
	 * Init task timetracking, start tracking time of given task
	 *
	 * @param	{String}		taskJSON			Task data as JSON
	 * @param	{Number}		trackedTotal		Total tracked time of the task
	 * @param	{Number}		trackedToday		Today tracked time of the task
	 * @param	{Number}		trackedCurrent		Current tracking time (not included in the others)
	 */
	initWithTask: function(taskJSON, trackedTotal, trackedToday, trackedCurrent) {
		this.task			= taskJSON;
		this.trackedTotal	= trackedTotal;
		this.trackedToday	= trackedToday;
		this.trackedCurrent	= trackedCurrent;

		this.initWithoutTask();
		this.start(this.getTaskID(), true);
	},



	/**
	 * Start tracking time on given task
	 *
	 * @param	{Number}	idTask
	 * @param	{Boolean}	noRequest
	 */
	start: function(idTask, noRequest) {
		if( noRequest === true ) {
				// Start click ticking
			this.Clock.start();
		} else if( this.isTracking() ) {
			this.Clock.stop();
			this.sendTrackRequest(idTask, false, this.onStoppedBeforeStart.bind(this));
		} else {
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
	sendTrackRequest: function(idTask, start, onComplete) {
		var url		= Todoyu.getUrl('timetracking', 'track');
		var options	= {
			'parameters': {
				'action':	start ? 'start' : 'stop',
				'task':		idTask
			},
			'onComplete': this.onTrackingRequestSended.bind(this, idTask, start, onComplete)
		};

		Todoyu.send(url, options);
	},

	onStoppedBeforeStart: function(idTask, started, response) {
			// Send request to server (and than start the clock)
		this.sendTrackRequest(idTask, true);
	},



	/**
	 * Handler when tracking request has been sent (started or stoped)
	 *
	 * @param	{Number}			idTask
	 * @param	{Boolean}			started
	 * @param	{Ajax.Response}		response
	 */
	onTrackingRequestSended: function(idTask, started, onComplete, response) {
		if( started ) {
			this.task			= response.getTodoyuHeader('taskData');
			this.trackedTotal	= response.getTodoyuHeader('trackedTotal');
			this.trackedToday	= response.getTodoyuHeader('trackedToday');
			this.trackedCurrent	= 0;

			this.fireStartCallbacks();
			this.Clock.start();
		} else {
			this.fireStopCallbacks();
			this.Clock.stop();
			this.reset();
		}

		if( typeof onComplete === 'function' ) {
			onComplete.call(this, idTask, started, response);
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
	 * @param	{Number}		idTask
	 */
	toggle: function(idTask) {
		if( this.isTrackingTask(idTask) ) {
			this.stop();
		} else {
			this.start(idTask);
		}
	},



	/**
	 * Check whether time is being currently tracked
	 */
	isTracking: function() {
		return ( this.task.id > 0 );
	},



	/**
	 * Check if given task is tracked
	 *
	 * @param	{Number}		idTask
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
	 * @param	{Function}		func
	 */
	registerClockCallback: function(func) {
		this._callbacks.clock.push(func);
	},




	/**
	 * Fire all registered clock callbacks
	 */
	fireClockCallbacks: function() {
		this._callbacks.clock.each(function(func){
			func(this.task.id, this.trackedTotal, this.trackedToday, this.trackedCurrent);
		}.bind(this));
	},



	/**
	 * Reset timetracking - stop track, reinit. time
	 */
	reset: function() {
		this.task 			= {};
		this.trackedCurrent	= 0;
		this.trackedTotal	= 0;
	},



	/**
	 * Handle clockUpdate event
	 */
	onClockTick: function() {
		this.trackedCurrent++;

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
	 * @param	{String}		key
	 */
	getTaskData: function(key) {
		return key === undefined ? this.task : this.task.key;
	},



	/**
	 * Get parts of current time
	 */
	getTimeParts: function() {
		return Todoyu.Time.getTimeParts(this.trackedCurrent);
	},



	/**
	 * Get current tracked time formated
	 */
	getTimeFormatted: function() {
		return Todoyu.Time.timeFormatSeconds(this.trackedCurrent);
	},



	/**
	 * Get tracked seconds of current task
	 */
	getTrackingTime: function() {
		return this.trackedCurrent;
	},



	/**
	 * Get already tracked time
	 */
	getTrackedTime: function() {
		return this.trackedTotal;
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