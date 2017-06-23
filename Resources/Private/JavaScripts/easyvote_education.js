var $body = $('body');

$(function() {

	// Move an element up and down in DOM
	$.fn.moveUp = function(callback) {
		$.each(this, function() {
			$(this).after($(this).prev());
		});
		callback();
	};
	$.fn.moveDown = function(callback) {
		$.each(this, function() {
			$(this).before($(this).next());
		});
		callback();
	};

	// Enforce a number
	$body.on('input', '.enforce-number', function() {
		this.value = this.value.replace(/[^0-9.]/g, '');
		this.value = this.value.replace(/(\..*)\./g, '$1');
	});

	var $easyvoteEducationContentContainer = $('#easyvoteeducation-content');
	var requestedAction = $easyvoteEducationContentContainer.attr('data-action');

	// Load managePanels on startup
	if ($easyvoteEducationContentContainer.length) {
		if (document.location.hash) {
			EasyvoteEducation.callHashRequestedAction();
		} else if (requestedAction) {
			EasyvoteEducation.loadAction(requestedAction);
		} else {
			// Fall back to managePanels
			EasyvoteEducation.loadAction('managePanels');
		}
	}

	// AJAX-based general actions such as main navigation
	$body.on('click', "a[data-role='generalaction']", function(e) {
		e.preventDefault();
		var $this = $(this);
		var actionName = $this.attr('data-actionname');
		var openPanel = $this.attr('data-openpanel');
		EasyvoteEducation.loadAction(actionName, null, openPanel);
	});

	// AJAX-based voting actions
	$body.on('click', "[data-role='votingaction']", function(e) {
		var $this = $(this);
		var targetContainer = null;
		e.preventDefault();
		if ($this.prop('tagName') === 'BUTTON') {
			$this.closest('ul').find('button.votingOption').attr('disabled', 'disabled');
			$this.attr('disabled', 'disabled').addClass('selected');
			targetContainer = '#votecast-message';
		}
		var actionarguments = $this.attr('data-actionarguments');
		var target = $this.attr('data-target');
		if (target) {
			targetContainer = target;
		}
		// set a cookie about voteCast to prevent double-casting (is checked again on server side, so not security relevant)
		$.cookie('easyvoteeducation-voteCast', actionarguments);

		$.ajax(
			{
				url: ['/?eID=castVote&arguments=', actionarguments].join('')
			}
		).done(function(response) {

		});
	});

	// AJAX-based actions for panels
	$body.on('click', "a[data-role='panelaction']", function(e) {
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
			Easyvote.displayModal($this.next('.ajaxobject-confirm').html(), function(status) {
				EasyvoteEducation.performAjaxObjectAction(actionName, objectName, objectUid);
			})
		} else {
			// no confirmation needed, call the action right away
			EasyvoteEducation.performAjaxObjectAction(actionName, objectName, objectUid);
		}
	});

	// Implement AJAX actions for generic object actions (voting, votingOption)
	$body.on('click', "[data-role='genericobjectaction']", function(e) {
		e.stopPropagation();
		e.preventDefault();
		var $this = $(this);
		var actionName = $this.attr('data-actionname');
		var objectName = $this.attr('data-object');
		var objectUid = $this.attr('data-uid');
		var submitValueFrom = $this.attr('data-submitvaluefrom');
		var targetElement = '.' + objectName + '-item-' + objectUid;
		var pluginPrefix = $this.attr('data-pluginprefix');

		var confirmAction = $this.attr('data-confirm') === 'true';
		if (confirmAction) {
			// open a modal and wait for confirmation to continue
			Easyvote.displayModal($this.next('.ajaxobject-confirm').html(), function(selection) {
				EasyvoteEducation.performAjaxObjectAction(actionName, objectName, objectUid, targetElement, selection, null, pluginPrefix);
			})
		} else if (submitValueFrom) {
			// Submit value from another field with request
			var value = $(submitValueFrom).val();
			EasyvoteEducation.performAjaxObjectAction(actionName, objectName, objectUid, targetElement, value);
		} else {
			// no confirmation needed, call the action right away
			EasyvoteEducation.performAjaxObjectAction(actionName, objectName, objectUid, targetElement, null, function() {
			});
		}
	});

	// Move generic objects
	$body.on('click', "[data-role='movegenericobject']", function(e) {
		e.stopPropagation();
		e.preventDefault();
		var $this = $(this);
		var actionName = $this.attr('data-actionname');
		var objectName = $this.attr('data-object');
		var objectUid = $this.attr('data-uid');
		var parentObjectName = $this.attr('data-parentobject');
		var parentObjectUid = $this.attr('data-parentuid');
		var direction = $this.attr('data-direction');
		var itemSelector = '.' + objectName + '-item-' + objectUid;

		if (direction == 'up') {
			$(itemSelector).moveUp(function() {
				EasyvoteEducation.persistSorting(actionName, objectName, parentObjectName, parentObjectUid);
			});
		} else {
			$(itemSelector).moveDown(function() {
				EasyvoteEducation.persistSorting(actionName, objectName, parentObjectName, parentObjectUid);
			});
		}
	});

	// React on history changes
	if (Modernizr.history && $easyvoteEducationContentContainer.length) {
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
				EasyvoteEducation.handleError(jsonData);
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
				EasyvoteEducation.handleError(jsonData);
			}
		});
	});

	// Update voting
	$body.on('submit', '.editVoting', function(e) {
		e.preventDefault();
		$this = $(this);
		var objectName = $this.attr('data-objectname');
		var objectUid = $this.attr('data-objectuid');
		var targetSelector = '.' + objectName + '-item-' + objectUid;
		EasyvoteEducation.postForm($this.serialize(), EasyvoteEducationActionUris['updateVoting']).done(function(data) {
			jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
			if (jsonData.hasOwnProperty('status') && jsonData.status == 200) {
				EasyvoteEducation.performAjaxObjectAction('editVoting', objectName, objectUid, targetSelector, null, function() {
					// open the edited voting
					EasyvoteEducation.openVoting(targetSelector);
				});
			} else {
				EasyvoteEducation.handleError(jsonData);
			}
		});
	});

	// Update votingOption
	$body.on('submit', '.editVotingOption', function(e) {
		e.preventDefault();
		$this = $(this);
		var data = new FormData();
		$this.serializeArray().forEach(function(field) {
			data.append(field.name, field.value)
		});
		var $fileUploadField = $this.find('.votingOption-image').first();
		var files = $fileUploadField.prop('files');
		if (files.length) {
			data.append('tx_easyvoteeducation_managepanels[votingOption][image]', files[0]);
		}
		var parentObjectName = $this.attr('data-parentobjectname');
		var parentObjectUid = $this.attr('data-parentobjectuid');
		var targetSelector = '.' + parentObjectName + '-item-' + parentObjectUid;
		EasyvoteEducation.postFormWithFileUpload(data, EasyvoteEducationActionUris['updateVotingOption']).done(function(data) {
			jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
			if (jsonData.hasOwnProperty('status') && jsonData.status == 200) {
				EasyvoteEducation.performAjaxObjectAction('editVoting', parentObjectName, parentObjectUid, targetSelector, null, function() {
					// open voting
					EasyvoteEducation.openVoting(targetSelector);
				});
			} else {
				EasyvoteEducation.handleError(jsonData);
			}
		});
	});

	// votingDuration
	$body.on('input change', '.votingDuration-slider', function() {
		var $container = $(this).closest('.editVoting');
		var votingDuration = this.value;
		$container.find('.votingDuration-value span').text(votingDuration);
		$container.find('.votingDuration').val(votingDuration + '');
	});

	$body.on('change', '.votingDuration-infinite', function() {
		var checked = this.checked;
		var $container = $(this).closest('.editVoting');
		var votingDuration = this.value;
		if (checked) {
			$container.find('.votingDuration-display').hide();
			$container.find('.votingDuration').val('0');
		} else {
			$container.find('.votingDuration-display').show();
			$container.find('.votingDuration-value span').text('60');
			$container.find('.votingDuration').val('60');
			$container.find('.votingDuration-slider').val('60');
		}
	});

	// votingOption image
	$body.on('change', '.votingOption-image', function() {
		var $this = $(this);
		var imagePreviewSelector = $this.parent().find('.votingOption-image-preview').attr('id');
		Easyvote.readFile(this, '#' + imagePreviewSelector);
	});

	// Trigger file upload selector on clicking button
	$body.on('click', '.triggeruploadbutton', function() {
		var $this = $(this);
		$($this.attr('data-target')).trigger('click');
	});

	// Trigger file upload selector on clicking current picture
	$body.on('click', '.votingOption-image-preview', function(e) {
		e.stopPropagation();
		var $this = $(this);
		$($this.attr('data-target')).trigger('click');
	});

	// Update voting title dynamically
	$body.on('input', '.voting-title', function(e) {
		var $this = $(this);
		$this.closest('.voting-item').find('.box-header h2').text($this.val());
	});

	// Save all forms (voting and votingOptions)
	$body.on('click', '.voting-save', function(e) {
		e.stopPropagation();
		$this = $(this);
		disableReopenExpandableContentBox = true;
		$this.closest('.box-content').find('form').trigger('submit');
	});

	/**
	 * Functions for party administrators
	 */
	$body.on('click', '#cancelPanelInvitationsFilter', function() {
		var $panelInvitationsFilter = $('#panelInvitationsFilter');
		$panelInvitationsFilter.trigger('reset');
		$('#panelInvitationsFilter-status').find('option:selected').prop('selected', false);
		$panelInvitationsFilter.trigger('submit');
	});

});


var EasyvoteEducation = {

	/**
	 * Load content by AJAX
	 * @param uri
	 * @returns {*}
	 */
	loadAjaxContent: function(uri) {
		return $.ajax({
			url: uri
		});
	},

	/**
	 * Load an action and write its result to a container
	 *
	 * @param actionName Name of the action, an URI with the same name must be defined
	 * @param contentContainerSelector
	 * @param panelUid
	 */
	loadAction: function(actionName, contentContainerSelector, panelUid) {
		if (typeof(contentContainerSelector) === 'string') {
			var $container = $(contentContainerSelector);
		} else {
			var $container = $('#easyvoteeducation-content');
		}
		EasyvoteEducation.loadAjaxContent(EasyvoteEducationActionUris[actionName]).done(function(data) {
			jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
			if (jsonData.hasOwnProperty('content')) {
				$container.html(jsonData.content);
			} else {
				EasyvoteEducation.handleError(jsonData);
			}
			EasyvoteEducation.pushHistoryState(actionName);
			Easyvote.bindPostalCodeSelection();
			EasyvoteEducation.bindPartySelection();
			EasyvoteGeneral.bindDateTime();
			Easyvote.bindToolTips();
			if (panelUid) {
				EasyvoteEducation.openPanel(panelUid);
			}
		});
	},

	/**
	 * Load a votingaction and write its result to a container
	 *
	 * @param actionArguments Name of the action, an URI with the same name must be defined
	 * @param contentContainerSelector
	 */
	loadVotingAction: function(actionArguments, contentContainerSelector) {
		if (typeof(contentContainerSelector) === 'string') {
			var $container = $(contentContainerSelector);
		} else {
			var $container = $('#easyvoteeducation-content');
		}
		var actionUri = '/routing/votings/' + actionArguments + '?L=' + EasyvoteLanguage;
		EasyvoteEducation.loadAjaxContent(actionUri).done(function(data) {
			jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
			if (jsonData.hasOwnProperty('content')) {
				$container.html(jsonData.content);
				EasyvoteEducation.disableVotingIfAlreadyVoted();
			} else {
				EasyvoteEducation.handleError(jsonData);
			}
		});
	},

	/**
	 * Load a votingaction and write its result to a container
	 *
	 * @param url Name of the action, an URI with the same name must be defined
	 * @param contentContainerSelector
	 */
	loadStaticVotingAction: function(url, contentContainerSelector) {
		if (typeof(contentContainerSelector) === 'string') {
			var $container = $(contentContainerSelector);
		} else {
			var $container = $('#easyvoteeducation-content');
		}
		EasyvoteEducation
			.loadAjaxContent(url)
			.done(function(data) {
				$container.html(data);
			});
	},

	/**
	 * @param objectName
	 * @param objectUid
	 * @param uri
	 * @param selection
	 * @param pluginPrefix
	 * @returns {*}
	 */
	loadAjaxObjectContent: function(objectName, objectUid, uri, selection, pluginPrefix) {
		if (!pluginPrefix) {
			var pluginPrefix = 'tx_easyvoteeducation_managepanels';
		}
		var data = {};
		data[pluginPrefix] = {};
		data[pluginPrefix][objectName] = objectUid;
		if (selection) {
			data[pluginPrefix]['selection'] = selection;
		}
		return $.ajax({
			type: "POST",
			url: uri,
			data: data
		});
	},

	/**
	 * @param actionName
	 * @param objectName
	 * @param objectUid
	 * @param contentContainerSelector
	 * @param selection
	 * @param pluginPrefix
	 * @param callback
	 */
	performAjaxObjectAction: function(actionName, objectName, objectUid, contentContainerSelector, selection, callback, pluginPrefix) {
		if (typeof(contentContainerSelector) === 'string') {
			var $container = $(contentContainerSelector);
		} else {
			var $container = $('#easyvoteeducation-content');
		}
		var actionUri = EasyvoteEducationActionUris[actionName];
		EasyvoteEducation.loadAjaxObjectContent(objectName, objectUid, actionUri, selection, pluginPrefix).done(function (data) {
			jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
			if (jsonData.hasOwnProperty('redirectToAction')) {
				EasyvoteEducation.loadAction(jsonData.redirectToAction, contentContainerSelector);
			} else if (jsonData.hasOwnProperty('removeElement')) {
				// Remove the target container, e.g. after a delete action
				$container.slideUp(200, function() {
					$container.remove();
					if (callback) {
						callback();
					}
				});
			} else if (jsonData.hasOwnProperty('reloadVotings')) {
				EasyvoteEducation.performAjaxObjectAction('listForCurrentUser', 'panel', jsonData.reloadVotings, '.votings-content', null, callback);
			} else if (jsonData.hasOwnProperty('reloadPanelInvitations')) {
				EasyvoteEducation.performAjaxObjectAction('listPanelInvitationsForCurrentUser', 'panel', jsonData.reloadPanelInvitations, '.panelinvitations-content', null, callback);
			} else if (jsonData.hasOwnProperty('reloadPanelParticipations')) {
				EasyvoteEducation.loadAction('panelParticipations');
			} else if (jsonData.hasOwnProperty('reloadVotingOptions')) {
				// Remove the target container, e.g. after a delete action
				EasyvoteEducation.performAjaxObjectAction('listForVoting', 'voting', jsonData.reloadVotingOptions, '.votingOptions-content', null, callback);
			} else if (jsonData.hasOwnProperty('status') && jsonData.status !== 200) {
				EasyvoteEducation.handleError(jsonData);
			} else {
				$container.html(jsonData.content);
				Easyvote.bindPostalCodeSelection();
				EasyvoteEducation.bindPartySelection();
				EasyvoteGeneral.bindDateTime();
				Easyvote.bindToolTips();
				if (callback) {
					callback();
				}
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

	postFormWithFileUpload: function(data, uri) {
		return $.ajax({
			type: 'POST',
			url: uri,
			data: data,
			cache: false,
			contentType: false,
			processData: false
		});

	},

	/**
	 * Persist the sorting of an objectStorage from its ordner in the DOM
	 * @param actionName
	 * @param objectName
	 */
	persistSorting: function(actionName, objectName, parentObjectName, parentObjectUid) {
		var $objects = $('.' + objectName + '-item');
		var sorting = 1;
		var data = {};
		data['tx_easyvoteeducation_managepanels'] = {};
		data['tx_easyvoteeducation_managepanels']['sorting'] = {};
		data['tx_easyvoteeducation_managepanels'][parentObjectName] = parentObjectUid;
		$objects.each(function() {
			var objectUid = $(this).attr('data-uid');
			data['tx_easyvoteeducation_managepanels']['sorting'][objectUid] = sorting;
			sorting++;
		});
		EasyvoteEducation.postForm(data, EasyvoteEducationActionUris[actionName]).done(function(data) {
			jsonData = JSON && JSON.parse(data) || $.parseJSON(data);
			if (jsonData.hasOwnProperty('status') && jsonData.status !== 200) {
				EasyvoteEducation.handleError(jsonData);
			}
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
		// If a specific action is requested, load it
		// hash might be: "actionName" or "actionName/objectName/objectUid"
		var hashData = document.location.hash.substr(1).split('/');
		if (hashData.length > 1) {
			// ajax object action
			var allowedActions = ['edit', 'editVotings', 'editPanelInvitations', 'execute'];
			if ($.inArray(hashData[0], allowedActions) !== -1) {
				var actionName = hashData[0];
				var objectName = hashData[1];
				var objectUid = hashData[2];
				EasyvoteEducation.performAjaxObjectAction(actionName, objectName, objectUid, null, null, function() {
					EasyvoteGeneral.bindDateTime();
					Easyvote.bindToolTips();
				})
			} else {
				// disallowed action, fall back to managePanels
				EasyvoteEducation.loadAction('managePanels');
			}
		} else {
			// action without an object involved
			// hashData[0] --> actionName
			EasyvoteEducation.loadAction(hashData[0])
		}
	},

	timer: function() {
		count = count - 1;
		if (count == 0) {
			clearInterval(counter);
			$('#stopVotingButton').trigger('click');
		}
		$('.timer').html(count);
	},

	/**
	 * Check if there is a cookie for the last vote cast
	 * If there is and a votingOption with the same ID is found on the page, prevent voting again
	 * A similar check is done on server-side to prevent casting double votes
	 */
	disableVotingIfAlreadyVoted: function() {
		if ($.cookie('easyvoteeducation-voteCast') !== null) {
			var usedVotingOption = $('[data-actionarguments="' + $.cookie('easyvoteeducation-voteCast') + '"]');
			if (usedVotingOption.prop('tagName') === 'BUTTON') {
				usedVotingOption.closest('ul').find('button.votingOption').attr('disabled', 'disabled');
				usedVotingOption.attr('disabled', 'disabled').addClass('selected');
			}
		}
	},

	/**
	 * Reopen voting after saving voting or votingOption
	 *
	 * @param targetSelector
	 */
	openVoting: function(targetSelector) {
		if (disableReopenExpandableContentBox === false) {
			$(targetSelector).find('.toggle i').trigger('click');
		}
	},

	/**
	 * Open a panel
	 *
	 * @param panelUid
	 */
	openPanel: function(panelUid) {
		$('.panel-' + panelUid).find('.toggle i').trigger('click');
		Easyvote.scrollToElement('.panel-' + panelUid);
	},

	/**
	 * Get parties that are available for creating a panel invitation
	 * There can be only one panel invitation for each party
	 */
	bindPartySelection: function() {
		if (typeof(panelUid) != 'undefined') {
			var $partySelector = $('.partyForCurrentPanelSelection');
			$partySelector.select2({
				multiple: true,
				//maximumSelectionSize: 2,
				ajax: {
					url: '/routing/getavailablepartiesforpanel/' + panelUid + '?L=' + EasyvoteLanguage + '&id=' + EasyvoteHomePid,
					dataType: 'json',
					data: function (term, page) {
						return {
							q: term
						};
					},
					results: function (data, page) {
						return {results: data.results};
					}
				},
				initSelection: function (element, callback) {
				},
				dropdownCssClass: "bigdrop",
				escapeMarkup: function (m) {
					return m;
				}
			});
		}
	},

	bindPartyMemberSelection: function() {
		var $partyMemberSelection = $('.partyMemberSelection');
		$partyMemberSelection.select2({
			ajax: {
				url: '/routing/getpartymembers',
				dataType: 'json',
				data: function (term, page) {
					return {
						q: term // search term
					};
				},
				results: function (data, page) {
					return {results: data.results};
				}
			},
			initSelection: function (element, callback) {
			},
			dropdownCssClass: "bigdrop",
			escapeMarkup: function (m) {
				return m;
			}
		}).on('change', function(e) {
			$(this).parents('form').find('button').show();
		});
	},

	/**
	 * Get panel invitations for the party of the current party administrator user
	 * and open a single panel invitation if requested
	 *
	 * @param openPanelInvitation
	 */
	getPanelInvitations: function(openPanelInvitation) {
		EasyvoteGeneral.getData('/routing/panelinvitations?L=' + EasyvoteLanguage).done(function(data) {
			$('.panel-invitations').html(data);
			if (openPanelInvitation) {
				var elementId = '#panelInvitation-item-' + openPanelInvitation;
				$(elementId).find('.toggle i').trigger('click');
				Easyvote.scrollToElement(elementId);
			}
			EasyvoteEducation.bindPartyMemberSelection();
			Easyvote.bindToolTips();
		});
	},

	/**
	 * Handles an error and displays a Flash Message
	 *
	 * @param data
	 */
	handleError: function(data) {
		if (data.hasOwnProperty('status')) {
			Easyvote.displayFlashMessage('<h2>Error ' + data.status + ':</h2><p>'+ data.reason + '</p>');
		} else {
			Easyvote.displayFlashMessage('Unhandled error');
		}
	}

};