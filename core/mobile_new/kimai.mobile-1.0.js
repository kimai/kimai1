/**
 * Kimai - mobile web frontend
 * 
 * @version 0.1
 * @author Kevin Papst <kpapst@gmx.net>
 */
 
/**
 * global variable to store the currently running timer.
 */
lastTimer = null;
 
 
/**
 * Running when page loads. 
 * Initializes the environment and attaches some action handler to buttons.
 */ 
$(function(){
	
	var obj = $.mobile.path.parseUrl(location.href);
	Kimai.setJsonApi(obj.domain + obj.directory + '../core/json.php');
	
	$('#password').bind('change keydown keypress keyup', function(){
		validateLoginButton();
	});
	$('#username').bind('change keydown keypress keyup', function(){
		validateLoginButton();
	});
	
	// authentication logic
	$('#btnLogin').button();
	$('#btnLogin').bind('click', function() {
		if (Kimai.authenticate($('#username').val(), $('#password').val())) {
			$.mobile.showPageLoadingMsg();	
			$('#loginForm').hide();
			$('.kimai-not-login').removeClass('kimai-not-login');
			setProjects(Kimai.getProjects());
			setTasks(Kimai.getTasks());
			setActiveTask(Kimai.getRunningTask());
			$.mobile.hidePageLoadingMsg();
		} else {
			$(this).parent().find('span.ui-icon').removeClass('ui-icon-check').addClass('ui-icon-alert');
		}
	});
	
	// actions on start and stop tasks
	$('#recorder').slider();
	$('#recorder').bind('change', function() {
		startStopRecord();
	});
	$('#tasks').selectmenu();
	$('#tasks').bind('change', function() {
		validateStartStopButton();
	});
	$('#projects').selectmenu();
	$('#projects').bind('change', function() {
		validateStartStopButton();
	});

	// trigger the state of the login and start/stop buttons
	validateLoginButton();
});

function setWindowState(title, stateMsg)
{
	document.title = title;
	$('#duration').text(stateMsg);
}

/**
 * This function cares about starting and stopping of an entry.
 * Also updates the visual UI elements.
 */
function startStopRecord()
{
	var task = Kimai.getRunningTask();
	if (task != null) {
		if (!Kimai.stop()) {
			alert('Could not stop entry');
		}
	} else {
		if (!Kimai.start($('#projects').val(),$('#tasks').val())) {
			alert('Could not start entry');
		}
	}
	
	setActiveTask(Kimai.getRunningTask());
}

/**
 * Sets the active task and all UI components into their correct state.
 */
function setActiveTask(task)
{
	if (task == null)
	{
		$('#projects').selectmenu('enable');
		$('#tasks').selectmenu('enable');
		$('#recorder').val('off');
		window.clearTimeout(lastTimer);
		setWindowState('Kimai Mobile: Easy mobile Time-Tracking v0.1', 'Please select the task you want to start');
	}
	else 
	{
		$('#projects').val(task.zef_pctID);
		$('#tasks').val(task.zef_evtID);
		$('#projects').selectmenu('disable');
		$('#tasks').selectmenu('disable');
		$('#recorder').val('on');
		updateTimer(task.zef_in);
	}

	$('#recorder').slider('refresh');
	$('#projects').selectmenu('refresh');
	$('#tasks').selectmenu('refresh');
}

function updateTimer(start)
{
	var currently = new Date();
	currently.setTime(currently.getTime() - (start*1000));
	// @FIXME getUTCHours depends on server and client settings
	var hours = currently.getUTCHours();
	if (hours < 10) {
		hours = "0" + hours;
	}
	var minutes = currently.getMinutes();
	if (minutes < 10) {
		minutes = "0" + minutes;
	}
	var seconds = currently.getSeconds();
	if (seconds < 10) {
		seconds = "0" + seconds;
	}
	var formTime = hours + ':' + minutes + ':' + seconds;
	setWindowState(formTime, 'You are now working: ' + formTime);
	lastTimer = window.setTimeout('updateTimer('+start+')', 1000);
}

function setProjects(projects)
{
	if (projects == null) {
		alert('Sorry, but the project list could not be loaded');
	}
	
	$.each(projects, function(index, theEntry) {
		$('#projects').append(
			$('<option></option>').val(theEntry.pct_ID).html(theEntry.pct_name + " ("+theEntry.knd_name+")")
		);
	});
	$('#projects').selectmenu('refresh');
}

function setTasks(tasks)
{
	if (tasks == null) {
		alert('Sorry, but the task list could not be loaded');
	}
	
	$.each(tasks, function(index, theEntry) {
		$('#tasks').append(
			$('<option></option>').val(theEntry.evt_ID).html(theEntry.evt_name)
		);
	});
	$('#tasks').selectmenu('refresh');
}


function validateStartStopButton()
{
	var mySlider   = $('#recorder');
	var myProjects = $('#projects');
	var myTasks    = $('#tasks');
	
	// switch the start/stop button
	if (myTasks.val() != '' && myProjects.val() != '') {
		mySlider.slider('enable');
		mySlider.parent().find('span.ui-icon').removeClass('ui-icon-alert').addClass('ui-icon-check');
	} else {
		mySlider.slider('disable');
		mySlider.parent().find('span.ui-icon').removeClass('ui-icon-check').addClass('ui-icon-alert');
	}
	
	mySlider.slider('refresh');
}

function validateLoginButton()
{
	var myButton = $('#btnLogin');
	if ($('#username').val() != '' && $('#password').val() != '') {
		myButton.button('enable');
		myButton.parent().find('span.ui-icon').removeClass('ui-icon-alert').addClass('ui-icon-check');
		return true;
	}
	
	myButton.button('disable');
	myButton.parent().find('span.ui-icon').removeClass('ui-icon-check').addClass('ui-icon-alert');
	return false;
}

/**
 * The global Kimai object gives access to the JSON API.
 */
var Kimai = 
{
	apiKey: '',
	jsonApi: '',
	
	/**
	 * Sets the URL where the JSON API is located.
	 *
	 * @param string url the url of the json api
	 */
	setJsonApi: function(url) {
		this.jsonApi = url;
	},
	
	/**
	 * Creates a JSON API Call ID.
	 * Actually, the API does not care about it yet, we could also use 
	 * a hardcoded string here, but its better nicer that way...
	 * 
	 * @return string
	 */
	getApiCallID: function(apiCall) {
		return apiCall; // @TODO improve me
	},
	
	/**
	 * Authenticates the user account and returns the API key on success.
	 * If user could not be authenticated null will be returned.
	 * 
	 * @param string uid the username
	 * @param string pwd the password
	 * @return boolean
	 */
	authenticate: function(uid, pwd) {
		if (this.jsonApi == '' || uid == '' || pwd == '') {
			return false;
		}
		
		var key = null;
		$.ajax({
			type: 'POST',
			url: this.jsonApi,
			data: {method: 'authenticate', id: Kimai.getApiCallID('authenticate'), params: {username: uid, password: pwd}},
			async: false,
			success: function(data) {
				if (typeof data != 'undefined' && typeof data.result != 'undefined' && data.result != '') {
					key = data.result;
				}
			}
		});
		
		if (key !== null) {
			this.apiKey = key;
			return true;
		}
		
		return false;
	},
	
	/**
	 * Retrieve a list ob project objects.
	 * 
	 * @return array
	 */
	getProjects: function() {
		return this._doApiCall('getProjects');
	},
	
	/**
	 * Returns a list of all tasks for the current user.
	 * 
	 * @return array
	 */
	getTasks: function() {
		return this._doApiCall('getTasks');
	},
	
	/**
	 * Returns null if no record is processing or an array if one is running.
	 * 
	 * @return null|array
	 */
	getRunningTask: function() {
		var result = this._doApiCall('getActiveTask');
		// only if a last entry is existing and its not stopped, return it
		if (typeof result.zef_out != 'undefined' && result.zef_out == 0) {
			return result;
		}
		return null;
	},
	
	/**
	 * Starts the given task within the project.
	 * 
	 * @param integer prjId
	 * @param integer taskId
	 * @return boolean
	 */
	start: function(prjId, taskId) {
		var result = null;
		$.ajax({
			type: 'POST',
			url: this.jsonApi,
			data: {method: 'startRecord', id: Kimai.getApiCallID('startRecord'), params: {apiKey: this.apiKey, projectId: prjId, eventId: taskId}},
			async: false,
			success: function(data) {
				if (typeof data != 'undefined' && typeof data.result != 'undefined') {
					result = data.result;
				}
			}
		});
		return result;
	},
	
	/**
	 * Stops the current running task.
	 * 
	 * @return boolean
	 */
	stop: function() {
		var stopped = this._doApiCall('stopRecord');
		return stopped;
	},
	
	/**
	 * Calls the JSON Api method and returns the result.
	 * This method is only meant for calls with no parameters.
	 * 
	 * @param string apimethod
	 * @access private
	 * @return mixed
	 */
	 _doApiCall: function(apimethod) {
		var result = null;
		$.ajax({
			type: 'POST',
			url: this.jsonApi,
			data: {method: apimethod, id: Kimai.getApiCallID(apimethod), params: {apiKey: this.apiKey}},
			async: false,
			success: function(data) {
				if (typeof data != 'undefined' && typeof data.result != 'undefined') {
					result = data.result;
				}
			}
		});
		return result;
	 }
};

