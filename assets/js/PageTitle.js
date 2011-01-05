/**
 * Update page title with current timetracking info
 */
Todoyu.Ext.timetracking.PageTitle = {
	/**
	 * Extension backlink
	 */
	ext: Todoyu.Ext.timetracking,



	/**
	 * Task data container
	 */
	task: null,

	/**
	 * Initialize callbacks and load task data
	 */
	init: function() {
		this.ext.addToggle('pagetitle', this.onTrackingToggle.bind(this), this.onTrackingToggleUpdate.bind(this));
		this.ext.addTick(this.onClockTick.bind(this));

		this.task = this.ext.getTaskData();
	},



	/**
	 * Handler when click is stopped/started
	 *
	 * @param	{Number}		idTask
	 * @param	{Boolean}		start
	 * @return	{Boolean}
	 */
	onTrackingToggle: function(idTask, start) {
		return false;
	},



	/**
	 * Handle browser page title update on toggeling of tracking
	 *
	 * @param	{Number}	idTask
	 * @param	{Object}	data
	 * @param	{Response}	response
	 */
	onTrackingToggleUpdate: function(idTask, data, response) {
		this.task = this.ext.getTaskData();

		if( this.ext.isTracking() ) {
			this.showInfo();
		} else {
			this.hideInfo();
		}
	},



	/**
	 * Handler clock tick
	 *
	 * @param	{Number}	idTask
	 * @param	{Number}	trackedTotal
	 * @param	{Number}	trackedToday
	 * @param	{Number}	trackedCurrent
	 */
	onClockTick: function(idTask, trackedTotal, trackedToday, trackedCurrent) {
		this.showInfo();
	},



	/**
	 * Show info (tracked time versus percent of estimated) in browser window title
	 */
	showInfo: function() {
		var taskNumber	= this.task.id_project + '.' + this.task.tasknumber;
		this.update(true, taskNumber, this.task.title, this.ext.getTotalTime(), this.ext.getPercentOfTime());
	},



	/**
	 * Hide tracking info (no task running)
	 */
	hideInfo: function() {
		this.update(false);
	},



	/**
	 * Update browser window title
	 *
	 * @param	{Boolean}		show			Show time tracking info
	 * @param	{String}		taskNumber		Task number (incl. project)
	 * @param	{String}		taskTitle		Task title
	 * @param	{Number}		time			Seconds of current tracking
	 */
	update: function(show, taskNumber, taskTitle, time, percent) {
		var blankTitle	= Todoyu.Ui.getTitle().split(' - [').first();
		var trackInfo	= '';

		if( show === true ) {
			var timeStr		= Todoyu.Time.timeFormatSeconds(time);
			var percentStr	= percent !== undefined ? ' - ' + percent + '%' : '';

			trackInfo	= ' - [' + taskNumber + ': ' + taskTitle.substr(0, 50) + ' [' + timeStr + percentStr + ']';
		}

		Todoyu.Ui.setTitle(blankTitle + trackInfo);
	}

};