(function($) {
$.fn.datetime = function(options) {
	var settings = $.extend({
		prepend: 'datetime',
		format: 'yyyy-mm-dd hh:MM:ss'
	}, options || {});


	return this.each(function() {
		var $input = $(this),

			$year_previous = $('<a href="javascript:;">&lt; Prev</a>'),
			$year_next = $('<a href="javascript:;">Next &gt;</a>'),
			$year_current = $('<a href="javascript:;">####</a>'),

			$select_year = $('<a href="javascript:;">Select Year</a>'),
			$select_date = $('<a href="javascript:;">Select a Date/Time</a>'),

			$year_thousands = [],
			$year_hundreds = [],
			$year_tens = [],
			$year_ones = [],

			$months = [],

			$hours = [],

			$minute_tens = [],
			$minute_ones = [],

			$am = $('<a href="javascript:;">AM</a>'),
			$pm = $('<a href="javascript:;">PM</a>'),

			timestamp,

			datereg = new RegExp('^([0-9]{4})-([0-9]{2})-([0-9]{2})( ([0-9]{2}):([0-9]{2}):([0-9]{2}))?$'),
			timeparse,
			curdate = new Date(),
			$container,

			prepend = settings.prepend,
			format = settings.format,

			$hour_list = $('<ol/>'),
			$year_tbody = $('<tbody/>').hide(),
			$month_list = $('<ol/>'),
			$day_tbody = $('<tbody/>'),
			$minute_tens_list = $('<ol/>'),
			$minute_ones_list = $('<ol/>'),
			$main = $('<div/>').hide();

		$am.bind('click', function() {
			if (curdate.getHours() > 11) {
				curdate.setHours(curdate.getHours() - 12);
				updateCal();
			}
		});

		$pm.bind('click', function() {
			if (curdate.getHours() < 12) {
				curdate.setHours(curdate.getHours() + 12);
				updateCal();
			}
		});

		$year_previous.bind('click', function() {
			curdate.setFullYear(curdate.getFullYear()-1);
			updateCal();
		});

		$year_next.bind('click', function() {
			curdate.setFullYear(curdate.getFullYear()+1);
			updateCal();
		});

		timestamp = $input.val();
		timeparse = datereg.exec(timestamp);

		if ( timeparse && timeparse[0] ) {
			curdate.setYear(timeparse[1]);
			curdate.setMonth(timeparse[2]-1);
			curdate.setDate(timeparse[3]);
			if (timeparse[4]) curdate.setHours(timeparse[5]);
			if (timeparse[4]) curdate.setMinutes(timeparse[6]);
		}

		for ( var i=0; i<10; i++ ) {
			$year_thousands[i] = $('<a href="javascript:;">' + i + '</a>')
				.bind('click', function(y1) {
					return function() {
						curdate.setFullYear(y1+(""+curdate.getFullYear()).substring(1));
						console.log();
						updateCal();
					};
				}(i));

			$year_hundreds[i] = $('<a href="javascript:;">' + i + '</a>')
				.bind('click', function(y2) {
					return function() {
						curdate.setFullYear((""+curdate.getFullYear()).substring(0,1)+y2+(""+curdate.getFullYear()).substring(2));
						updateCal();
					};
				}(i));

			$year_tens[i] = $('<a href="javascript:;">' + i + '</a>')
				.bind('click', function(y3) {
					return function() {
						curdate.setFullYear((""+curdate.getFullYear()).substring(0,2)+y3+(""+curdate.getFullYear()).substring(3));
						updateCal();
					};
				}(i));

			$year_ones[i] = $('<a href="javascript:;">' + i + '</a>')
				.bind('click', function(y4) {
					return function() {
						curdate.setFullYear((""+curdate.getFullYear()).substring(0,3)+y4);
						updateCal();
					};
				}(i));

			$year_tbody.append(
				$('<tr/>')
					.append(
						$('<td/>').append($year_thousands[i])
					)
					.append(
						$('<td/>').append($year_hundreds[i])
					)
					.append(
						$('<td/>').append($year_tens[i])
					)
					.append(
						$('<td/>').append($year_ones[i])
					)
			);
		}

		$select_year.bind('click', function() {
			if ( $year_tbody.is(':hidden') ) {
				$year_tbody.show();
			} else {
				$year_tbody.hide();
			}
		});

		$select_date.bind('click', function() {
			if ( $main.is(':hidden') ) {
				$main.show();
			} else {
				$main.hide();
			}
		});

		//TODO: move names out of here
		for ( var i=1; i<13; i++ ) {
			$months[i] = $('<a href="javascript:;">' + ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][(i-1)] + '</a>');

			$month_list.append(
				$('<li/>').append($months[i])
			);

			$months[i].bind('click', function(month) {
				return function() {
					curdate.setMonth(month-1);
					updateCal();
				};
			}(i));
		}

		for ( var i=0; i<12; i++ ) {
			$hours[i] = $('<a href="javascript:;">' + ((i==0)?12:i) + '</a>')
				.bind('click', function(h) {
					return function() {
						if (curdate.getHours() > 11) h += 12;
						curdate.setHours(h);
						updateCal();
					};
				}(i));

			$hour_list.append(
				$('<li/>').append($hours[i])
			);
		}

		for ( var i=0; i<6; i++ ) {
			$minute_tens[i] = $('<a href="javascript:;">' + i + '</a>')
				.bind('click', function(t) {
					return function() {
						var m = (""+curdate.getMinutes());
						if ( m.length == 1 ) m = "0" + m;
						curdate.setMinutes(t+m.substring(1));
						updateCal();
					};
				}(i));

			$minute_tens_list.append(
				$('<li/>').append($minute_tens[i])
			);
		}

		for ( var i=0; i<10; i++ ) {
			$minute_ones[i] = $('<a href="javascript:;">' + i + '</a>')
				.bind('click', function(o) {
					return function() {
						var m = (""+curdate.getMinutes());
						if ( m.length == 1 ) m = "0" + m;
						curdate.setMinutes(m.substring(0,1)+o);
						updateCal();
					};
				}(i));

			$minute_ones_list.append(
				$('<li/>').append($minute_ones[i])
			);
		}

		$container = $('<div/>').addClass(prepend + '_container')
			.append(
				$('<h2/>').append($select_date)
			)
			.append(
				$main.addClass(prepend + '_main')
					.append(
						$('<div/>').addClass(prepend + '_date')
							.append(
								$('<div/>').addClass(prepend + '_year')
									.append(
										$('<h3/>').text('Year')
									)
									.append(
										$('<ul/>')
											.append(
												$('<li/>')
													.append($year_previous)
											)
											.append(
												$('<li/>')
													.append($year_current)
											)
											.append(
												$('<li/>')
													.append($year_next)
											)
									)
									.append(
										$('<table/>').attr({cellspacing:0, cellpadding:0})
											.append(
												$('<thead/>')
													.append(
														$('<tr/>')
															.append(
																$('<th/>').attr({colspan:4})
																	.append($select_year)
															)
													)
											)
											.append($year_tbody)
									)
							)
							.append(
								$('<div/>').addClass(prepend + '_month')
								.append(
									$('<h3/>').text('Month')
								)
								.append($month_list)
							)
							.append(
								$('<div/>').addClass(prepend + '_day')
									.append(
										$('<h3/>').text('Day')
									)
									.append(
										$('<table/>')
											.append(
												$('<thead/>')
													.append(
														$('<tr/>')
															.append(
																$('<th/>').text('Sun')
															)
															.append(
																$('<th/>').text('Mon')
															)
															.append(
																$('<th/>').text('Tue')
															)
															.append(
																$('<th/>').text('Wed')
															)
															.append(
																$('<th/>').text('Thu')
															)
															.append(
																$('<th/>').text('Fri')
															)
															.append(
																$('<th/>').text('Sat')
															)
													)
											)
											.append(
												$day_tbody
											)
									)
							)
					)
					.append(
						$('<div/>').addClass(prepend + '_time')
							.append(
								$('<div/>').addClass(prepend + '_hour')
									.append(
										$('<h3/>').text('Hour')
									)
									.append($hour_list)
							)
							.append(
								$('<div/>').addClass(prepend + '_minute')
									.append(
										$('<h3/>').text('Minute')
									)
									.append($minute_tens_list.addClass(prepend+'_tens'))
									.append($minute_ones_list.addClass(prepend+'_ones'))
							)
							.append(
								$('<div/>').addClass(prepend + '_ampm')
									.append(
										$('<h3/>').text('AM/PM')
									)
									.append(
										$('<ul/>')
											.append(
												$('<li/>').append($am)
											)
											.append(
												$('<li/>').append($pm)
											)
									)
							)
					)
					.append(
						$('<div/>').addClass(prepend + '_datetime').append($('<p/>').html("&nbsp;"))
					)
			)

		$input.after($container);

		var updateCal = function() {
			$day_tbody.html('');
			$('.' + prepend + '_current', $container).removeClass(prepend + '_current');
			var caldate = new Date(curdate);
			caldate.setDate(1);
			caldate.setSeconds(caldate.getSeconds()-(caldate.getDay() * 24 * 60 * 60));
			for ( var wr=0; wr<6; wr++ ) {
				var $week = $('<tr/>');
				for ( var dc=0; dc<7; dc++ ) {
					var $day = $('<a href="javascript:;">' + caldate.getDate() + '</a>')
						.addClass(((curdate.getMonth()==caldate.getMonth() && curdate.getDate()==caldate.getDate())?prepend+'_current':''))
						.addClass(((curdate.getMonth()==caldate.getMonth())?prepend+"_curmonth":""))
						.bind('click', function(y, m, d) {
							return function() {
								curdate.setFullYear(y);
								curdate.setMonth(m);
								curdate.setDate(d);
								updateCal();
							}
						}(caldate.getFullYear(), caldate.getMonth(), caldate.getDate()));
					$week.append(
						$('<td/>').append(
							$day
						)
					);
					caldate.setSeconds(caldate.getSeconds()+(1*24*60*60));
				}
				$day_tbody.append($week);
			}

			$year_current.text(caldate.getFullYear()).addClass(prepend + '_current');

			$year_thousands[(""+caldate.getFullYear()).substring(0,1)].addClass(prepend + '_current');
			$year_hundreds[(""+caldate.getFullYear()).substring(1,2)].addClass(prepend + '_current');
			$year_tens[(""+caldate.getFullYear()).substring(2,3)].addClass(prepend + '_current');
			$year_ones[(""+caldate.getFullYear()).substring(3,4)].addClass(prepend + '_current');

			if ( curdate.getHours() > 11 ) {
				$am.removeClass(prepend + '_current');
				$pm.addClass(prepend + '_current');
			} else {
				$am.addClass(prepend + '_current');
				$pm.removeClass(prepend + '_current');
			}

			var m = (""+curdate.getMinutes());
			if ( m.length == 1 ) m = "0" + m;
			$minute_ones[m.substring(1,2)].addClass(prepend + '_current');
			$minute_tens[m.substring(0,1)].addClass(prepend + '_current');

			var h = curdate.getHours();
			if ( h > 11 ) h -= 12;
			$hours[h].addClass(prepend + '_current');

			$months[curdate.getMonth()+1].addClass(prepend + '_current');

			$input.val($.datetime.format(curdate, format));
		}

		updateCal();
	});
};

$.datetime = {
	// hack n slash from http://blog.stevenlevithan.com/archives/date-time-format
	token: /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g, //"dont mind me, just here for stupid editors :D
	timezone: /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
	timezoneClip: /[^-+\dA-Z]/g,
	pad: function (val, len) {
		val = String(val);
		len = len || 2;
		while (val.length < len) val = "0" + val;
		return val;
	},
	format: function (date, mask, utc) {
		var dF = $.datetime;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   dF.pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   dF.pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   dF.pad(H % 12 || 12),
				H:    H,
				HH:   dF.pad(H),
				M:    M,
				MM:   dF.pad(M),
				s:    s,
				ss:   dF.pad(s),
				l:    dF.pad(L, 3),
				L:    dF.pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(dF.timezone) || [""]).pop().replace(dF.timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + dF.pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};

		return mask.replace(dF.token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	},
	
	// Some common format strings
	masks: {
		"default":      "ddd mmm dd yyyy HH:MM:ss",
		shortDate:      "m/d/yy",
		mediumDate:     "mmm d, yyyy",
		longDate:       "mmmm d, yyyy",
		fullDate:       "dddd, mmmm d, yyyy",
		shortTime:      "h:MM TT",
		mediumTime:     "h:MM:ss TT",
		longTime:       "h:MM:ss TT Z",
		isoDate:        "yyyy-mm-dd",
		isoTime:        "HH:MM:ss",
		isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
		isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
	},
	
	// Internationalization strings
	i18n: {
		dayNames: [
			"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
			"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
		],
		monthNames: [
			"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
			"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
		]
	}
};
})(jQuery);
