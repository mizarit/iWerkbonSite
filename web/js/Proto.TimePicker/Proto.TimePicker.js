/*
 * A time picker for Prototype.js
 * 
 *
 * Dual licensed under the MIT and GPL licenses (unfortunately).
 * Copyright (c) 2012 Jarvis Badgley
 * @name     Proto.TimePicker
 * @author   Jarvis Badgley (http://chipersoft.com)
 * @example  new Proto.TimePicker('mytime')
 * @example  new Proto.TimePicker('mytime', {step:30, startTime:"15:00", endTime:"18:00"});
 *
 * Ported from jquery.timePicker by Anders Fajerson (https://github.com/perifer/timePicker/network)
 *
 * Options:
 *   step: # of minutes to step the time by
 *   startTime: beginning of the range of acceptable times
 *   endTime: end of the range of acceptable times
 *   defaultTime: if the user has not yet selected a time, select this by default
 *   separator: separator string to use between hours and minutes (e.g. ':')
 *   show24Hours: use a 24-hour scheme
 *   leadingZero: append a leading 0 to hours less than 10.
 */

(function(){

	window.Proto = window.Proto || {};
	window.Proto.TimePicker = Class.create({
		version:'0.1',
		initialize: function(elm, options) {

			elm = $(elm);
			elm.timePicker = this;
			
			var settings = {
				step:30,
				startTime: new Date(0, 0, 0, 0, 0, 0),
				endTime: new Date(0, 0, 0, 23, 30, 0),
				defaultTime: null,
				separator: ':',
				show24Hours: true,
				leadingZero: true
			};
			
			Object.extend(settings, options || {});

			var tpOver = false;
			var keyDown = false;
			var startTime = timeToDate(settings.startTime, settings);
			var endTime = timeToDate(settings.endTime, settings);
			var defaultTime = settings.defaultTime ? timeToDate(settings.defaultTime, settings) : startTime;
			var selectedClass = "selected";
			var selectedSelector = "li." + selectedClass;

			elm.setAttribute('autocomplete', 'OFF'); // Disable browser autocomplete

			var times = [];
			var time = new Date(startTime); // Create a new date object.
			while(time <= endTime) {
				times[times.length] = formatTime(time, settings);
				time = new Date(time.setMinutes(time.getMinutes() + settings.step));
			}

			var $tpDiv = new Element('div',{className:'time-picker'+ (settings.show24Hours ? '' : ' time-picker-12hours')});
			var $tpList = new Element('ul');

			// Build the list.
			for(var i = 0; i < times.length; i++) {
				$tpList.insert("<li data-time=\""+times[i]+"\">" + times[i] + "</li>");
			}
			$tpDiv.insert($tpList);
			// Append the timPicker to the body and position it.
			document.body.appendChild($tpDiv.hide());

			// Store the mouse state, used by the blur event. Use mouseover instead of
			// mousedown since Opera fires blur before mousedown.
			$tpDiv.on('mouseover', function() {
				tpOver = true;
			});
			$tpDiv.on('mouseout', function() {
				tpOver = false;
			});

			$tpList.on('mouseover', 'li',function(event, element) {
				if (!keyDown) {
					$tpDiv.select(selectedSelector).invoke('removeClassName', selectedClass);
					element.addClassName(selectedClass);
				}
			});
			$tpList.on('mousedown','li', function(event, element) {
				 tpOver = true;
			});
			$tpList.on('click', 'li', function(event, element) {
				setTimeVal(elm, element, $tpDiv, settings);
				tpOver = false;
			});

			var showPicker = function() {
				if ($tpDiv.visible()) {
					return false;
				}
				$tpDiv.select('li').invoke('removeClassName', selectedClass);

				// Position
				var elmOffset = elm.cumulativeOffset();
				var elmLayout = elm.getLayout();
				$tpDiv.setStyle({
					'top':(elmOffset.top + elmLayout.get('border-box-height')) + 'px', 
					'left':elmOffset.left + 'px',
					'minWidth':elmLayout.get('padding-box-width') + 'px'
				});

				// Show picker. This has to be done before scrollTop is set since that
				// can't be done on hidden elements.
				$tpDiv.show();

				// Try to find a time in the list that matches the entered time.
				var time = elm.value ? timeStringToDate(elm.value, settings) : defaultTime;
				var startMin = startTime.getHours() * 60 + startTime.getMinutes();
				var min = (time.getHours() * 60 + time.getMinutes()) - startMin;
				var steps = Math.round(min / settings.step);
				var roundTime = normaliseTime(new Date(0, 0, 0, 0, (steps * settings.step + startMin), 0));
				roundTime = (startTime < roundTime && roundTime <= endTime) ? roundTime : startTime;
				var $matchedTime = $tpDiv.down("li[data-time='" + formatTime(roundTime, settings) + "']");

				if ($matchedTime) {
					$matchedTime.addClassName(selectedClass);
					// Scroll to matched time.
					$tpDiv.scrollTop = $matchedTime.offsetTop;
				}
				return true;
			};
			// Attach to click as well as focus so timePicker can be shown again when
			// clicking on the input when it already has focus.
			elm.on('focus', showPicker);
			elm.on('click', showPicker);
			// Hide timepicker on blur
			elm.on('blur', function() {
				if (!tpOver) {
					$tpDiv.hide();
				}
			});
			// Keypress doesn't repeat on Safari for non-text keys.
			// Keydown doesn't repeat on Firefox and Opera on Mac.
			// Using kepress for Opera and Firefox and keydown for the rest seems to
			// work with up/down/enter/esc.
			var event = (window.Prototype.Browser.Opera || window.Prototype.Browser.Gecko) ? 'keypress' : 'keydown';
			elm.on(event, function(e) {
				var $selected;
				keyDown = true;
				var top = $tpDiv.scrollTop;
				switch (e.keyCode) {
					case 38: // Up arrow.
						// Just show picker if it's hidden.
						if (showPicker()) return false;
						
						$selected = $tpList.down(selectedSelector) || $tpList.childElements().last();
						var prev = $selected.previous();
						if (prev) {
							prev.addClassName(selectedClass)
							$selected.removeClassName(selectedClass);
							// Scroll item into view.
							if (prev.positionedOffset().top < top) {
								$tpDiv.scrollTop = top - prev.getHeight();
							}
						}
						else {
							// Loop to next item.
							$selected.removeClassName(selectedClass);
							prev = $tpList.down("li:last").addClassName(selectedClass)[0];
							$tpDiv.scrollTop = prev.positionedOffset().top - prev.getHeight();
						}
						e.stop();
						return false;

					case 40: // Down arrow, similar in behaviour to up arrow.
						if (showPicker()) return false;

						$selected = $tpList.down(selectedSelector) || $tpList.down();
						var next = $selected.next()
						if (next) {
							next.addClassName(selectedClass);
							$selected.removeClassName(selectedClass);
							if (next.positionedOffset().top + next.getHeight() > top + $tpDiv.getHeight()) {
								$tpDiv.scrollTop = top + next.getHeight();
							}
						}
						else {
							$selected.removeClassName(selectedClass);
							next = $tpList.down("li").addClassName(selectedClass);
							$tpDiv.scrollTop = 0;
						}
						e.stop();
						return false;

					case 13: // Enter
						if ($tpDiv.visible()) {
							var sel = $tpList.down(selectedSelector);
							setTimeVal(elm, sel, $tpDiv, settings);
							e.stop();
							return false;
						}
						return;
					case 27: // Esc
						$tpDiv.hide();
						e.stop();
						return false;

				}
				return true;
			});
			elm.on('keyup', function(e) {
				keyDown = false;
			});
			// Helper function to get an inputs current time as Date object.
			// Returns a Date object.
			this.getTime = function() {
				return timeStringToDate(elm.value, settings);
			};
			// Helper function to set a time input.
			// Takes a Date object or string.
			this.setTime = function(time) {
				elm.value = formatTime(timeToDate(time, settings), settings);
				// Trigger element's change events.
				elm.fire('time:change');
			};

		} // End fn;
	});
	// Private functions.

	function setTimeVal(elm, sel, $tpDiv, settings) {
		// Update input field
		elm.value = $(sel).getAttribute('data-time');
		// Trigger element's change events.
		elm.fire('time:change');
		// Keep focus for all but IE (which doesn't like it)
		if (!window.Prototype.Browser.IE) {
			elm.focus();
		}
		// Hide picker
		$tpDiv.hide();
	}

	function formatTime(time, settings) {
		var h = time.getHours();
		var hours = settings.show24Hours ? h : (((h + 11) % 12) + 1);
		var minutes = time.getMinutes();
	    var hours_str = settings.leadingZero ?  formatNumber(hours) : hours;
		return hours_str + settings.separator + formatNumber(minutes) + (settings.show24Hours ? '' : ((h < 12) ? ' AM' : ' PM'));
	}

	function formatNumber(value) {
		return (value < 10 ? '0' : '') + value;
	}

	function timeToDate(input, settings) {
		return (typeof input == 'object') ? normaliseTime(input) : timeStringToDate(input, settings);
	}

	function timeStringToDate(input, settings) {
		if (input) {
			input = input.toUpperCase();
			var array = input.split(settings.separator);
			var hours = parseFloat(array[0]);
			var minutes = parseFloat(array[1]);

			// Convert AM/PM hour to 24-hour format.
			if (!settings.show24Hours) {
				if (hours === 12 && input.indexOf('AM') !== -1) {
					hours = 0;
				}
				else if (hours !== 12 && input.indexOf('PM') !== -1) {
					hours += 12;
				}
			}
			var time = new Date(0, 0, 0, hours, minutes, 0);
			return normaliseTime(time);
		}
		return null;
	}

	/* Normalise time object to a common date. */
	function normaliseTime(time) {
		time.setFullYear(2001);
		time.setMonth(0);
		time.setDate(0);
		return time;
	}

})();
