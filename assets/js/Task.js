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

Todoyu.Ext.timetracking.Task = {

	ext: Todoyu.Ext.timetracking,



	/**
	 * Enter description here...
	 *
	 */
	init: function() {
		this.registerClockCallbacks();
	},



	/**
	 * Start task timetracking
	 *
	 * @param unknown_type idTask
	 */
	start: function(idTask) {
		this.ext.start(idTask);
		this.setRunningStyle(idTask, true);
		
		if( Todoyu.Ext.project.Task.getStatus(idTask) !== 2 ) {
			console.log('Set running');
		}
		
	},



	/**
	 * Stop task timetracking
	 *
	 * @param unknown_type idTask
	 */
	stop: function(idTask) {
		this.ext.stop();
		this.setRunningStyle(idTask, false);
	},



	/**
	 * Register timetracking clock callbacks
	 */
	registerClockCallbacks: function() {
		this.ext.registerToggleCallback(this.onClockToggle.bind(this));
		this.ext.registerClockCallback(this.onClockTick.bind(this));
	},



	/**
	 * Event handler 'onClockToggle'
	 *
	 * @param unknown_type idTask
	 * @param unknown_type start
	 */
	onClockToggle: function(idTask, start) {
		if( this.isTaskTrackingTabVisible(idTask) ) {
			if( start ) {
				this.updateTabControl(idTask);
				this.setRunningStyle(idTask, true);
			} else {
				this.updateTab(idTask);
				this.setRunningStyle(idTask, false);
			}
		}
	},



	/**
	 * Event handler: 'onClockTick'
	 *
	 * @param unknown_type idTask
	 * @param unknown_type time
	 */
	onClockTick: function(idTask, time) {
		var el = $('task-' + idTask + '-timetrack-currentsession');
		if( el ) {
			el.update(this.ext.getTimeFormatted());
		}
	},




	isTaskTrackingTabVisible: function(idTask) {
		return Todoyu.exists('task-' + idTask + '-tabcontent-timetracking');
	},



	/**
	 * Set task style 'running', indicating visually that it is currently not / being timetracked
	 */
	setRunningStyle: function(idTask, running) {
		if( Todoyu.exists('task-' + idTask) ) {
			if( running ) {
				$('task-' + idTask).addClassName('running');
			} else {
				$('task-' + idTask).removeClassName('running');
			}
		}
	},



	/**
	 * Update timetracking tab
	 */
	updateTab: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			'parameters': {
				'cmd': 'update',
				'task':	idTask
			}
		};
		var target	= 'task-' + idTask + '-tabcontent-timetracking';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Update timetracking tacks list
	 */
	updateTrackList: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			'parameters': {
				'cmd': 'tracklist',
				'task':	idTask
			}
		};
		var target	= 'task-' + idTask + '-timetracks' ;

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Update timetracking tab controll
	 */
	updateTabControl: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			'parameters': {
				'cmd':	'control',
				'task':	idTask
			}
		};
		var target	= 'task-' + idTask + '-timetrack-control' ;

		if(Todoyu.exists(target))	{
			Todoyu.Ui.update(target, url, options);
		}
	},



	/**
	 * Get track editing form
	 */
	editTrack: function(idTask, idTrack) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			'parameters': {
				'cmd': 'edittrack',
				'track': idTrack
			},
			'onComplete': this.onEditFormLoaded.bind(this, idTask, idTrack)
		};
		var target 	= 'task-' + idTask + '-track-' + idTrack;

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 *	Event handler: edit form loaded
	 */
	onEditFormLoaded: function(idTask, idTrack, response) {
		$('timetrack-' + idTrack + '-field-workload-tracked').select();
	},



	/**
	 * Save edited track
	 */
	saveTrack: function(idTask, idTrack) {
		$('timetrack-' + idTrack + '-form').request({
			'parameters': {
				'cmd': 'updatetrack'
			},
			'onComplete': this.onTrackSaved.bind(this, idTask, idTrack)
		});
	},



	/**
	 *	Event handler: being evoked after edited track has been saved
	 */
	onTrackSaved: function(idTask, idTrack, response) {
		this.updateTabContent(idTask, response.responseText);
	},



	/**
	 *	Cancel track editing
	 */
	cancelTrackEditing: function(idTask) {
		this.updateTrackList(idTask);
	},



	/**
	 * Update timetracking tab content
	 */
	updateTabContent: function(idTask, tabContent) {
		$('task-' + idTask + '-tabcontent-timetracking').update(tabContent);
	},



	/**
	 * Toggle timetracks list visibility
	 */
	toggleList: function(idTask) {
		Todoyu.Ui.toggle('task-' + idTask + '-timetracks');
	}

};