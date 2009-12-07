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
 * Clock functions to display the current tracked time
 */

Todoyu.Ext.timetracking.Clock = {

	ext: Todoyu.Ext.timetracking,

	/**
	 * Areas to display the clock
	 */
	//displayAreas: [],

	//callbacks: [],
	/**
	 * Peridical executer
	 */
	pe: null,


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
	 * Enter description here...
	 *
	 */
	isRunning: function() {
		return this.pe !== null;
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type periodicalExecuter
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
	 *	@param	String		idDisplayArea
	 *	@param	Integer		startTime
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
	 * Enter description here...
	 *
	 */
	getTask: function() {
		return Todoyu.Ext.timetracking.getTask();
	},



	/**
	 * Enter description here...
	 *
	 */
	getTime: function() {
		return Todoyu.Ext.timetracking.getTime();
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type callback
	 */
	addCallback: function(callback) {
		this.callbacks.push(callback);
	},



	/**
	 * Enter description here...
	 *
	 */
	callCallbacks: function() {
		this.callbacks.each(function(callback) {
			callback(this.getTask(), this.getTime());
		}.bind(this));
	},



	/**
	 * Add a new display area to the list of updated elements
	 *
	 *	@param	String		idDisplayArea
	 */
	addDisplayArea: function(idDisplayArea) {
		this.displayAreas.push(idDisplayArea);
		this.displayAreas.uniq();
	},



	/**
	 * Call updater function for all registered display areas
	 *
	 *	@param	PeriodicalExecuter	pe
	 */
	refreshAreas: function(pe) {
		this.displayAreas.each(function(idDisplayArea) {
			this.updateDisplayArea(idDisplayArea, this.getTime());
		}.bind(this));
	},



	/**
	 * Update a display area with the current time
	 *
	 *	@param	String		idDisplayArea
	 *	@param	Integer		seconds
	 */
	updateDisplayArea: function(idDisplayArea, seconds) {
		var timeString = Todoyu.Helper.timestampFormat(seconds, ':');

		$(idDisplayArea).update(timeString);
	},



	/**
	 * Get an array with the keys hours,minutes and seconds of the current time
	 */
	getTimeParts: function() {
		return {'hours': 	Math.floor(this.getTime()/3600),
				'minutes':	Math.floor((this.getTime()-Math.floor(this.getTime()/3600)*3600)/60),
				'seconds':	this.getTime() - (Math.floor(this.getTime()/3600)*3600) - (Math.floor((this.getTime()-Math.floor(this.getTime()/3600)*3600)/60)*60)};
	}

};