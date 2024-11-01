/**
 * Used global objects:
 * - ajaxurl
 * - jQuery
 * - SwiftControl
 */
(function ($) {
	var statusButton = document.querySelector('.active-items-box .saved-status');
	var isRequesting = false;
	var elms = {};
	var $elms = {};
	var ajax = {};

	/**
	 * Call the main functions here.
	 */
	function init() {
		setupElms();
		setupSortable();
		setupSettingsArea();
		setupIconPicker();
		setupEvents();
	}

	/**
	 * Setup re-usable elements.
	 */
	function setupElms() {
		// jQuery elements.
		$elms.activeItems = $('#active-items');
	}

	/**
	 * Sortable setup for both active & available widgets.
	 */
	function setupSortable() {
		$elms.activeItems.sortable({
			connectWith: '.widget-items',
			receive: function (e, ui) {
				var isReadonly = ui.item[0].classList.contains('edit-mode') ? false : true;
				setWidgetTextFieldState(ui.item[0], isReadonly);
			},
			update: function (e, ui) {
				ajax.changeWidgetsOrder();
			}
		});

		$('#available-items').sortable({
			connectWith: '.widget-items',
			receive: function (e, ui) {
				setWidgetTextFieldState(ui.item[0], true);
			}
		});
	}

	/**
	 * Register necessary elements which are related to settings area.
	 */
	function setupSettingsArea() {
		$elms.page = $('.swift-control-page');
		$elms.activeItemsArea = $('.left-section');

		moveSettingsArea();
	}

	/**
	 * Move general settings area (an area that contains "Settings" metabox).
	 *
	 * In desktop, this area will be under active items,
	 * while in mobile, this area will be under both active & available items.
	 */
	function moveSettingsArea() {
		if (window.innerWidth < 992) {
			$('.general-settings-area').appendTo($elms.page);
		} else {
			$('.general-settings-area').appendTo($elms.activeItemsArea);
		}
	}

	/**
	 * Setup icon picker.
	 *
	 * @param {HTMLElement|jQueryElement} trigger The icon picker trigger (optional).
	 */
	function setupIconPicker(trigger) {
		var $trigger;

		if (trigger) {
			if (trigger.tagName) {
				$trigger = $(trigger);
			} else {
				$trigger = trigger;
			}

			$trigger.iconPicker();
		} else {
			// If trigger param is omitted.
			// $(".swift-control-settings .icon-picker").iconPicker();
		}
	}

	/**
	 * Register events.
	 */
	function setupEvents() {
		// General events.
		window.addEventListener('resize', function (e) {
			moveSettingsArea();
		});

		document.querySelector('.widget-items.active-items').addEventListener('keydown', function (e) {
			if (e.key === 'Escape' || e.key === 'Esc' || e.keyCode === 27) {
				escapeEditMode('all');
			}
		});

		$(document).on('iconPicker:selected', onIconpickerSelected);


		// Widget events.
		$('.swift-control-settings .widget-item-col .edit-button').on('click', onEditButtonClick);
		$('.swift-control-settings .widget-items .dblclick-trigger').on('dblclick', onWidgetDoubleClick);
		$('.swift-control-page .widget-text-field').on('keydown', onWidgetEnter);
		$('.swift-control-page .new-tab-field').on('keydown', onWidgetEnter);
		$('.swift-control-page .widget-item .blur-trigger').on('click', onBlurTriggerClick);
	}

	function onEditButtonClick(e) {
		var widgetItem = this.parentNode.parentNode.parentNode;

		if (widgetItem.classList.contains('edit-mode')) {
			saveWidgetSettings(widgetItem);
		} else {
			switchEditMode(widgetItem);
		}
	}

	function onWidgetDoubleClick(e) {
		var widgetItem = this.parentNode.parentNode.parentNode;

		// Stop if current item is inside available items.
		if (widgetItem.parentNode.classList.contains('available-items')) return;

		// Stop if current item is already in edit-mode.
		if (widgetItem.classList.contains('edit-mode')) return;

		switchEditMode(widgetItem);
	}

	function onIconpickerSelected(evt, trigger, selectedIcon) {
		var icon = trigger.parentNode.parentNode.querySelector('.widget-icon > i');
		icon.className = selectedIcon;
	}

	function onWidgetEnter(e) {
		if (e.key === 'Enter' || e.keyCode === 13) {
			e.preventDefault();
			if (this.classList.contains('widget-text-field')) this.blur();
			saveWidgetSettings($(this).closest('.widget-item')[0]);
		}
	}

	function onBlurTriggerClick(e) {
		var widgetItem = this.parentNode.parentNode.parentNode.parentNode;
		window.getSelection().removeAllRanges();
		widgetItem.querySelector('.widget-text-field').blur();
	}

	/**
	 * Switch the `readonly` state of widget text field.
	 *
	 * @param {HTMLElement} widgetItem The current widget item.
	 * @param {bool} readonly Whether or not to disable the field.
	 */
	function setWidgetTextFieldState(widgetItem, readonly) {
		if (!widgetItem) return;

		if (widgetItem === 'all') {
			$('.swift-control-settings .widget-item .widget-text-field').attr('readonly', readonly);
		} else {
			var field = widgetItem.querySelector('.widget-text-field');
			if (!field) return;
			field.readOnly = readonly;
		}

	}

	/**
	 * Switch edit mode of active widget items.
	 *
	 * @param {HTMLElement} widgetItem The current widget item.
	 */
	function switchEditMode(widgetItem) {
		var textField = widgetItem.querySelector('.widget-text-field');

		escapeEditMode('all');
		widgetItem.classList.add('edit-mode');
		setWidgetTextFieldState(widgetItem, false);
		widgetItem.querySelector('.edit-button').innerHTML = SwiftControl.labels.save;
	}

	/**
	 * Switch saved status in the metabox header.
	 *
	 * @param {string} state Whether or not to show the "Saved" status. 
	 *                       Accepted values: "show" or "hide". Default is "show".
	 */
	function switchSavedStatus(state) {
		if (state === 'hide') {
			statusButton.classList.remove('is-shown');
		} else {
			statusButton.classList.add('is-shown');
		}
	}

	/**
	 * Escape edit mode when esc key pressed.
	 * 
	 * @param {HTMLElement|string} widgetItem The current widget item.
	 */
	function escapeEditMode(widgetItem) {
		if (widgetItem === 'all') {
			$('.swift-control-page .active-items .widget-item').removeClass('edit-mode');
			$('.swift-control-page .active-items .edit-button').html(SwiftControl.labels.edit);
			setWidgetTextFieldState('all', true);
		} else {
			var editButton = widgetItem.querySelector('.edit-button');
			editButton.innerHTML = SwiftControl.labels.edit;

			widgetItem.classList.remove('edit-mode');
			setWidgetTextFieldState(widgetItem, true);
		}

		$(".icon-picker-container").remove();
		$(document).unbind(".icon-picker");

		window.getSelection().removeAllRanges();
	}

	function displaySavedStatus() {
		switchSavedStatus('show');

		// We need some delay to give visual effect.
		setTimeout(function () {
			switchSavedStatus('hide');
		}, 2500);
	}

	/**
	 * Save widget item when the save button is clicked.
	 * Only bring available settings.
	 *
	 * @param {HTMLElement} widgetItem The current widget item.
	 */
	function saveWidgetSettings(widgetItem) {
		escapeEditMode(widgetItem);

		var settings = {};

		settings.icon_class = widgetItem.querySelector('.widget-icon i').className;
		settings.text = widgetItem.querySelector('.widget-text-field').value;

		var extraFields = {
			url: widgetItem.querySelector('.widget-url-field'),
			newTab: widgetItem.querySelector('.new-tab-field'),
			redirectUrl: widgetItem.querySelector('.redirect-url-field')
		};

		if (extraFields.url) {
			settings.url = extraFields.url.value;
		}

		if (extraFields.newTab) {
			settings.new_tab = (extraFields.newTab.checked ? 1 : 0);
		}

		if (extraFields.redirectUrl) {
			settings.redirect_url = extraFields.redirectUrl.value;
		}

		prepareAjaxRequest(ajax.changeWidgetSettings, {
			widget: widgetItem,
			widget_key: widgetItem.getAttribute('data-widget-key'),
			settings: settings
		});
	}

	/**
	 * Prepare ajax request.
	 *
	 * The widget item's setting is saved in `swift_control_widget_settings` option meta.
	 *
	 * This way, we can only do single request at a time.
	 * Because multiple requests will override each other.
	 *
	 * @param {function} func The ajax request function.
	 * @param {object} data The data to be passed to the "func".
	 */
	function prepareAjaxRequest(func, data) {
		// If there's other request in process, let's check again every 250ms.
		setTimeout(function () {
			if (isRequesting) {
				prepareAjaxRequest(func, data);
			} else {
				if (data) {
					func(data);
				} else {
					func();
				}
			}
		}, 250);
	}

	/**
	 * Send ajax request to change widget item's setting.
	 *
	 * @param {object} itemData Widget item's data (widget_key, settings).
	 */
	ajax.changeWidgetSettings = function (itemData) {
		isRequesting = true;
		var data = {};

		data.action = 'swift_control_change_widget_settings';
		data.nonce = SwiftControl.nonces.changeWidgetSettings;
		data.widget_key = itemData.widget_key;

		for (var prop in itemData.settings) {
			if (itemData.settings.hasOwnProperty(prop)) {
				data[prop] = itemData.settings[prop];
			}
		}

		$.ajax({
			url: ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data
		}).done(function (r) {
			displaySavedStatus();
		}).always(function () {
			isRequesting = false;
		});
	}

	/**
	 * Send ajax request to change active widget's order.
	 */
	ajax.changeWidgetsOrder = function () {
		var activeItems = document.querySelectorAll('#active-items .widget-item');
		var activeWidget = [];

		[].slice.call(activeItems).forEach(function (el) {
			activeWidget.push(el.dataset.widgetKey);
		});

		$.ajax({
			url: ajaxurl,
			type: 'post',
			dataType: 'json',
			data: {
				action: 'swift_control_change_widgets_order',
				nonce: SwiftControl.nonces.changeWidgetsOrder,
				active_widgets: activeWidget
			}
		}).done(function (r) {
			displaySavedStatus();
		});
	};

	// Let's initialize!
	init();
})(jQuery);
