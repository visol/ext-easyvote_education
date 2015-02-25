var $body = $('body');

$(function() {

	var $easyvoteEducationContentContainer = $('#easyvoteeducation-content');

	// Load dashboard on startup
	if ($easyvoteEducationContentContainer.length) {
		if (document.location.hash) {
			EasyvoteEducation.callHashRequestedAction();
		} else {
			// Fall back to dashboard
			EasyvoteEducation.loadAction('dashboard');
		}
	}

	// Implement main navigation of app
	$body.on('click', "a[data-role='ajax']", function(e) {
		e.preventDefault();
		var $this = $(this);
		var actionName = $this.attr('data-actionname');
		EasyvoteEducation.loadAction(actionName);
	});

	// Implement object-based AJAX actions
	$body.on('click', "a[data-role='ajaxobject']", function(e) {
		e.stopPropagation();
		e.preventDefault();
		var $this = $(this);
		var actionName = $this.attr('data-actionname');
		var objectName = $this.attr('data-object');
		var objectUid = $this.attr('data-uid');
		EasyvoteEducation.pushHistoryState(actionName, objectName, objectUid);

		var confirmAction = $this.attr('data-confirm') === 'true';
		if (confirmAction) {
			// open a modal and wait for confirmation to continue
			Easyvote.displayModal($this.parent().find('.ajaxobject-confirm').html(), function(status) {
				EasyvoteEducation.performAjaxObjectAction(actionName, objectName, objectUid);
			})
		} else {
			// no confirmation needed, call the action right away
			EasyvoteEducation.performAjaxObjectAction(actionName, objectName, objectUid);
		}
	});

	// React on history changes
	if (Modernizr.history) {
		window.onpopstate = function () {
			EasyvoteEducation.callHashRequestedAction();
		};
	}

	// Create panel
	$body.on('submit', '#newPanel', function(e) {
		e.preventDefault();
		EasyvoteEducation.postForm($(this).serialize(), EasyvoteEducationActionUris['create']).done(function(data) {
			jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
			if (jsonData.hasOwnProperty('redirectToAction')) {
				EasyvoteEducation.loadAction(jsonData.redirectToAction);
			} else {
				// todo meaningful and usable error
				alert('Fehler!');
			}
		});
	});

	// Update panel
	$body.on('submit', '#editPanel', function(e) {
		e.preventDefault();
		EasyvoteEducation.postForm($(this).serialize(), EasyvoteEducationActionUris['update']).done(function(data) {
			jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
			if (jsonData.hasOwnProperty('redirectToAction')) {
				EasyvoteEducation.loadAction(jsonData.redirectToAction);
			} else {
				// todo meaningful and usable error
				alert('Fehler!');
			}
		});
	});

	// Panel toolbar
	$body.on('click', '.panel-item', function(e) {
		var $this = $(this);
		var $toolbar = $this.find('.panel-item-toolbar');
		$toolbar.slideToggle(100);
	});

	// Datepicker field
	if ($.fn.datetimepicker) {
		$('.easyvoteEducation-date').each(function() {
			var $this = $(this);
			// stop javascript datepicker, if browser supports type="date" or "datetime-local" or "time"
			if ($this.prop('type') === 'date' || $this.prop('type') === 'datetime-local' || $this.prop('type') === 'time') {
				if ($this.data('datepicker-force')) {
					// rewrite input type
					$this.prop('type', 'text');
				} else {
					// stop js datepicker
					return;
				}
			}

			var datepickerStatus = true;
			var timepickerStatus = true;
			if ($this.data('datepicker-settings') === 'date') {
				timepickerStatus = false;
			} else if ($this.data('datepicker-settings') === 'time') {
				datepickerStatus = false;
			}

			// create datepicker
			$this.datetimepicker({
				format: $this.data('datepicker-format'),
				timepicker: timepickerStatus,
				datepicker: datepickerStatus,
				lang: 'en',
				i18n:{
					en:{
						months: $this.data('datepicker-months').split(','),
						dayOfWeek: $this.data('datepicker-days').split(',')
					}
				}
			});
		});
	}

});


var EasyvoteEducation = {

	loadAjaxContent: function(uri) {
		return $.ajax({
			url: uri
		});
	},

	loadAction: function(actionName) {
		var $easyvoteEducationContentContainer = $('#easyvoteeducation-content');
		EasyvoteEducation.loadAjaxContent(EasyvoteEducationActionUris[actionName]).done(function(data) {
			$easyvoteEducationContentContainer.html(data);
			EasyvoteEducation.pushHistoryState(actionName);
			Easyvote.bindPostalCodeSelection();
		});
	},

	loadAjaxObjectContent: function(objectName, objectUid, uri) {
		var data = {};
		data['tx_easyvoteeducation_managepanels'] = {};
		data['tx_easyvoteeducation_managepanels'][objectName] = objectUid;
		return $.ajax({
			type: "POST",
			url: uri,
			data: data
		});
	},

	performAjaxObjectAction: function(actionName, objectName, objectUid) {
		var $easyvoteEducationContentContainer = $('#easyvoteeducation-content');
		var actionUri = EasyvoteEducationActionUris[actionName];
		EasyvoteEducation.loadAjaxObjectContent(objectName, objectUid, actionUri).done(function (data) {
			jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
			if (jsonData.hasOwnProperty('redirectToAction')) {
				EasyvoteEducation.loadAction(jsonData.redirectToAction);
			} else {
				$easyvoteEducationContentContainer.html(jsonData.content);
				Easyvote.bindPostalCodeSelection();
			}
		});

	},

	postForm: function(data, uri) {
		return $.ajax({
			type: "POST",
			url: uri,
			data: data
		});
	},

	pushHistoryState: function(actionName, objectName, objectUid) {
		if (Modernizr.history) {
			var currentUrl = document.location.href.match(/(^[^#]*)/)[0];
			var hash = '#' + actionName;
			if (objectName) {
				hash += '/' + objectName + '/' + objectUid;
			}
			window.history.pushState(null, '', currentUrl + hash);
		}
	},

	callHashRequestedAction: function() {
		var $easyvoteEducationContentContainer = $('#easyvoteeducation-content');
		// If a specific action is requested, load it
		// hash might be: "actionName" or "actionName/objectName/objectUid"
		var hashData = document.location.hash.substr(1).split('/');
		if (hashData.length > 1) {
			// ajax object action
			var allowedActions = ['edit', 'editVotings'];
			if ($.inArray(hashData[0], allowedActions) !== -1) {
				var actionName = hashData[0];
				var objectName = hashData[1];
				var objectUid = hashData[2];
				EasyvoteEducation.performAjaxObjectAction(actionName, objectName, objectUid)
			} else {
				// disallowed action, fall back to dashboard
				EasyvoteEducation.loadAction('dashboard');
			}
		} else {
			// action without an object involved
			// hashData[0] --> actionName
			EasyvoteEducation.loadAction(hashData[0])
		}
	}


};