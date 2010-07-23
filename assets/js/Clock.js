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
 * Clock functions to display the current tracked time
 */

Todoyu.Ext.timetracking.Clock = {

	/**
	 * Ext shortcut
	 *
	 * @var	{Object}	ext
	 */
	ext:	Todoyu.Ext.timetracking,

	/**
	 * Areas to display the clock
	 */
//	displayAreas:	[],

//	callbacks:		[],
	/**
	 * Peridical executer
	 */
	pe:		null,



	/**
	 * Start loop which refreshes the clock every second
	 * Prevents making multiple instances (the clock would run faster every time!)
	 */
	start: function() {
		if( ! this.isRunning() ) {
			this.pe = new PeriodicalExecuter(this.onClockTick.bind(this), 1);
		}
	},



	/**
	 * Stop clock refreshing
	 */
	stop: function() {
		if( this.isRunning() ) {
			this.pe.stop();
			this.pe = null;
		}
	},



	/**
	 * Check whether clock (periodical execution update) is runnig
	 * 
	 * @return	{Boolean}
	 */
	isRunning: function() {
		return this.pe !== null;
	},



	/**
	 * Handler for clock tick: evoke timetracking clock ticking handler
	 *
	 * @param	{Object}	periodicalExecuter
	 */
	onClockTick: function(periodicalExecuter) {
		this.ext.onClockTick();
	},



//	setTime: function(time) {
//		Todoyu.Ext.timetracking.setTime(time);
//	},



	/**
	 * Show a new clock in a display area. Can be initialized with a start time
	 *
	 * @param	{String}		idDisplayArea
	 * @param	{Number}		startTime
	 */
	showClock: function(idDisplayArea, startTime) {
		this.addDisplayArea(idDisplayArea);

		if( ! this.isRunning() ) {
			if( typeof startTime === 'number' ) {
				this.setTime(startTime);
			}
			this.start();
		}
	},



	/**
	 * Get current tracked task
	 *
	 * @todo	check: used?
	 */
	getTask: function() {
		return Todoyu.Ext.timetracking.getTask();
	},



	/**
	 * Get currently tracked time
	 *
	 * @return	{Number}
	 */
	getTime: function() {
		return this.ext.getTrackingTime();
	},



	/**
	 * Register given callback
	 *
	 * @param	{Function}	callback
	 */
	addCallback: function(callback) {
		this.callbacks.push(callback);
	},



	/**
	 * Call registered callback functions
	 */
	callCallbacks: function() {
		this.callbacks.each(function(callback) {
			callback(this.getTask(), this.getTime());
		}.bind(this));
	},



	/**
	 * Add a new display area to the list of updated elements
	 *
	 * @param	{String}		idDisplayArea
	 */
	addDisplayArea: function(idDisplayArea) {
		this.displayAreas.push(idDisplayArea);
		this.displayAreas.uniq();
	},



	/**
	 * Call updater function for all registered display areas
	 *
	 * @param	{PeriodicalExecuter}	pe
	 */
	refreshAreas: function(pe) {
		this.displayAreas.each(function(idDisplayArea) {
			this.updateDisplayArea(idDisplayArea, this.getTime());
		}.bind(this));
	},



	/**
	 * Update a display area with the current time
	 *
	 * @param	{String}		idDisplayArea
	 * @param	{Number}		seconds
	 */
	updateDisplayArea: function(idDisplayArea, seconds) {
		var timeString = Todoyu.Helper.timestampFormat(seconds, ':');

		$(idDisplayArea).update(timeString);
	},



	/**
	 * Get an array with the keys hours, minutes and seconds of the current time
	 *
	 * @return	{Array}
	 */
	getTimeParts: function() {
		var hours	= Math.floor(this.getTime() / Todoyu.Time.seconds.hour);
		var minutes	= this.getTime() - (hours * Todoyu.Time.seconds.hour);

		return {
			'hours': 	hours,
			'minutes':	Math.floor(minutes / Todoyu.Time.seconds.minute),
			'seconds':	minutes - (Math.floor(minutes / Todoyu.Time.seconds.minute) * Todoyu.Time.seconds.minute)
		};
	}

};