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
	trackedTime: 0,
	trackingTime: 0,
	estimatedTime: 0,



	/**
	 * Init task timetracking
	 *
	 */
	init: function() {
		this.Task.init();
	},



	/**
	 * Init task timetracking, start tracking time of given task
	 *
	 * @param	Integer		idTask				Task ID
	 * @param	Integer		trackedTime			Already tracked and saved time
	 * @param	Integer		trackingTime		Currently tracking time which is not saved yet
	 * @param	Integer		estimatedTime		Total estimated time for task
	 */
	initWithTask: function(idTask, trackedTime, trackingTime, estimatedTime) {
		this.init();

		this.task			= idTask;
		this.trackedTime	= trackedTime;
		this.trackingTime	= trackingTime;
		this.estimatedTime	= estimatedTime;

		this.start(idTask, true);
	},



	/**
	 * Start tracking time on given task
	 *
	 * @param	Integer	idTask
	 * @param	Boolean	noRequest
	 */
	start: function(idTask, noRequest) {
			// Make noRequest boolean
		noRequest	= noRequest === true;

			// Check if tracking is active and is normal start
		if( this.isTracking() && noRequest === false ) {
			this.stop(noRequest);
		}

			// If normal start, remove running styles
		if( noRequest === false ) {
			this.removeAllRunningStyles();
		}

			// Set task ID
		this.task = idTask;

			// If initiali request
		if( noRequest === true ) {
				// Send start to all registered clock listeners
			//@ change: Not necessary on first load
			//this.fireStartCallbacks();
				// Start click ticking
			this.Clock.start();
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
		this.sendTrackRequest(this.task, false);
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
			this.estimatedTime	= Todoyu.Helper.intval(response.getTodoyuHeader('estimatedTime'));
			this.trackedTime	= Todoyu.Helper.intval(response.getTodoyuHeader('trackedTime'));			
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
			func(this.task, true);
		}.bind(this));
	},



	/**
	 * Fire all registered stop callbacks
	 */
	fireStopCallbacks: function() {
		this._callbacks.toggle.each(function(func){
			func(this.task, false);
		}.bind(this));
	},



	/**
	 * Toggle timetracking	of given task
	 *
	 * @param	Integer		idTask
	 */
	toggle: function(idTask) {
		if( this.task === idTask ) {
			this.stop();
		} else {
			this.start(idTask);
		}
	},



	/**
	 * Check wether time is being currently tracked
	 */
	isTracking: function() {
		return this.task > 0;
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
			func(this.task, this.trackingTime);
		}.bind(this));
	},



	/**
	 * Reset timetracking - stop track, reinit. time
	 */
	reset: function() {
		this.task = 0;
		this.trackingTime	= 0;
		this.trackedTime	= 0;
		this.estimatedTime	= 0;
	},



	/**
	 * Handle clockUpdate event
	 */
	onClockTick: function() {
		this.trackingTime++;

		this.fireClockCallbacks();
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
	 * Enter description here...
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
	 * Enter description here...
	 */
	getEstimatedTime: function() {
		return this.estimatedTime;
	},



	/**
	 * Enter description here...
	 */
	hasEstimatedTime: function() {
		return this.getEstimatedTime() > 0;
	},



	/**
	 * Enter description here...
	 */
	getPercentOfTime: function() {
		if( this.hasEstimatedTime() ) {
			var total 		= this.getTotalTime();
			var estimated	= this.getEstimatedTime();
			var percent 	= Math.round((total/estimated)*100);
			return percent;
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