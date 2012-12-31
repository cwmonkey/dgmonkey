/* $Id: dropdown.jquery.js,v 1.1.4.1 2008/03/24 19:56:48 ytan Exp $

Unobtrusive Accessible jQuery 1.2.1 drop downs.

Supports:
	- Positioning dropdown down, left, right
	- onFocus dropdowns
	- dropdown on click or mouseover

Usage:
	<div class="dropDown">
		<h3>Header</h3>
		<ul>
			<li>link</li>
			<li>link</li>
		</ul>
	</div>
	$('.dropDown').dropdown();
	$('.dropDown').dropdown('mouseover');
	$('.dropDown').dropdown({trigger: 'mouseover', direction: left, sameWidth: true});
	You MUST set a width on your header tags or there will be unexpected results.
	Use overflow-y: scroll; in your body selector to avoid positioning problems with dropdowns near the end of the viewport

Author:
	gburns

TODO:
	possibly change down drops to go above the header element if they fall outside of the viewport
	documentation
*/

(function($) {
/* Main dropdown function */
$.fn.dropdown = function(options) {
	var trigger;
	var settings;

	settings = $.extend({
		trigger: 'click',
		direction: 'down',
		sameWidth: 'default',
		confineToViewport: true,
		atMouse: false,
		prepend: 'dropdown'
	}, options || {});

	return $(this).each(function() {
		var $this = $(this);
		var that = this;
		var $ul;
		var $h;
		var a;
		var stopFocus = false;
		var stopBlur = false;

		$this.addClass(settings.prepend + '_container');

		$ul = $('> :nth-child(2)', this);
		var ul = $ul[0];
		if ( !ul ) return;

		$h = $('> :first-child', this);
		$this.addClass(settings.prepend + '_head');
		var h = $h[0];
		if ( !h ) return;

		$ul.addClass(settings.prepend + '_list');
		var container = this;

		if ( !$('> a', $h)[0] ) {
			$h
				.attr({'innerHTML': '<a href="javascript:;">' + $h.attr('innerHTML') + '</a>'});
		}

		a = $('> a', $h)[0];
		$a = $(a);

		$ul
			.css({
				'position': 'absolute',
				'zIndex': 1000
			});

		$.dropdown.addUl($ul, $h);

		var show = function(event, focus) {
			var $body = $(document.body);
			var outer_boundry_top = 0;
			var outer_boundry_left = 0;
			var outer_boundry_bottom = $body.outerHeight();
			var outer_boundry_right = $body.outerWidth();
			var inner_boundry_top = 0;
			var inner_boundry_left = 0;
			var inner_boundry_bottom = $body.outerHeight();
			var inner_boundry_right = 0;

			if ( $.browser.mozilla ) outer_boundry_bottom -= $.scrollbarwidth();

			if ( settings.confineToViewport ) {
				inner_boundry_top = $.viewport.top();
				inner_boundry_left = $.viewport.left();
				inner_boundry_bottom = $.viewport.bottom();
				inner_boundry_right = $.viewport.right();
			} else {
				inner_boundry_top = outer_boundry_top;
				inner_boundry_left = outer_boundry_left;
				inner_boundry_bottom = outer_boundry_bottom;
				inner_boundry_right = outer_boundry_right;
			}

			$ul.css({
				top: '-1000px',
				left: '-1000px',
				display: 'block',
				visibility: 'visible'
			});

			if (
				settings.sameWidth == true
				||
					(
						settings.sameWidth == 'default' &&
						(settings.direction == 'up' || settings.direction == 'down')
					)
				) {

				if ( $ul.outerWidth() < $this.outerWidth() ) {
					var w = $this.outerWidth() - ((parseInt($ul.css('paddingLeft')) || 0)
							+ (parseInt($ul.css('paddingRight')) || 0)
							+ (parseInt($ul.css('marginLeft')) || 0)
							+ (parseInt($ul.css('marginRight')) || 0)
							+ (parseInt($ul.css('borderLeftWidth')) || 0)
							+ (parseInt($ul.css('borderRightWidth')) || 0));
					$ul.css({
						width: w + 'px'
					});
				}
			}

			var top = 0;
			var left = 0;

			if ( settings.atMouse && !focus ) {
				left = that_left -1;
				top = that_top -1;
			} else if ( settings.direction == 'left' ) {
				left = that_left - $ul.outerWidth();
				top = that_top;
			} else if ( settings.direction == 'right' ) {
				left = that_left + $h.outerWidth();
				top = that_top;
			} else if ( settings.direction == 'down' ) {
				left = $this.position().left + (parseInt($this.css('marginLeft')) || 0);
				top = $this.position().top + $this.outerHeight() + (parseInt($h.css('marginTop')) || 0);
			}

			$ul.css({top: top + 'px', left: left + 'px'});
		}

		$ul.hide();

		if ( settings.trigger == 'mouseover' ) {
			$this
				.mouseover(function(event) {
					$ul[0].hideMe = false;
					clearTimeout($ul[0].hideTimeout);
					if ( $ul[0].hasFocus ) return;
					$ul[0].hasFocus = true;
					$ul[0].hideTimeout = setTimeout($.dropdown.hideUnfocusedUls, 0);
					if ( $ul.is(':hidden') ) {
						show(event);
					}
				})
				.mouseout(function() {
					$ul[0].hideMe = true;
					$ul[0].hideTimeout = setTimeout($.dropdown.hideUnfocusedUls, 500);
				});

			$a
				.focus(function(event) {
					$ul[0].hideMe = false;
					$ul[0].hasFocus = true;
					$ul[0].hideTimeout = setTimeout($.dropdown.hideUnfocusedUls, 0);
					if ( $ul.is(':hidden') ) {
						show(event, true);
					}
				})
				.blur(function() {
					$ul[0].hideMe = true;
					$ul[0].hideTimeout = setTimeout($.dropdown.hideUnfocusedUls, 0);
				})
				.mouseover(function() {
					clearTimeout($ul[0].hideTimeout);
					$ul[0].hideMe = false;
				})
				.mouseout(function() {
					return false;
				});

			$('ul a', this)
				.focus(function(event) {
					$ul[0].hideMe = false;
					clearTimeout($ul[0].hideTimeout);
				})
				.blur(function(event) {
					$ul[0].hideMe = true;
					$ul[0].hideTimeout = setTimeout($.dropdown.hideUnfocusedUls, 0);
				});
		} else if ( settings.trigger == 'click' ) {
			$ul[0].hasFocus = false;

			$ul
				.click(function(event) {
					event.stopPropagation();
				});

			$this
				.click(function(event) {
					clearTimeout($.dropdown.hideTimeout);
					$ul[0].hasFocus = true;
					var display = $ul.css('display');
					$.dropdown.hideUls();
					if ( display == 'none' ) {
						show(event);
					}
					event.stopPropagation();
				})
				.mousedown(function(event) {
					//stopFocus = true;
					stopBlur = true;
				});

			$('ul a', this)
				.mousedown(function() {
					stopBlur = true;
				})
				.focus(function(event) {
					$ul[0].hasFocus = true;
					clearTimeout($.dropdown.hideTimeout);
				})
				.blur(function(event) {
					$ul[0].hideMe = true;
					$.dropdown.hideTimeout = setTimeout($.dropdown.hideUnfocusedUls, 0);
				})
				.click(function(event) {
					var display = $ul.css('display');
					$.dropdown.hideUls();
					if ( display == 'none' ) {
						show(event);
					}
				});

			$a
				.focus(function(event) {
					$ul[0].hasFocus = true;
					if ( !stopFocus ) {
						var display = $ul.css('display');
						if ( display == 'none' ) {
							show(event);
						}
					}
					stopFocus = false;
					//event.stopPropagation();
				})
				.blur(function(event) {
					$ul[0].hasFocus = false;
					if ( !stopBlur ) {
						$.dropdown.hideTimeout = setTimeout($.dropdown.hideUnfocusedUls, 0);
					}
					stopBlur = false;
					//event.stopPropagation();
				})
				.mousedown(function(event) {
					//stopFocus = true;
				});
		}
	});
}

/* Helper object. Stores dropdown objects for hiding, etc */
$.dropdown = {
	uls: [],
	hideTimeout: null,
	addUl: function($ul, $h) {
		if ( !$.dropdown.uls.length ) {
			$(document).click($.dropdown.hideUls);
		}
		$.dropdown.uls[$.dropdown.uls.length] = $ul;
		$ul[0].hasFocus = false;
		$ul[0].hideMe = false;
	},
	hideUnfocusedUls: function() {
		for ( var i = 0; ($ul = $.dropdown.uls[i]); i++ ) {
			if ( $ul[0].hideMe ) {
			$ul[0].hasFocus = false;
				$ul[0].hideMe = false;
				$ul.css({
					display: 'none',
					visibility: 'hidden'
				});
			}
		}
	},
	hideUls: function() {
		for ( var i = 0; ($ul = $.dropdown.uls[i]); i++ ) {
			$ul[0].hasFocus = false;
			$ul.hide();
		}
	}
}

/* Helper object. Contains functions for dimensions and position of viewport. */
if ( !$.viewport ) {
	$.viewport = {
		top: function() {
			if ( document.body && ( document.body.scrollTop ) ) {
				//DOM compliant
				return document.body.scrollTop;
			} else if ( document.documentElement && ( document.documentElement.scrollTop ) ) {
				//IE6 standards compliant mode
				return document.documentElement.scrollTop;
			}
			return 0;
		},
		left: function() {
			if ( document.body && ( document.body.scrollLeft ) ) {
				//DOM compliant
				return document.body.scrollLeft;
			} else if ( document.documentElement && ( document.documentElement.scrollLeft ) ) {
				//IE6 standards compliant mode
				return document.documentElement.scrollLeft;
			}
			return 0;
		},
		height: function() {
			if ( typeof window.innerHeight != 'undefined' ) {
				// Firefox adds the width of scrollbars to the viewport height
				if ( $.browser.mozilla && $(document.body).outerWidth() > window.innerWidth ) {
					return window.innerHeight - $.scrollbarwidth();
				}
				return window.innerHeight;
			// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
			} else if (
				typeof document.documentElement != 'undefined'
				&& typeof document.documentElement.clientHeight != 'undefined'
				&& document.documentElement.clientHeight != 0
				) {
				return document.documentElement.clientHeight;
			}
			return 0;
		},
		width: function() {
			if ( typeof window.innerWidth != 'undefined' ) {
				if ( $.browser.mozilla && $(document.body).outerHeight() > window.innerHeight ) {
					return window.innerWidth - $.scrollbarwidth();
				}
				return window.innerWidth;
			// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
			} else if (
				typeof document.documentElement != 'undefined'
				&& typeof document.documentElement.clientWidth != 'undefined'
				&& document.documentElement.clientWidth != 0
				) {
				return document.documentElement.clientWidth;
			}
			return 0;
		},
		right: function() {
			return $.viewport.left() + $.viewport.width();
		},
		bottom: function() {
			return $.viewport.top() + $.viewport.height();
		}
	};
}

/* Helper function. Returns width of scrollbars on the page. */
if ( !$.scrollbarwidth ) {
	$.scrollbarwidth = function() {
		if ( typeof(document.body.scrollbarWidth) != 'undefined' ) return parseInt(document.body.scrollbarWidth);

		var $div = $('<div/>')
		.css({
			position: 'absolute',
			top: '-1000px',
			left: '-1000px',
			width: '100px',
			height: '50px',
			overflow: 'hidden'
		});

		var $inner = $('<div/>')
		.css({
			width: '100%',
			height: '200px'
		});

		$div.append($inner);
		$(document.body).append($div);

		var no_scroll = $inner.width();

		$div.css({overflow: 'auto'});

		var scroll = $inner.width();

		$div.remove();
		document.body.scrollbarWidth = no_scroll - scroll;
		return no_scroll - scroll;
	}
}
})(jQuery);
