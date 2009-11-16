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

	ext: Todoyu.Ext.timetracking,


	/**
	 * Enter description here...
	 *
	 */
	init: function() {
		this.registerTimetracking();
	},



	/**
	 * Enter description here...
	 */
	registerTimetracking: function() {
		this.ext.registerToggleCallback(this.onToggle.bind(this));
		this.ext.registerClockCallback(this.onClockUpdate.bind(this));
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type idTask
	 * @param unknown_type start
	 */
	onToggle: function(idTask, start) {
		if( start === true ) {
			this.update();
		} else {
			this.hide();
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type idTask
	 * @param unknown_type time
	 */
	onClockUpdate: function(idTask, time) {
		this.updateTime(time);
		this.updatePercent();
	},

	hide: function() {
		Effect.SlideUp('headlettimetracking');
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type time
	 */
	updateTime: function(time) {
		$('headlettimetracking-time-tracking').update( Todoyu.Time.timeFormatSeconds(time) );
	},



	/**
	 * Enter description here...
	 *
	 */
	updatePercent: function() {
		var percentContainer = 'headlettimetracking-time-percent-value';

		if( Todoyu.exists(percentContainer) && this.ext.hasEstimatedTime() ) {
			var percent	= this.ext.getPercentOfTime();
			$(percentContainer).update(percent);
		}
	},



	/**
	 * Enter description here...
	 *
	 */
	update: function() {
		var url		= Todoyu.getUrl('timetracking', 'headlet');
		var options	= {
			'parameters': {
				'action': 'update'
			}
		};
		var target	= 'headlettimetracking';

		Todoyu.Ui.replace(target, url, options);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type idTask
	 */
	stopTask: function(idTask) {
		this.ext.stop(idTask);
	}

};