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
	
	barClasses: {},



	/**
	 * Initialize timetracking headlet (register timetracking).
	 */
	init: function() {
			// Register timetracking
		this.ext.registerToggleCallback(this.onClockToggle.bind(this));
		this.ext.registerClockCallback(this.onClockTick.bind(this));

	},
	
	
	/**
	 * @todo	comment
	 * 
	 * @param	Object	event
	 */
	onButtonClick: function(event) {
		if( this.isContentVisible() ) {
			this.hide();
		} else {
			this.hideOthers();
			this.showContent();
		}
	},
	

	/**
	 * Handle toggeling of timetracking (headlet). If timetrack started: show it, otherwise: hide it
	 *
	 * @param	Integer		idTask
	 * @param	Boolean		start
	 */
	onClockToggle: function(idTask, start) {
		this.updateContent();
		this.setActiveStatus(start);
	},



	/**
	 * Handle update event of clock inside timetracking headlet
	 *
	 * @param	Integer	idTask
	 * @param	Time	time
	 */
	onClockTick: function(idTask, time) {
		this.updateTime(time);
		this.updatePercent();
	},
	
	
	hide: function() {
		this.hideContent();
	},
	
	
	setActiveStatus: function(active) {
		this.headlet.getButton('timetracking').toggleClassName('active');
	},

	/**
	 * Update displayed tracked time count inside headlet
	 *
	 * @param	Time	time
	 */
	updateTime: function(time) {
		$('headlet-timetracking-tracking').update( Todoyu.Time.timeFormatSeconds(time) );
	},



	/**
	 * Update (used amount of estimated task workload in) percent inside headlet
	 */
	updatePercent: function() {
		var idPercent = 'headlet-timetracking-percent';

		if( this.ext.hasEstimatedTime() ) {
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


	setBarClasses: function(barClasses) {
		this.barClasses	= $H(barClasses);
	},


	/**
	 * Update timetracking headlet. Evokes rerendering of the headlet.
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
	
	onContentUpdated: function(response) {
		console.log('updated');
	},



	/**
	 * Stop timetracking of given task
	 *
	 *	@param	Integer	idTask
	 */
	stopTask: function(idTask) {
		this.ext.stop(idTask);
	},
	
	startTask: function(idTask) {
		this.ext.start(idTask);
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