/* $Id: imgflip.jquery.js 14 2009-07-09 09:48:56Z geraldb $

Unobtrusive Accessible jQuery Auto Sizing Textarea

Overview:
	Makes textareas automatically expand when input nears the bottom.
	If the textarea is resized, the new maxHeight is set to the textarea's
	new height.

	If you call the .autosize method on a textarea twice, the settings will
	be set to the new method's parameters.

Usage:
	$('textarea').autosize();

Parameters:
	maxHeight: (Int) Maximum height textarea should resize to. null = no maximum
		Default: null
	expandAmount: (Int) Number of pixels to add to the height of the textarea
		Default: 32
	bufferHeight: (Int) How close in pixels to the bottom of the textarea
						content should get
		Default: 16

Authors:
	http://james.padolsey.com, gburns

TODO:

*/

(function($) {
jQuery.fn.autosize = function(parameters) {
	var settings = $.extend({
		maxHeight: null,
		expandAmount: 32,
		bufferHeight: 16
	}, parameters || {});

	return this.each(function() {
		var $this = $(this);
		if ( !$this.is('textarea') ) $('textarea', this).autoexpand();

		// Check to see if .autosize() has already been called on this textarea
		if ( this.autosize_change_parameters ) {
			this.autosize_change_parameters(settings);
			return;
		}

		var $fauxbox;
		var interval;
		var keypressed = false;
		var maxHeight = parseInt(settings.maxHeight);
		var curHeight;
		var ignorePropChange = false;
		var expandAmount = parseInt(settings.expandAmount);
		var bufferHeight = parseInt(settings.bufferHeight);

		this.autosize_change_parameters = function(settings) {
			maxHeight = parseInt(settings.maxHeight);
			expandAmount = parseInt(settings.expandAmount);
			bufferHeight = parseInt(settings.bufferHeight);
			expand(true);
		};

		$this
			.css({
				overflowY: 'hidden',
				resize: 'none'
			})
			.bind($.browser.msie ? 'propertychange' : 'DOMAttrModified', function() {
				if ( !ignorePropChange && $this.css('height') != curHeight ) {
					changeMaxHeight(parseInt($this.css('height')));
				}
			});

		var changeMaxHeight = function(h) {
			maxHeight = h;
			if ( parseInt(curHeight) > maxHeight ) {
				ignorePropChange = true;
				$this.css({height: maxHeight});
				$this.css({overflowY: 'auto'});
				ignorePropChange = false;
			}
		};

		// Only run expand if a key has been pressed
		var checkExpand = function() {
			if ( keypressed ) expand(true);
		};

		var expand = function(textchange) {
			keypressed = false;

			if ( textchange ) {
				$fauxbox
					.val($this.val());
			}

			$fauxbox
				[0].scrollTop = 10000;

			if ( $.browser.msie ) {
				var rng = $fauxbox[0].createTextRange();
				rng.collapse(false);
				rng.select();
			}

			var fauxheight = $fauxbox.innerHeight() - parseInt($fauxbox.css('paddingTop')) - parseInt($fauxbox.css('paddingBottom'));
			var thisheight = $this.innerHeight() - parseInt($this.css('paddingTop')) - parseInt($this.css('paddingBottom'));

			var scrollTop = $fauxbox[0].scrollTop + fauxheight;

			if ( maxHeight && scrollTop > maxHeight ) {
				ignorePropChange = true;
				$this.css({
					overflowY: 'auto',
					height: maxHeight
				});
				curHeight = $this.css('height');
				ignorePropChange = false;
			} else if ( scrollTop + bufferHeight > thisheight ) {
				ignorePropChange = true;
				$this.css({
					height: scrollTop + expandAmount,
					overflowY: 'hidden'
				});
				curHeight = $this.css('height');
				ignorePropChange = false;
			} else if ( scrollTop + bufferHeight < thisheight ) {
				ignorePropChange = true;
				$this.css({
					height: scrollTop + expandAmount,
					overflowY: 'hidden'
				});
				curHeight = $this.css('height');
				ignorePropChange = false;
			}
		};

		var makebox = function() {
			if ( !$fauxbox ) $fauxbox = $this.clone().removeAttr('id').removeAttr('name').attr({tabIndex: -1});

			$fauxbox.css({
				position: 'absolute',
				top: 0,
				left: -10000,
				resize: 'none',
				width: $this.css('width'),
				lineHeight: $this.css('lineHeight'),
				textDecoration: $this.css('textDecoration'),
				letterSpacing: $this.css('letterSpacing'),
				fontSize: $this.css('fontSize'),
				fontFamily: $this.css('fontFamily')
			});

			$fauxbox.appendTo('body');
		};

		var setKeyPressed = function() {
			keypressed = true;
		};

		$this.bind('keyup', setKeyPressed);

		$this.bind('focus', function() {
			makebox();
			interval = window.setInterval(checkExpand, 400);
		});

		$this.bind('blur', function() {
			clearInterval(interval);
		});

		makebox();

		setTimeout(function() {
			var val = $this.val();
			$this.val('');
			$this.val(val);
			$fauxbox.val(val);
			expand();
		}, 0);
	});
};
})(jQuery);
