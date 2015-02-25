var $body = $('body');


$(function() {

	var $easyvoteEducationContentContainer = $('#easyvoteeducation-content');

	// Load dashboard on startup
	if ($easyvoteEducationContentContainer.length) {
		if (document.location.hash) {
			// If a specific action is requested, load it
			var hash = document.location.hash;
			var actionName = hash.substr(1);
			var actionUri = EasyvoteEducationActionUris[actionName];
			EasyvoteEducation.loadAjaxContent(actionUri).done(function(data) {
				$easyvoteEducationContentContainer.html(data);
				Easyvote.bindPostalCodeSelection();
			});
		} else {
			// Fall back to dashboard
			EasyvoteEducation.loadAjaxContent(EasyvoteEducationActionUris.dashboard).done(function(data) {
				$easyvoteEducationContentContainer.html(data);
				Easyvote.bindPostalCodeSelection();
			});
		}
	}

	// Implement main navigation of app
	$body.on('click', "a[data-role='ajax']", function(e) {
		e.preventDefault();
		var $this = $(this);
		var currentUrl = document.location.href.match(/(^[^#]*)/)[0];
		var actionName = $this.attr('data-actionname');
		var hash = '#' + actionName;
		if (Modernizr.history) {
			window.history.pushState(null, '', currentUrl + hash);
		}
		var actionUri = EasyvoteEducationActionUris[actionName];
		EasyvoteEducation.loadAjaxContent(actionUri).done(function(data) {
			$easyvoteEducationContentContainer.html(data);
			Easyvote.bindPostalCodeSelection();
			Easyvote.bindModals();
		});
	});

	// Implement object-based AJAX actions
	$body.on('click', "a[data-role='ajaxobject']", function(e) {
		e.stopPropagation();
		e.preventDefault();
		var $this = $(this);
		//var currentUrl = document.location.href.match(/(^[^#]*)/)[0];
		var actionName = $this.attr('data-actionname');
		var objectName = $this.attr('data-object');
		var objectUid = $this.attr('data-uid');
		var confirmAction = $this.attr('data-confirm') === 'true';
		if (confirmAction) {
			// open a modal and wait for confirmation to continue
			Easyvote.displayModal($this.parent().find('.ajaxobject-confirm').html(), function(status) {
				var actionUri = EasyvoteEducationActionUris[actionName];
				EasyvoteEducation.loadAjaxObjectContent(objectName, objectUid, actionUri).done(function (data) {
					jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
					if (jsonData.hasOwnProperty('redirectToAction')) {
						EasyvoteEducation.loadAction(jsonData.redirectToAction);
					} else {
						$easyvoteEducationContentContainer.html(data);
						Easyvote.bindPostalCodeSelection();
					}
				});
			})
		} else {
			// no confirmation needed, call the action right away
			var actionUri = EasyvoteEducationActionUris[actionName];
			EasyvoteEducation.loadAjaxObjectContent(objectName, objectUid, actionUri).done(function (data) {
				jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
				if (jsonData.hasOwnProperty('redirectToAction')) {
					EasyvoteEducation.loadAction(jsonData.redirectToAction);
				} else {
					$easyvoteEducationContentContainer.html(data);
					Easyvote.bindPostalCodeSelection();
				}
			});		}
	});

	// React on history changes
	if (Modernizr.history) {
		window.onpopstate = function (e) {
			var hash = document.location.hash;
			var actionName = hash.substr(1);
			var actionUri = EasyvoteEducationActionUris[actionName];
			EasyvoteEducation.loadAjaxContent(actionUri).done(function(data) {
				$easyvoteEducationContentContainer.html(data);
			});
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

	postForm: function(data, uri) {
		return $.ajax({
			type: "POST",
			url: uri,
			data: data
		});
	}


};