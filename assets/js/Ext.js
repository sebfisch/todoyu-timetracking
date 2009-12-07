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

	task: 0,
	time: 0,
	estimatedTime: 0,



	/**
	 * Enter description here...
	 *
	 */
	init: function() {
		this.Task.init();
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type currentTask
	 *	@param unknown_type currentTrackedTime
	 *	@param unknown_type currentTaskEstimatedWorkload
	 */
	initWithTask: function(idTask, trackedTime, estimatedWorkload) {
		this.init();

		this.task			= idTask;
		this.time			= trackedTime;
		this.estimatedTime	= estimatedWorkload;

		this.start(idTask, true);
	},


	/**
	 *	Start tracking time to given task
	 *
	 *	@param	unknown_type	idTask
	 *	@param	unknown_type	noRequest
	 */
	start: function(idTask, noRequest) {
		noRequest	= noRequest === true;

		if( this.isTracking() && noRequest === false ) {
			this.stop(noRequest);
		}

			// Set task ID
		this.task = idTask;

		if( noRequest === true ) {
			//this.fireStartCallbacks();
			this.Clock.start();
		} else {
			this.sendTrackRequest(idTask, true);
		}
	},



	/**
	 *	Stop tracking time to given task
	 */
	stop: function() {
		this.sendTrackRequest(this.task, false);
	},



	/**
	 *	Send track request
	 */
	sendTrackRequest: function(idTask, start) {
		var url		= Todoyu.getUrl('timetracking', 'track');
		var options	= {
			'parameters': {
				'task':		idTask,
				'action':	start ? 'start' : 'stop'
			},
			'onComplete': this.onTrackingRequestSended.bind(this, idTask, start)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when tracking request has been sent (started or stoped)
	 *
	 *	@param Integer	idTask
	 *	@param Boolean	started
	 *	@param Object	response
	 */
	onTrackingRequestSended: function(idTask, started, response) {
		if( started ) {
			this.fireStartCallbacks();
			this.Clock.start();
		} else {
			this.fireStopCallbacks();
			this.Clock.stop();
			this.reset();
		}
	},



	/**
	 *	Fire all registered start callbacks
	 */
	fireStartCallbacks: function() {
		this._callbacks['toggle'].each(function(func){
			func(this.task, true);
		}.bind(this));
	},



	/**
	 *	Fire all registered stop callbacks
	 */
	fireStopCallbacks: function() {
		this._callbacks['toggle'].each(function(func){
			func(this.task, false);
		}.bind(this));
	},



	/**
	 *	Toggle timetracking	of given task
	 */
	toggle: function(idTask) {
		if( this.task === idTask ) {
			this.stop();
		} else {
			this.start(idTask);
		}
	},



	/**
	 *	Check wether time is being currently tracked
	 */
	isTracking: function() {
		return this.task > 0;
	},


	/**
	 *	Register new callback function to be evoked on timetrack toggeling
	 */
	registerToggleCallback: function(func) {
		this._callbacks['toggle'].push(func);
	},



	/**
	 *	Register new callback to be evoked on clock event
	 *
	 *	@param unknown_type func
	 */
	registerClockCallback: function(func) {
		this._callbacks['clock'].push(func);
	},




	/**
	 *	Fire all registered clock callbacks
	 */
	fireClockCallbacks: function() {
		this._callbacks['clock'].each(function(func){
			func(this.task, this.time);
		}.bind(this));
	},



	/**
	 *	Reset timetracking - stop track, reinit. time
	 */
	reset: function() {
		this.task = 0;
		this.time = 0;
		this.estimatedTime = 0;
	},



	/**
	 *	Handle clockUpdate event
	 */
	onClockTick: function() {
		this.time++;

		this.fireClockCallbacks();
	},



	/**
	 *	Get parts of current time
	 */
	getTimeParts: function() {
		return Todoyu.Time.getTimeParts(this.time);
	},



	/**
	 *	Get current tracked time formated
	 */
	getTimeFormatted: function() {
		return Todoyu.Time.timeFormatSeconds(this.time);
	},



	/**
	 * Enter description here...
	 *
	 */
	getTime: function() {
		return this.time;
	},



	/**
	 * Enter description here...
	 *
	 */
	getEstimatedTime: function() {
		return this.estimatedTime;
	},



	/**
	 * Enter description here...
	 *
	 */
	hasEstimatedTime: function() {
		return this.getEstimatedTime() > 0;
	},



	/**
	 * Enter description here...
	 *
	 */
	getPercentOfTime: function() {
		if( this.hasEstimatedTime() ) {
			return Math.round((this.getTime()/this.getEstimatedTime())*100);
		} else {
			return 0;
		}
	}
};