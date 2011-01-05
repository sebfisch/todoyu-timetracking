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

Todoyu.Ext.timetracking.Headlet.Timetracking = {

	/**
	 * Ext shortcut
	 *
	 * @var	{Object}	ext
	 */
	ext:	Todoyu.Ext.timetracking,

	button: null,

	info: null,

	barClasses: {},



	/**
	 * Initialize timetracking headlet (register timetracking).
	 */
	init: function() {
			// Register timetracking
		this.ext.addToggle('trackheadlet', this.onTrackingToggle.bind(this), this.onTrackingToggleUpdate.bind(this));
		this.ext.addTick(this.onClockTick.bind(this));
	},



	/**
	 * Handler when clicked on button
	 *
	 * @param	{Event}		event
	 */
	onButtonClick: function(event) {
		if( this.isContentVisible() ) {
			this.hide();
		} else {
			this.hideOthers();
			this.showContent();
			this.saveOpenStatus();
		}
	},



	/**
	 * Handler when clicked on content
	 *
	 * @param	{Event}		event
	 */
	onContentClick: function(event) {
		this.setActive();
	},



	/**
	 * Handle click on body: stop event bubbling if occured inside timetracking headlet content
	 *
	 * @param	{Event}		event
	 */
	onBodyClick: function(event) {
		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},



	/**
	 * Hide timetracking headlet, save display state
	 */
	hide: function() {
		this.hideContent();
		this.saveOpenStatus();
	},



	/**
	 * Callback for timetracking toggeling
	 *
	 * @param	{Number}	idTask
	 * @param	{Boolean}	start
	 * @return	{Boolean}	No data to transmit. Just render new headlet content
	 */
	onTrackingToggle: function(idTask, start) {
		this.setTrackingStatus(start);

		return false;
	},



	/**
	 * Update timetracking headlet with data from tracking request
	 *
	 * @param	{Number}		idTask
	 * @param	{String}		data		New HTML content
	 * @param	{Ajax.Response}	response
	 */
	onTrackingToggleUpdate: function(idTask, data, response) {
		this.setContent(data);
	},



	/**
	 * Handle update event of clock inside timetracking headlet
	 *
	 * @param	{Number}	idTask
	 * @param	{Time}		trackedTotal
	 * @param	{Time}		trackedToday
	 * @param	{Time}		trackedCurrent
	 */
	onClockTick: function(idTask, trackedTotal, trackedToday, trackedCurrent) {
		this.updateTime(trackedCurrent);
		this.updatePercent();
	},



	/**
	 * Set tracking status for button
	 *
	 * @param	{Boolean}		tracking
	 */
	setTrackingStatus: function(tracking) {
		this.headlet.getButton('timetracking')[tracking?'addClassName':'removeClassName']('tracking');
	},



	/**
	 * Update displayed tracked time count inside headlet
	 *
	 * @param	{Time}  	time
	 */
	updateTime: function(time) {
		var headlet = $('headlet-timetracking-tracking');

		if( headlet ) {
			$('headlet-timetracking-tracking').update( Todoyu.Time.timeFormatSeconds(time) );
		}
	},



	/**
	 * Update (used amount of estimated task workload in) percent inside headlet
	 */
	updatePercent: function() {
		var idPercent = 'headlet-timetracking-percent';

		if( Todoyu.exists(idPercent) && this.ext.hasEstimatedTime() ) {
			var percent	= this.ext.getPercentOfTime();
			$(idPercent).update(percent + '%');

			var progress= $('headlet-timetracking-progress');
			this.barClasses.each(function(percent, pair){
				if( percent >= pair.key ) {
					progress.setStyle({
						'backgroundColor': pair.value
					});
					throw $break;
				}
			}.bind(this, percent));
		}
	},



	/**
	 * Set barClasses to internal storage
	 *
	 * @param	{Object}		barClasses
	 */
	setBarClasses: function(barClasses) {
		this.barClasses	= $H(barClasses);
	},



	/**
	 * Update timetracking headlet. Evokes reRendering of the headlet.
	 */
	updateContent: function() {
		var url		= Todoyu.getUrl('timetracking', 'headlet');
		var options	= {
			'parameters': {
				'action':	'update'
			},
			'onComplete':	this.onContentUpdated.bind(this)
		};
		var target	= 'headlet-timetracking-content';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Set timetracking headlet content
	 *
	 * @param	{String}	html
	 */
	setContent: function(html) {
		$('headlet-timetracking-content').update(html);
	},



	/**
	 * Handler when content is updated
	 *
	 * @param	{Ajax.Response}		response
	 */
	onContentUpdated: function(response) {

	},



	/**
	 * Stop timetracking of given task
	 *
	 * @param	{Number}	idTask
	 */
	stopTask: function(idTask) {
		this.ext.stop(idTask);
	},



	/**
	 * Start timetracking of given task
	 *
	 * @param	{Number}	idTask
	 */
	startTask: function(idTask) {
		this.ext.start(idTask);
	},



	/**
	 * Scroll to given task if in current page, otherwise show in project area
	 *
	 * @param	{Number}	idProject
	 * @param	{Number}	idTask
	 */
	goToTask: function(idProject, idTask) {
		if( this.isTaskInCurrentView(idTask) ) {
			$('task-' + idTask).scrollToElement();
		} else {
			Todoyu.Ext.project.goToTaskInProject(idTask, idProject);
		}
	},



	/**
	 * Check whether given task exists in current view
	 *
	 * @param	{Number}	idTask
	 * @return	{Boolean}
	 */
	isTaskInCurrentView: function(idTask) {
		return Todoyu.exists('task-' + idTask);
	}

};