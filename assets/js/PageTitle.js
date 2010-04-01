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
	 * Initialize callbacks and load taskdata
	 */
	init: function() {
		this.ext.registerToggleCallback(this.onClockToggle.bind(this));
		this.ext.registerClockCallback(this.onClockTick.bind(this));
		
		this.task = this.ext.getTaskData();
	},
	
	
	
	/**
	 * Handler when click is stopped/started
	 * 
	 * @param	Integer		idTask
	 * @param	Boolean		start
	 */
	onClockToggle: function(idTask, start) {
		if( start ) {
			this.task = this.ext.getTaskData();		
			this.showInfo();			
		} else {
			this.update(false);
			this.task	= null;
		}
	},
	
	
	/**
	 * Handler when click ticks
	 * 
	 * @param	Integer		idTask
	 * @param	Integer		time
	 */
	onClockTick: function(idTask, time) {
		this.showInfo();
	},
	
	
	
	/**
	 * Show info in window title
	 */
	showInfo: function() {	
		this.update(true, this.task.id_project + '.' + this.task.tasknumber, this.task.title, this.ext.getTotalTime(), this.ext.getPercentOfTime());
	},
	
	
	
	/**
	 * Update window title
	 * 
	 * @param	Boolean		show			Show time tracking info
	 * @param	String		taskNumber		Task number (incl. project)
	 * @param	String		taskTitle		Task title
	 * @param	Integer		time			Seconds of current tracking
	 */
	update: function(show, taskNumber, taskTitle, time, percent) {
		var currentTitle= Todoyu.Ui.getTitle();
		var blankTitle	= currentTitle.split(' - [')[0];
		var trackInfo	= '';
		var percentStr	= percent !== undefined ? ' - ' + percent + '%' : '';

		if( show === true ) {
			var timeStr	= Todoyu.Time.timeFormatSeconds(time);
			trackInfo	= ' - [' + taskNumber + ': ' + taskTitle + ' [' + timeStr + percentStr + ']';			
		}
		
		Todoyu.Ui.setTitle(blankTitle + trackInfo);
	}
	
};