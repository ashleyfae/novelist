/**
 * Repeater code taken from CMB2.
 * @link https://github.com/WebDevStudios/CMB2
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

(function ($) {

	var Novelist = {

		/**
		 * ID number of current repeater row
		 */
		idNumber: false,

		/**
		 * Repeated elements
		 */
		repeatEls: 'input:not([type="button"]),select,textarea',

		/**
		 * Initialize functions.
		 */
		init: function () {
			$(document).ready(function () {
				Novelist.sortPurchaseLinks();
			});

			var repeatGroup = $('.novelist-repeatable-group');

			repeatGroup
				.on('click', '.novelist-repeater-heading', this.toggleRepeaterSection)
				.on('click', '.novelist-add-repeater-section', this.addRepeaterSection)
				.on('click', '.novelist-repeater-remove', this.removeRepeaterSection)
				.on('novelist_remove_repeater_section', this.resetRepeaterCounter);

			if (repeatGroup.length) {
				repeatGroup
					.filter('.novelist-repeater-sortable').each(function () {
					$(this).find('button.novelist-repeater-remove').before('<button class="button novelist-repeater-shift-rows move-up alignleft" title="' + NOVELIST.repeater_move_up + '"><span class="dashicons dashicons-arrow-up-alt2"></span></button> <button class="button novelist-repeater-shift-rows move-down alignleft" title="' + NOVELIST.repeater_move_down + '"><span class="dashicons dashicons-arrow-down-alt2"></span></button>');
				})
					.on('click', '.novelist-repeater-shift-rows', this.shiftRepeaterRows)
					.on('novelist_add_repeater_section', this.emptyValue);
			}
		},

		sortPurchaseLinks: function () {
			var purchaseLinkTable = $('#novelist-purchase-links tbody');
			if (purchaseLinkTable.length) {
				purchaseLinkTable.sortable({
					handle: '.novelist-drag-handle',
					items: '.novelist-cloned',
					opacity: 0.6,
					cursor: 'move',
					axis: 'y',
					update: function () {
						/*
						var order = $(this).sortable("serialize") + '&action=update-subscription-order';
						$.post(ajaxurl, order, function(response) {
							// @todo in case we want to auto save the new order
						});
						*/
					}
				});
			}
		},

		/**
		 * Toggle Repeater Section
		 *
		 * Opens/closes the contents of the repeater section when clicked on.
		 *
		 * @param e
		 */
		toggleRepeaterSection: function (e) {
			if (!$(e.target).hasClass('dashicons-trash')) {
				$(this).parent().toggleClass('novelist-repeater-section-expanded');
			}
		},

		/**
		 * Reset Repeater Counter
		 *
		 * Adjusts section titles and iterators to use new numbers.
		 *
		 * @param e
		 */
		resetRepeaterCounter: function (e) {
			// Loop repeatable group tables
			$('.novelist-repeatable-group').each(function () {
				var wrapper = $(this);
				// Loop repeatable group table rows
				wrapper.find('.novelist-repeater-section').each(function (rowindex) {
					var row = $(this);
					// Reset rows iterator
					row.data('iterator', rowindex);
					// Reset rows title
					row.find('.novelist-repeater-heading h3').text(wrapper.find('.novelist-add-repeater-section').data('group-title').replace('{#}', (rowindex + 1)));
				});
			});
		},

		/**
		 * Shift Rows Up or Down
		 *
		 * @param e
		 */
		shiftRepeaterRows: function (e) {

			e.preventDefault();

			var self = $(this);
			var parent = self.parents('.novelist-repeater-section');
			var goto = self.hasClass('move-up') ? parent.prev('.novelist-repeater-section') : parent.next('.novelist-repeater-section');

			if (!goto.length) {
				return;
			}

			// Let's shift, babes.
			self.trigger('novelist_shift_rows_start', self);

			var inputVals = [];

			// Loop this item's fields.
			parent.find(Novelist.repeatEls).each(function () {
				var element = $(this);
				var val;

				if ('checkbox' === element.attr('type') || 'radio' === element.attr('type')) {
					val = element.is(':checked');
				} else if ('select' === element.prop('tagName')) {
					val = element.is(':selected');
				} else {
					val = element.val();
				}
				// Get all the current values per element
				inputVals.push({val: val, $: element});
			});

			// And swap them all.
			goto.find(Novelist.repeatEls).each(function (index) {
				var element = $(this);
				var val;

				// handle checkbox swapping
				if ('checkbox' === element.attr('type') || 'radio' === element.attr('type')) {
					inputVals[index].$.prop('checked', element.is(':checked'));
					element.prop('checked', inputVals[index].val);
				}
				// handle select swapping
				else if ('select' === element.prop('tagName')) {
					inputVals[index].$.prop('selected', element.is(':selected'));
					element.prop('selected', inputVals[index].val);
				}
				// handle normal input swapping
				else {
					inputVals[index].$.val(element.val());
					element.val(inputVals[index].val);
				}
			});

			// Shift done.
			self.trigger('novelist_shift_rows_complete', self);

		},

		/**
		 * Add Repeater Section
		 *
		 * @param e
		 */
		addRepeaterSection: function (e) {
			e.preventDefault();

			var self = $(this);
			var wrapper = $(self.data('selector'));
			var oldRow = wrapper.find('.novelist-repeater-section').last();
			var prevNum = parseInt(oldRow.data('iterator'));
			Novelist.idNumber = prevNum + 1;
			var row = oldRow.clone();

			Novelist.newRowHousekeeping(row.data('title', self.data('group-title'))).cleanRow(row, prevNum, true);

			var newRow = $('<div class="novelist-repeater-section novelist-repeater-section-expanded" data-iterator="' + Novelist.idNumber + '">' + row.html() + '</div>');
			oldRow.after(newRow);

			if (wrapper.find('.novelist-repeater-section').length <= 1) {
				wrapper.find('.novelist-repeater-remove').prop('disabled', true);
			} else {
				wrapper.find('.novelist-repeater-remove').prop('disabled', false);
			}

			wrapper.trigger('novelist_add_repeater_section', newRow);
		},

		/**
		 * Remove Repeater Section
		 *
		 * @param e
		 */
		removeRepeaterSection: function (e) {
			e.preventDefault();

			var self = $(this);
			var wrapper = $(self.data('selector'));
			var parent = self.parents('.novelist-repeater-section');
			var number = wrapper.find('.novelist-repeater-section').length;

			if (number > 1) {

				// when a group is removed loop through all next groups and update fields names
				parent.nextAll('.novelist-repeater-section').find(Novelist.repeatEls).each(Novelist.updateRepeaterNameAttr);

				parent.remove();

				if (number <= 2) {
					wrapper.find('.novelist-repeater-remove').prop('disabled', true);
				} else {
					wrapper.find('.novelist-repeater-remove').prop('disabled', false);
				}

				wrapper.trigger('novelist_remove_repeater_section');
			}
		},

		/**
		 * Update Repeater Name Attribute
		 *
		 * @returns {boolean}
		 */
		updateRepeaterNameAttr: function () {

			var self = $(this);
			var name = self.attr('name');

			// No name? Bail
			if (typeof name === 'undefined') {
				return false;
			}

			var prevNum = parseInt(self.parents('.novelist-repeater-section').data('iterator'));
			var newNum = prevNum - 1; // Subtract 1 to get new iterator number

			// Update field name attributes so data is not orphaned when a row is removed and post is saved
			var newName = name.replace('[' + prevNum + ']', '[' + newNum + ']');

			// New name with replaced iterator
			self.attr('name', newName);

		},

		/**
		 * New Row Housekeeping
		 *
		 * @param row
		 * @returns {{idNumber: boolean, repeatEls: string, init: Novelist.init, toggleRepeaterSection: Novelist.toggleRepeaterSection, resetRepeaterCounter: Novelist.resetRepeaterCounter, addRepeaterSection: Novelist.addRepeaterSection, removeRepeaterSection: Novelist.removeRepeaterSection, updateRepeaterNameAttr: Novelist.updateRepeaterNameAttr, newRowHousekeeping: Novelist.newRowHousekeeping, cleanRow: Novelist.cleanRow, emptyValue: Novelist.emptyValue}}
		 */
		newRowHousekeeping: function (row) {

			var colorPicker = row.find('.wp-picker-container');

			if (colorPicker.length) {
				// Need to clean-up colorpicker before appending
				colorPicker.each(function () {
					var td = $(this).parent();
					td.html(td.find('input[type="text"].novelist-colorpicker').attr('style', ''));
				});
			}

			return Novelist;

		},

		/**
		 * Clean Row
		 *
		 * @param row
		 * @param prevNum
		 */
		cleanRow: function (row, prevNum) {

			var inputs = row.find('input:not([type="button"]), select, textarea, label');
			var other = row.find('[id]').not('input:not([type="button"]), select, textarea, label');

			// Update all elements with an ID.
			if (other.length) {
				other.each(function () {
					var $_this = $(this);
					var oldID = $_this.attr('id');
					var newID = oldID.replace('_' + prevNum, '_' + Novelist.idNumber);
					var buttons = row.find('[data-selector="' + oldID + '"]');
					$_this.attr('id', newID);

					// Replace data-selector vars
					if (buttons.length) {
						buttons.attr('data-selector', newID).data('selector', newID);
					}
				});
			}

			inputs.filter(':checked').prop('checked', false);
			inputs.filter(':selected').prop('selected', false);

			if (row.find('.novelist-repeater-heading h3').length) {
				row.find('.novelist-repeater-heading h3').text(row.data('title').replace('{#}', (Novelist.idNumber + 1)));
			}

			inputs.each(function () {
				var newInput = $(this);
				var oldFor = newInput.attr('for');
				var attrs = {};
				var newID, oldID;

				if (oldFor) {
					attrs = {'for': oldFor.replace('_' + prevNum, '_' + Novelist.idNumber)};
				} else {
					var oldName = newInput.attr('name');
					// Replace 'name' attribute key
					var newName = oldName ? oldName.replace('[' + prevNum + ']', '[' + Novelist.idNumber + ']') : '';
					oldID = newInput.attr('id');
					newID = oldID ? oldID.replace('_' + prevNum, '_' + Novelist.idNumber) : '';
					attrs = {
						id: newID,
						name: newName,
						// value: '',
						'data-iterator': Novelist.idNumber
					};
				}

				newInput.attr(attrs).val('');
			});

		},

		/**
		 * Empty All Values
		 * @param e
		 * @param row
		 */
		emptyValue: function (e, row) {
			$('input:not([type="button"]), textarea', row).val('');
		}

	};

	Novelist.init();

	/*
	 * General Sorting
	 */
	$('.novelist-sortable').sortable({
		cancel: '.novelist-no-sort, textarea, input, select',
		connectWith: '.novelist-sortable',
		placeholder: 'novelist-sortable-placeholder',
		update: function (event, ui) {
			var currentItem = ui.item;
			var parentID = currentItem.parent().attr('id');
			var disabledIndicator = currentItem.find('.novelist-book-option-disabled');
			if ($('#' + parentID).hasClass('novelist-sorter-enabled-column')) {
				disabledIndicator.val('false');
			} else {
				disabledIndicator.val('true');
			}
		}
	}).enableSelection();

	/*
	 * Open up editable textarea.
	 */
	$('.novelist-book-option-toggle').click(function (e) {
		$(this).next().slideToggle();
	});

	/*
	 * Change book cover alignment.
	 */
	$('#novelist-book-layout-cover-changer').change(function () {
		var parentDiv = $('#novelist-book-option-cover');
		parentDiv.removeClass(function (index, css) {
			return (css.match(/(^|\s)novelist-book-cover-align-\S+/g) || []).join(' ');
		});
		parentDiv.addClass('novelist-book-cover-align-' + $(this).val());
	});

	/*
	 * Live update text. Not sure I want to use this...
	 */
	/*$('.novelist-book-option-label').keyup(function () {
	 var targetField = $(this).parents('.novelist-book-option').find('.novelist-book-option-title');
	 targetField.html($(this).val());
	 });*/

	/*
	 * Purchase Links - Add/Remove Field
	 */

	$('#novelist-add-link').relCopy();

	/*
	 * Initialize color picker
	 */

	$('.novelist-color-picker').wpColorPicker();

	/*
	 * Reset tab
	 */

	$('#novelist-reset-tab-button').click(function (e) {
		e.preventDefault();

		if (!confirm(NOVELIST.confirm_reset)) {
			return false;
		}

		var parentDiv = $(this).parent();

		parentDiv.append('<span id="novelist-spinner" class="spinner is-active"></span>');

		var data = {
			'action': 'novelist_restore_default_settings',
			'tab': $(this).data('current-tab'),
			'section': $(this).data('current-section'),
			'_ajax_nonce': $(this).data('nonce')
		};

		$.post(ajaxurl, data, function (response) {
			$('#novelist-spinner').remove();

			if (response.success == true) {
				window.location.href = response.data;
			} else {
				parentDiv.append(response.data);
			}
		});
	});

	/*
	 * Import Demo Book
	 */
	$('#novelist-import-demo-book').click(function (e) {
		e.preventDefault();

		var buttonContainer = $(this).parent();

		$(this).attr('disabled', 'true');
		buttonContainer.append('<span id="novelist-spinner" class="spinner is-active" style="float: none;"></span>');

		var data = {
			'action': 'novelist_import_demo_book',
			'nonce': $(this).data('nonce')
		};

		$.post(ajaxurl, data, function (response) {
			buttonContainer.empty().append(response.data);
		});
	});

})(jQuery);