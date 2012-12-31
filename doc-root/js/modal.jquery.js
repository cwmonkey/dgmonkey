/* $Id: modal.jquery.js,v 1.1.2.3 2008/07/01 20:04:42 gburns Exp $

Unobtrusive Accessible jQuery 1.2.1 drop downs.

Overview:
	jQuery.fn.modal()

Parameters:
	prepend: (String) String to prepend classes/id's
		Default: 'modal'
	width: (Float) Default width for modal (null = no size will be set)
		Default: 400
	height: (Float) Default height for modal (null = no size will be set)
		Default: 300
	opacity: (Float) Opacity for overlay
		Default: 0.8
	speed: (String) Animation speed for modal animations
		Default: 'slow'
	callback: (Function) Function to call after contents
		are loaded into into modal - NOTE: the callback
		function is called on each subsequent jquery.modal.load
		call within the same modal "instance"
	closecallback(closer): (Function) Function to call after modal
		is closed. Passes "closer" to function, which is the element
		that triggered the close.
	center: (Boolean) True centers the modal on the screen
		Default: true
	urlSelector: (String) used for bringing back only a portion
		of the URL specified. See jQuery's load documentation for
		more info.
		Default: null

Usage:
	JavaScript:
	$('selector').modal();

	If the 'selector' is an ancor tag, the link's location will open
	in a modal. If the selector is a form the form's action will
	open in a modal. If neither, it will search for anchor tags
	and form tags and attach modal methods to them.

	You can also use the jQuery.modal methods directly to incorrporate
	your own plugins:

	$('a#slideshow_link').bind('click', function() {
		$.modal.load(this.href, null, function() {
			$('.slideshow', $.modal.$content).slideshow();
		});
	});

"Magic" Classes:
	{prepend}_close: Anything with the prepend parameter concatinated
		with "_close" will call the jQuery.modal.hide() on click.

Author:
	gburns, modal

TODO:
	documentation
	create multiple instances of modales for various settings
*/

(function($) {
$.fn.modal = function(options) {
	var img_search = /(\.jpg|\.jpeg|\.png|\.gif|\.bmp)$/;
	var urlSelector = ( options && options.urlSelector ) ? ' ' + options.urlSelector : '';

	return this.each(function() {
		var $this = $(this);

		if ( this.nodeName == 'A' ) {
			if ( this.href.match(img_search) ) {
				$this
					.bind('click', function() {
						$.modal.showImage(this.href, options);
						return false;
					});
			} else {
				$this
					.bind('click', function() {
						$.modal.load(this.href + urlSelector, null, options.callback, options);
						return false;
					});
			}
		} else if ( this.nodeName == 'FORM' ) {
			$this
				.bind('submit', function() {
					if ( this.method.toUpperCase() == 'POST' ) {
						$.modal.load(this.action + urlSelector, $this.serializeArray(), options.callback, options);
					} else {
						var query = (( this.action.indexOf('?') != -1 ) ? '&' : '?') + $this.serialize();
						$.modal.load(this.action + query + urlSelector, null, options.callback, options);
					}
					return false;
				});
		}
	});
};

$.fn.showDom = function(options) {
	var settings = $.modal.getSettings(options);

	return this.each(function() {
		var $this = $(this);

		$.modal.create(settings);
		$.modal.$content.append($this.html());
		$.modal.show();
		$.modal.$modal
			.removeClass(settings.prepend + '_loading');
		if ( settings.callback ) settings.callback();
	});
};

$.modal = {
	$modal: null,
	$overlay: null,
	$content: null,
	showing: false,
	settings: null,
	updated: false,
	closing: false,

	getSettings: function(options) {
		return $.extend({
			width: 400,
			height: 300,
			prepend: 'modal',
			opacity: 0.8,
			speed: 'slow',
			callback: null,
			wrappers: ['n', 's', 'e', 'w', 'nw', 'ne', 'sw', 'se', 'content'],
			closeCallback: null,
			center: true,
			urlSelector: null
		}, options || {});
	},

	// See if objects have been created
	isCreated: function() {
		return ( this.$content && this.$modal && this.$overlay );
	},

	// Resize modal object
	resize: function(options) {
		//if ( !showing ) return;

		var settings = $.extend({
			width: null,
			height: null,
			callback: null
		}, options || {});

		var dimensions = this.getPageDimensions();
		var scroll = this.getPageScroll();

		this.$modal.show();

		var modal_width_extra = this.$modal.outerWidth() - this.$content.innerWidth();
		var modal_height_extra = this.$modal.outerHeight() - this.$content.innerHeight();

		var to_x = parseInt((dimensions.vpw - settings.width - modal_width_extra) / 2) + scroll.x;
		var to_y = parseInt((dimensions.vph - settings.height - modal_height_extra) / 2) + scroll.y;

		if ( to_y < 0 ) to_y = 0;
		if ( to_x < 0 ) to_x = 0;

		if ( settings.speed ) {
			this.$content.animate({ width: settings.width + 'px', height: settings.height + 'px' }, settings.speed, function() {
				if ( settings.callback ) settings.callback();
				this.resizeOverlay();
			});
			this.$modal.animate({ left: to_x + 'px', top: to_y + 'px' }, settings.speed);
		} else {
			this.$content.css({ width: settings.width + 'px', height: settings.height + 'px' });
			this.$modal.css({ left: to_x + 'px', top: to_y + 'px' });
			if ( settings.callback ) settings.callback();
			this.resizeOverlay();
		}
	},

	// Show an image in content box
	// TODO: Figure out how to compensate for 404 errors
	showImage: function(href, options) {
		if ( this.updated && !confirm("You will lose all changes to this form. OK to proceed?") ) return false;
		this.updated = false;

		this.show(options);
		this.$modal.show();
		var settings = this.settings;

		var img = new Image();

		// once image is preloaded, resize image container
		img.onload = function() {
			$.modal.resize({
				width: img.width,
				height: img.height,
				callback: function() {
					$.modal.$content.html(img);
					}
				});
			img.onload = function(){}; // clear onLoad, IE behaves irratically with animated gifs otherwise

			$.modal.$modal
				.removeClass(settings.prepend + '_loading');

			$(img).click($.modal.hide);
		};

		img.src = href;
	},

	// Load a remote url via $'s load method
	load: function(href, data, callback, options) {
		if ( this.updated && !confirm("You will lose all changes to this form. OK to proceed?") ) return false;
		this.updated = false;

		this.show(options);

		var settings = $.extend(this.settings, options || {});

		if ( !href.match(/ +/) ) href += ' >:not(script, title)';

		if ( settings.speed ) this.$content.animate({opacity: 0}, settings.speed, function() {
			var $container = $('<div/>');
			$container.load(href, data, function() {
			/*$container.load(href + ' >:not(script)', data, function() {
				$('title, meta', $container)
					.remove();*/

				$.modal.$content.html($container.html());

				if ( settings.speed ) $.modal.$content.animate({opacity: 1}, settings.speed);

				$('select, input, textarea', $.modal.$content).bind('change', function() {
					this.updated = true;
				});

				$.modal.$modal
					.removeClass(settings.prepend + '_loading');

				$('.' + settings.prepend + '_close', $.modal.$content)
					.bind('click', function() {
						$.modal.hide(3);
						return false;
					});

				//if ( settings.callback ) settings.callback();
				if ( callback ) callback();
			});
		});
	},

	html: function(data) {
		if ( this.updated && !confirm("You will lose all changes to this form. OK to proceed?") ) return false;
		this.updated = false;

		this.show(options);

		var settings = $.extend(this.settings, options || {});

		$.modal.$content.html(data);

		if ( settings.speed ) $.modal.$content.animate({opacity: 1}, settings.speed);

		$('select, input, textarea', $.modal.$content).bind('change', function() {
			this.updated = true;
		});

		$.modal.$modal
			.removeClass(settings.prepend + '_loading');

		$('.' + settings.prepend + '_close', $.modal.$content)
			.bind('click', function() {
				$.modal.hide(3);
				return false;
			});
	},

	// Create objects
	create: function(options) {
		if ( this.isCreated() ) return;

		var settings = this.getSettings(options);

		var $body = $('body');

		this.$overlay = $('<div/>')
			.attr({id: settings.prepend + '_overlay'})
			.css({
				position: 'absolute',
				top: 0,
				left: 0,
				opacity: 0,
				display: 'none',
				zIndex: 10000
				})
			.bind('click', function() {
				if ( !$.modal.closing ) $.modal.hide(4);
				});

		this.$modal = $('<div/>')
			.attr({id: settings.prepend + '_container'})
			.css({
				position: 'absolute',
				display: 'none',
				zIndex: 10001
				});

		var $container;
		var $old_container = this.$modal;
		var divs = settings.wrappers;
		for ( var div in divs ) {
			$container = $('<div/>').addClass(settings.prepend + '_' + divs[div]);
			$old_container.append($container);
			$old_container = $container;
		}

		this.$content = $old_container;

		$body.append(this.$overlay);
		$body.append(this.$modal);

		this.settings = settings;
	},

	// Hide objects, clear content
	hide: function(closer) {
		var me = $.modal;
		if ( me.updated && !confirm("You will lose all changes to this form. OK to proceed?") ) return false;
		me.updated = false;

		if ( me.isCreated() ) {
			me.closing = true;
			me.showing = false;

			if ( me.settings.speed ) {
				me.$modal.animate({ opacity: 0 }, me.settings.speed, function() {
					$.modal.$modal.hide();
					$.modal.$overlay.animate({ opacity: 0 }, $.modal.settings.speed, function() {
						$.modal.$overlay.remove();
						$.modal.$modal.remove();

						$.modal.$overlay = null;
						$.modal.$modal = null;
						$.modal.$content = null;
						//$.modal.$overlay.hide();
						if ( $.modal.settings.closecallback ) $.modal.settings.closecallback(closer);
						//$.modal.$content.html('');
						$.modal.closing = false;

						var $hide_objects = $('iframe[modal_hide="true"], object[modal_hide="true"], embed[modal_hide="true"], select[modal_hide="true"]');

						$hide_objects.each(function() {
							$this = $(this);
							$this
								.attr({
									modal_hide: null,
									src: $this.attr('modal_src'),
									modal_src: null
									})
								.css({visibility: 'visible'});
						});

						$.modal.showing = false;
						$.modal.settings = null;
					});
				});
			} else {
				me.$overlay.remove();
				me.$modal.remove();

				me.$overlay = null;
				me.$modal = null;
				me.$content = null;

				if ( me.settings.closecallback ) me.settings.closecallback(closer);
				me.closing = false;

				if ( $.browser.msie ) {
					$('select[modal_iehide="true"]')
						.show();
				}

				me.showing = false;
				me.settings = null;
			}
		}
	},

	resizeOverlay: function() {
		if ( !$.modal.isCreated() ) return;

		var $body = $('body');
		var $html = $('html');

		if ( !this.$overlay ) return;

		this.$overlay
			.css({
				height: $html.outerHeight(true) + 'px'
				});

		var dimensions = this.getPageDimensions();

		this.$overlay
			.css({
				width: '100%', //(!$.browser.msie?$html.outerWidth(true) + 'px':'100%'),
				height: dimensions.ph + 'px'
				//height: '100%'
				});
	},

	// Show objects
	show: function(options) {
		if ( !this.isCreated() ) this.create(options);

		if ( this.showing ) return;
		this.showing = true;

		var settings = this.getSettings(options);

		var $hide_objects = $('iframe:visible, object:visible, embed:visible, select:visible');

		$hide_objects.each(function() {
			var $this = $(this);
			$this
				.attr({
					modal_hide: 'true',
					modal_src: $this.attr('src'),
					src: null
					})
				.css({visibility: 'hidden'});
		});

		var dimensions = this.getPageDimensions();

		var $body = $('body');
		var $html = $('html');

		this.$overlay
			.css({
				width: '100%', //(!$.browser.msie?$html.outerWidth(true) + 'px':'100%'),
				height: dimensions.ph + 'px',
				opacity: 0
				})
			.show();

		this.$modal
			.addClass(settings.prepend + '_loading')
			.css({
				opacity: 0
				});

		if ( settings.width && settings.height ) {
			this.$content
				.css({
					width: settings.width + 'px',
					height: settings.height + 'px'
					});
		}

		var scroll = this.getPageScroll();

		this.$modal
			.show();

		var to_x = 0;
		var to_y = 0;

		if ( settings.center ) {
			to_x = parseInt((dimensions.vpw - this.$modal.outerWidth()) / 2) + scroll.x;
			to_y = parseInt((dimensions.vph - this.$modal.outerHeight()) / 2) + scroll.y;
		} else {
			to_y = scroll.y;
		}

		if ( to_y < 0 ) to_y = 0;
		if ( to_x < 0 ) to_x = 0;

		this.$modal
			.css({
				top: to_y + 'px',
				left: to_x + 'px'
				});

		$('.' + settings.prepend + '_close', this.$content)
			.bind('click', function() {
				$.modal.hide(1);
				return false;
			});

		if ( settings.speed ) {
			this.$overlay.animate({opacity: settings.opacity}, settings.speed, 'linear', function(){
				$.modal.$modal.animate({ opacity: 1 }, settings.speed, 'linear', function() {
					if ( $.browser.msie ) $.modal.$modal[0].filters.alpha.enabled = false;
					if ( settings.callback ) settings.callback();
				});
			});
		} else {
			this.$modal.css({ opacity: 1 });
			if ( $.browser.msie ) $modal[0].filters.alpha.enabled = false;
			if ( callback ) callback();
			this.$overlay.css({ opacity: settings.opacity });
		}

		$(window).bind('resize', this.resizeOverlay);
	},

	maximize: function(options) {
		var settings = $.extend({
			setWidth: true,
			setHeight: true
		}, options || {});

		var dimensions = $.modal.getPageDimensions();
		var scroll = $.modal.getPageScroll();
		var scrollbarWidth = $.modal.getScrollbarWidth();

		var modal_width_extra = $.modal.$modal.outerWidth() - $.modal.$content.innerWidth();
		var modal_height_extra = $.modal.$modal.outerHeight() - $.modal.$content.innerHeight();

		var to_x = scroll.x;
		var to_y = scroll.y;

		if ( to_y < 0 ) to_y = 0;
		if ( to_x < 0 ) to_x = 0;

		var to_width = dimensions.vpw - modal_width_extra - scrollbarWidth;
		var to_height = dimensions.vph - modal_height_extra - scrollbarWidth;

		var height_width = {};

		if ( settings.setHeight ) height_width.height = to_height + 'px';
		if ( settings.setWidth ) height_width.width = to_width + 'px';

		if ( $.modal.speed ) {
			$.modal.$content.animate(height_width, $.modal.speed, function() {
				$.modal.resizeOverlay();
			});
			$.modal.$modal.animate({ left: to_x + 'px', top: to_y + 'px' }, $.modal.speed);
		} else {
			$.modal.$content.css(height_width);
			$.modal.$modal.css({ left: to_x + 'px', top: to_y + 'px' });
			$.modal.resizeOverlay();
		}
	},

	getPageDimensions: function() {
		var xScroll, yScroll;
		if ( window.innerHeight && window.scrollMaxY ) {	
			xScroll = window.innerWidth + window.scrollMaxX;
			yScroll = window.innerHeight + window.scrollMaxY;
		} else if ( document.body.scrollHeight > document.body.offsetHeight ) { // all but Explorer Mac
			xScroll = document.body.scrollWidth;
			yScroll = document.body.scrollHeight;
		} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
			xScroll = document.body.offsetWidth;
			yScroll = document.body.offsetHeight;
		}

		var windowWidth, windowHeight;
		if ( self.innerHeight ) {	// all except Explorer
			if ( document.documentElement.clientWidth ) {
				windowWidth = document.documentElement.clientWidth; 
			} else {
				windowWidth = self.innerWidth;
			}
			windowHeight = self.innerHeight;
		} else if ( document.documentElement && document.documentElement.clientHeight ) { // Explorer 6 Strict Mode
			windowWidth = document.documentElement.clientWidth;
			windowHeight = document.documentElement.clientHeight;
		} else if ( document.body ) { // other Explorers
			windowWidth = document.body.clientWidth;
			windowHeight = document.body.clientHeight;
		}	
		// for small pages with total height less then height of the viewport
		if ( yScroll < windowHeight ) {
			pageHeight = windowHeight;
		} else { 
			pageHeight = yScroll;
		}
		// for small pages with total width less then width of the viewport
		if ( xScroll < windowWidth ) {	
			pageWidth = xScroll;		
		} else {
			pageWidth = windowWidth;
		}

		if ( $('html').outerHeight(true) > pageHeight ) pageHeight = $('html').outerHeight(true);

		return {pw: pageWidth, ph: pageHeight, vpw: windowWidth, vph: windowHeight};
	},

	getPageScroll: function() {
		var xScroll, yScroll;

		if ( self.pageYOffset ) {
			yScroll = self.pageYOffset;
			xScroll = self.pageXOffset;
		} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
			yScroll = document.documentElement.scrollTop;
			xScroll = document.documentElement.scrollLeft;
		} else if (document.body) {// all other Explorers
			yScroll = document.body.scrollTop;
			xScroll = document.body.scrollLeft;	
		}

		//arrayPageScroll = new Array(xScroll, yScroll) 
		return {x: xScroll, y: yScroll};
		//arrayPageScroll;
	},

	scrollbarWidth: null,
	getScrollbarWidth: function() {
		if ( $.modal.scrollbarWidth ) return $.modal.scrollbarWidth;

		var scr = null;
		var inn = null;
		var wNoScroll = 0;
		var wScroll = 0;

		// Outer scrolling div
		scr = document.createElement('div');
		scr.style.position = 'absolute';
		scr.style.top = '-1000px';
		scr.style.left = '-1000px';
		scr.style.width = '100px';
		scr.style.height = '50px';
		// Start with no scrollbar
		scr.style.overflow = 'hidden';

		// Inner content div
		inn = document.createElement('div');
		inn.style.width = '100%';
		inn.style.height = '200px';

		// Put the inner div in the scrolling div
		scr.appendChild(inn);
		// Append the scrolling div to the doc

	    document.body.appendChild(scr);

		// Width of the inner div sans scrollbar
		wNoScroll = inn.offsetWidth;
		// Add the scrollbar
		scr.style.overflow = 'auto';
		// Width of the inner div width scrollbar
		wScroll = inn.offsetWidth;

		// Remove the scrolling div from the doc
		document.body.removeChild(document.body.lastChild);

		// Pixel width of the scroller
		var width = wNoScroll - wScroll;

		this.scrollbarWidth = width;

		return width;
	}
};
})(jQuery);