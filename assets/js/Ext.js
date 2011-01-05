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

	/**
	 * Lists if callback functions for toggle and clock (every second) events
	 */
	_callbacks: {
		'toggle': [],
		'clock': []
	},

	callback: {
		onToggle: {},
		onTick: []
	},

	/**
	 * Task record of the current tracked task
	 */
	task: {},

	/**
	 * Tracked time parts of current task
	 */
	trackedTotal:	0,
	trackedToday:	0,
	trackedCurrent:	0,


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
				// Start clock
			this.Clock.start();
		} else {
				// Stop clock until new task is started
			this.Clock.stop();

			this.sendRequest(idTask, true);
		}
	},



	/**
	 * Stop tracking time to given task
	 */
	stop: function() {
		this.Clock.stop();

		this.sendRequest(this.getTaskID(), false);
	},



	/**
	 * Send tracking request (start and stop)
	 * The request includes all update requests of other extensions
	 *
	 * @param	{Number}	idTask
	 * @param	{Boolean}	start
	 * @param	{Function}	onComplete
	 */
	sendRequest: function(idTask, start, onComplete) {
		var requestData = this.fireOnToggle(idTask, start);

		var url		= Todoyu.getUrl('timetracking', 'track');
		var options	= {
			parameters: {
				action: 'track',
				start:	start ? 1 : 0,
				task:	idTask,
				data:	Object.toJSON(requestData)
			},
			onComplete: this.onResponse.bind(this, idTask, start, requestData, onComplete)
		};

		Todoyu.send(url, options);
	},



	/**
	 * On request completed
	 * - Load tracking data
	 * - Start clock
	 * - Share all update data with the registered callbacks
	 *
	 * @param	{Number}		idTask
	 * @param	{Boolean}		started
	 * @param	{Object}		data		Request data
	 * @param	{Function}		onComplete	Optional onComplete handler
	 * @param	{Ajax.Response}	response
	 */
	onResponse: function(idTask, started, data, onComplete, response) {
		if( started ) {
				// Load task and tracking info
			this.task			= response.responseJSON.taskData;
			this.trackedTotal	= response.responseJSON.trackedTotal;
			this.trackedToday	= response.responseJSON.trackedToday;
			this.trackedCurrent	= 0;

			this.Clock.start();
		} else {
			this.Clock.stop();
			this.reset();
		}

			// Call all callbacks with the response data
		$H(this.callback.onToggle).each(function(pair){
			pair.value.update.call(this, idTask, response.responseJSON.data[pair.key], response);
		}.bind(this));

		if( typeof onComplete === 'function' ) {
			onComplete.call(this, idTask, started, response);
		}
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
	 *
	 * @return	{Boolean}
	 */
	isTracking: function() {
		return this.task.id > 0;
	},



	/**
	 * Check whether given task is being tracked
	 *
	 * @param	{Number}		idTask
	 * @return	{Boolean}
	 */
	isTrackingTask: function(idTask) {
		return this.getTaskID() == idTask;
	},



	/**
	 * Add toggle callbacks
	 * Allows other extensions to hook in the request and transfer their data in the request
	 * (no extra request needed for updates on tracking toggle)
	 *
	 * @param	{String}	key					Identifier on the server which renders the update content
	 * @param	{Function}	callbackRequest		Function called just before sending request. Parameters: idTask, start - The return value is sent with the request to the server
	 * @param	{Function}	callbackUpdate		Function called just after response. Parameters: idTask, info, response
	 */
	addToggle: function(key, callbackRequest, callbackUpdate) {
		this.callback.onToggle[key] = {
			request:	callbackRequest,
			update:		callbackUpdate
		};
	},



	/**
	 * Add tick callback
	 * Callback is called every second if clock is running
	 * Parameters: idTask, trackedTotal, trackedToday, trackedCurrent
	 *
	 * @param	{Function}	callback
	 */
	addTick: function(callback) {
		this.callback.onTick.push(callback);
	},



	/**
	 * Collect custom request data from all registered callbacks
	 *
	 * @param	{Number}	idTask
	 * @param	{Boolean}	start
	 * @return	{Object}			requestData
	 */
	fireOnToggle: function(idTask, start) {
		var requestData = {};

		$H(this.callback.onToggle).each(function(pair){
			requestData[pair.key] = pair.value.request.call(this, idTask, start)
		}.bind(this));

		return requestData;
	},



	/**
	 * Handle clockUpdate event
	 */
	onClockTick: function() {
		this.trackedCurrent++;

		this.callback.onTick.each(function(func){
			func.call(this, this.getTaskID(), this.getTotalTime(), this.getTrackedToday(), this.getTrackedCurrent())
		}.bind(this));
	},



	/**
	 * Reset timetracking - stop track, reinitialize time
	 */
	reset: function() {
		this.task			= {};
		this.trackedCurrent	= 0;
		this.trackedToday	= 0;
		this.trackedTotal	= 0;
	},



	/**
	 * Get ID of currently tracked task
	 *
	 * @return	{Number}
	 */
	getTaskID: function() {
		return Todoyu.Helper.intval(this.task.id);
	},



	/**
	 * Get task data (all or single value)
	 *
	 * @param	{String}		key
	 * @return	{String|Object}
	 */
	getTaskData: function(key) {
		return key ? this.task[key] : this.task || {} ;
	},



	/**
	 * Get parts of current time
	 *
	 * @return	{Object}
	 */
	getTimeParts: function() {
		return Todoyu.Time.getTimeParts(this.trackedCurrent);
	},



	/**
	 * Get current tracked time formatted
	 *
	 * @return	{String}
	 */
	getTimeFormatted: function() {
		return Todoyu.Time.timeFormatSeconds(this.trackedCurrent);
	},



	/**
	 * Get tracked seconds of current task
	 *
	 * @return	{Number}
	 */
	getTrackedCurrent: function() {
		return this.trackedCurrent;
	},



	/**
	 * Get today tracked time
	 *
	 * @return	{Number}
	 */
	getTrackedToday: function() {
		return this.trackedToday;
	},



	/**
	 * Get total tracked time
	 *
	 * @return	{Number}
	 */
	getTrackedTotal: function() {
		return this.trackedTotal;
	},



	/**
	 * Get total tracked time with current time
	 *
	 * @return	{Number}
	 */
	getTotalTime: function() {
		return this.getTrackedTotal() + this.getTrackedCurrent();
	},



	/**
	 * Get estimated workload of a task in seconds
	 *
	 * @return	{Number}
	 */
	getEstimatedTime: function() {
		return Todoyu.Helper.intval(this.task.estimated_workload);
	},



	/**
	 * Check if estimated workload is set
	 *
	 * @return	{Boolean}
	 */
	hasEstimatedTime: function() {
		return this.getEstimatedTime() > 0;
	},



	/**
	 * Get percent of time already tracked
	 *
	 * @return	{Number}
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