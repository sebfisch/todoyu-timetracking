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

Todoyu.Ext.timetracking.Headlet.Timetracking = {

	/**
	 *	Ext shortcut
	 */
	ext:	Todoyu.Ext.timetracking,
	
	button: null,
	
	info: null,



	/**
	 * Initialize timetracking headlet (register timetracking).
	 */
	init: function() {
			// Register timetracking
		this.ext.registerToggleCallback(this.onToggle.bind(this));
		this.ext.registerClockCallback(this.onClockUpdate.bind(this));
		
			// Register button observer
		this.button	= $('headlet-Timetracking').down('div.button');
		this.info	= $('headlet-Timetracking').down('div.info');
		
		//this.button.observe('mouseover', this.onButtonHover.bindAsEventListener(this));
		this.button.observe('click', this.onButtonClick.bindAsEventListener(this));
	},
	
	onButtonHover: function(event) {
		this.info.setStyle({
			'display': 'block'
		});
		this.info.focus();
	},
	
	onButtonClick: function(event) {
		console.log('click');
	},
	


	/**
	 * Handle toggeling of timetracking (headlet). If timetrack started: show it, otherwise: hide it
	 *
	 *	@param	Integer	idTask
	 *	@param	Boolean	start
	 */
	onToggle: function(idTask, start) {
		if( start === true ) {
			this.update();
		} else {
			this.hide();
		}
	},



	/**
	 * Handle update event of clock inside timetracking headlet
	 *
	 *	@param	Integer	idTask
	 *	@param	Time	time
	 */
	onClockUpdate: function(idTask, time) {
		this.updateTime(time);
		this.updatePercent();
	},



	/**
	 * Evoke slide-up effect of headlet to hide it
	 */
	hide: function() {
		Effect.SlideUp('headlettimetracking');
	},



	/**
	 * Update displayed tracked time count inside headlet
	 *
	 *	@param	Time	time
	 */
	updateTime: function(time) {
		$('headlettimetracking-time-tracking').update( Todoyu.Time.timeFormatSeconds(time) );
	},



	/**
	 * Update (used amount of estimated task workload in) percent inside headlet
	 */
	updatePercent: function() {
		var percentContainer = 'headlettimetracking-time-percent-value';

		if( Todoyu.exists(percentContainer) && this.ext.hasEstimatedTime() ) {
			var percent	= this.ext.getPercentOfTime();
			$(percentContainer).update(percent);
		}
	},



	/**
	 * Update timetracking headlet. Evokes rerendering of the headlet.
	 */
	update: function() {
		var url		= Todoyu.getUrl('timetracking', 'headlet');
		var options	= {
			'parameters': {
				'action':	'update'
			}
		};
		var target	= 'headlettimetracking';

		Todoyu.Ui.replace(target, url, options);
	},



	/**
	 * Stop timetracking of given task
	 *
	 *	@param	Integer	idTask
	 */
	stopTask: function(idTask) {
		this.ext.stop(idTask);
	},



	/**
	 *	Go to given task
	 *
	 *	@param	Integer	idProject
	 *	@param	Integer	idTask
	 */
	goToTask: function(idProject, idTask) {
		if( this.isTaskInCurrentView(idTask) ) {
			$('task-' + idTask).scrollToElement();
		} else {
			Todoyu.Ext.project.goToTaskInProject(idTask, idProject);
		}
	},



	/**
	 *	Check if given task is exists in current view
	 *
	 *	@param	Integer	idTask
	 */
	isTaskInCurrentView: function(idTask) {
		return Todoyu.exists('task-' + idTask);
	}
};