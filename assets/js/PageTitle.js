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
	 * @param	Bool		start
	 */
	onClockToggle: function(idTask, start) {
		if( start ) {			
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
		if( this.task === null ) {
			this.task = this.ext.getTaskData();
		}
		this.update(true, this.task.id_project + '.' + this.task.id, this.task.title, this.ext.getTrackingTime());
	},
	
	
	
	/**
	 * Update window title
	 * 
	 * @param	Bool		show		Show timetracking info
	 * @param	String		taskNumber	Tasknumber (incl. project)
	 * @param	String		taskTitle	Task title
	 * @param	String		time		Seconds of current tracking
	 */
	update: function(show, taskNumber, taskTitle, time) {
		var currentTitle= Todoyu.Ui.getTitle();
		var blankTitle	= currentTitle.split(' - [')[0];
		var trackInfo	= '';
		
		if( show === true ) {
			var timeStr	= Todoyu.Time.timeFormatSeconds(time);
			trackInfo	= ' - [' + taskNumber + ': ' + taskTitle + ' [' + timeStr + ']';			
		}
		
		Todoyu.Ui.setTitle(blankTitle + trackInfo);
	}
	
};