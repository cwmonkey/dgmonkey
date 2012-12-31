/* $Id: lightbox.jquery.js,v 1.1.2.3 2008/07/01 20:04:42 gburns Exp $

Unobtrusive Accessible jQuery 1.2.1 drop downs.

Overview:
	jQuery.fn.lightbox()

Parameters:
	prepend: (String) String to prepend classes/id's
		Default: 'lightbox'
	width: (Float) Default width for lightbox (null = no size will be set)
		Default: 400
	height: (Float) Default height for lightbox (null = no size will be set)
		Default: 300
	opacity: (Float) Opacity for overlay
		Default: 0.8
	speed: (String) Animation speed for lightbox animations
		Default: 'slow'
	callback: (Function) Function to call after contents
		are loaded into into lightbox - NOTE: the callback
		function is called on each subsequent jquery.lightbox.load
		call within the same lightbox "instance"
	closecallback(closer): (Function) Function to call after lightbox
		is closed. Passes "closer" to function, which is the element
		that triggered the close.
	center: (Boolean) True centers the lightbox on the screen
		Default: true;

Usage:
	JavaScript:
	$('selector').lightbox();

	If the 'selector' is an ancor tag, the link's location will open
	in a lightbox. If the selector is a form the form's action will
	open in a lightbox. If neither, it will search for anchor tags
	and form tags and attach lightbox methods to them.

	You can also use the jQuery.lightbox methods directly to incorrporate
	your own plugins:

	$('a#slideshow_link').bind('click', function() {
		$.lightbox.load(this.href, null, function() {
			$('.slideshow', $.lightbox.$content).slideshow();
		});
	});

"Magic" Classes:
	{prepend}_close: Anything with the prepend parameter concatinated
		with "_close" will call the jQuery.lightbox.hide() on click.

Author:
	gburns, lightbox

TODO:
	documentation
	create multiple instances of lightboxes for various settings
*/

jQuery.fn.lightbox = function(options) {
	jQuery.lightbox.set(options);
	jQuery.lightbox.create();
	var settings = jQuery.lightbox.getSettings();

	var img_search = /(\.jpg|\.jpeg|\.png|\.gif|\.bmp)$/;

	return this.each(function() {
		var $this = jQuery(this);

		if ( this.nodeName == 'A' ) {
			if ( this.href.match(img_search) ) {
				$this
					.bind('click', function() {
						jQuery.lightbox.showImage(this.href);
						return false;
					});
			} else {
				$this
					.bind('click', function() {
						jQuery.lightbox.load(this.href, null, settings.callback);
						return false;
					});
			}
		} else if ( this.nodeName == 'FORM' ) {
			$this
				.bind('submit', function() {
					if ( this.method.toUpperCase() == 'POST' ) {
						jQuery.lightbox.load(this.action, $this.serializeArray(), settings.callback);
					} else {
						var query = (( this.action.indexOf('?') != -1 ) ? '&' : '?') + $this.serialize();
						jQuery.lightbox.load(this.action + query, null, settings.callback);
					}
					return false;
				});
		}
	});
}

jQuery.lightbox = {
	showing: false,
	$content: null,
	$lightbox: null,
	$overlay: null,
	prepend: 'lightbox',
	width: 400,
	height: 300,
	opacity: 0.8,
	speed: 'slow',
	wrappers: ['n', 's', 'e', 'w', 'nw', 'ne', 'sw', 'se', 'content'],
	closing: false,
	callback: null,
	closecallback: null,
	center: true,
	updated: false,

	// Used for overriding lightbox defaults
	getSettings: function(options) {
		var settings = jQuery.extend({
			width: jQuery.lightbox.width,
			height: jQuery.lightbox.height,
			prepend: jQuery.lightbox.prepend,
			opacity: jQuery.lightbox.opacity,
			speed: jQuery.lightbox.speed,
			callback: jQuery.lightbox.callback,
			wrappers: jQuery.lightbox.wrappers,
			closecallback: jQuery.lightbox.closecallback,
			center: jQuery.lightbox.center
		}, options || {});

		return settings;
	},

	// Set global params
	set: function(options) {
		var settings = jQuery.lightbox.getSettings(options);

		jQuery.lightbox.width = settings.width;
		jQuery.lightbox.height = settings.height;
		jQuery.lightbox.prepend = settings.prepend;
		jQuery.lightbox.opacity = settings.opacity;
		jQuery.lightbox.speed = settings.speed;
		jQuery.lightbox.wrappers = settings.wrappers;
		jQuery.lightbox.callback = settings.callback;
		jQuery.lightbox.closecallback = settings.closecallback;
		jQuery.lightbox.center = settings.center;
	},

	// See if objects have been created
	isCreated: function() {
		if ( jQuery.lightbox.$content && jQuery.lightbox.$lightbox && jQuery.lightbox.$overlay ) return true;
	},

	// Create objects
	create: function(options) {
		var settings = jQuery.lightbox.getSettings(options);

		if ( jQuery.lightbox.isCreated() ) return;

		var $body = jQuery('body');

		jQuery.lightbox.$overlay = jQuery('<div/>')
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
				if ( !jQuery.lightbox.closing ) jQuery.lightbox.hide(this);
				});

		jQuery.lightbox.$lightbox = jQuery('<div/>')
			.attr({id: settings.prepend + '_container'})
			.css({
				position: 'absolute',
				display: 'none',
				zIndex: 10001
				});

		var $container;
		var $old_container = jQuery.lightbox.$lightbox;
		var divs = settings.wrappers;
		for ( var div in divs ) {
			$container = jQuery('<div/>').addClass(settings.prepend + '_' + divs[div]);
			$old_container.append($container);
			$old_container = $container;
		}

		jQuery.lightbox.$content = $old_container;

		$body.append(jQuery.lightbox.$overlay);
		$body.append(jQuery.lightbox.$lightbox);
	},

	// Show objects
	show: function(options, callback) {
		var settings = jQuery.lightbox.getSettings(options);

		if ( jQuery.lightbox.showing ) return;
		jQuery.lightbox.showing = true;

		var $hide_objects = jQuery('iframe:visible, object:visible, embed:visible, select:visible');

		$hide_objects.each(function() {
			var $this = jQuery(this);
			$this
				.attr({
					lightbox_hide: 'true',
					lightbox_src: $this.attr('src'),
					src: null
					})
				.css({visibility: 'hidden'});
		});

		jQuery.lightbox.create(settings);

		var dimensions = jQuery.lightbox.getPageDimensions();

		var $body = jQuery('body');
		var $html = jQuery('html');

		jQuery.lightbox.$overlay
			.css({
				width: $html.outerWidth({margin: 1}) + 'px',
				height: dimensions.ph + 'px',
				opacity: 0
				})
			.show();

		jQuery.lightbox.$lightbox
			.addClass(settings.prepend + '_loading')
			.css({
				opacity: 0
				});

		if ( settings.width && settings.height ) {
			jQuery.lightbox.$content
				.css({
					width: settings.width + 'px',
					height: settings.height + 'px'
					});
		}

		var scroll = jQuery.lightbox.getPageScroll();

		jQuery.lightbox.$lightbox
			.show();

		var to_x = 0;
		var to_y = 0;

		if ( settings.center ) {
			to_x = parseInt((dimensions.vpw - jQuery.lightbox.$lightbox.outerWidth()) / 2) + scroll.x;
			to_y = parseInt((dimensions.vph - jQuery.lightbox.$lightbox.outerHeight()) / 2) + scroll.y;
		} else {
			to_y = scroll.y;
		}

		if ( to_y < 0 ) to_y = 0;
		if ( to_x < 0 ) to_x = 0;

		jQuery.lightbox.$lightbox
			.css({
				top: to_y + 'px',
				left: to_x + 'px'
				});

		jQuery('.' + settings.prepend + '_close', jQuery.lightbox.$content)
			.bind('click', function() {
				jQuery.lightbox.hide(this);
				return false;
			});

		if ( settings.speed ) {
			jQuery.lightbox.$overlay.animate({opacity: settings.opacity}, settings.speed, 'linear', function(){
				jQuery.lightbox.$lightbox.animate({ opacity: 1 }, settings.speed, 'linear', function() {
					if ( jQuery.browser.msie ) jQuery.lightbox.$lightbox[0].filters.alpha.enabled = false;
					if ( callback ) callback();
				});
			});
		} else {
			jQuery.lightbox.$lightbox.css({ opacity: 1 });
			if ( jQuery.browser.msie ) jQuery.lightbox.$lightbox[0].filters.alpha.enabled = false;
			if ( callback ) callback();
			jQuery.lightbox.$overlay.css({ opacity: settings.opacity });
		}

		jQuery(window).bind('resize', jQuery.lightbox.resizeOverlay);
	},

	resizeOverlay: function() {
		var $body = jQuery('body');
		var $html = jQuery('html');

		jQuery.lightbox.$overlay
			.css({
				height: $html.outerHeight({margin:1}) + 'px'
				});

		var dimensions = jQuery.lightbox.getPageDimensions();

		jQuery.lightbox.$overlay
			.css({
				width: $html.outerWidth({margin:1}) + 'px',
				height: dimensions.ph + 'px'
				});
	},

	// Hide objects, clear content
	hide: function(closer) {
		if ( jQuery.lightbox.updated && !confirm("You will lose all changes to this form. OK to proceed?") ) return false;
		jQuery.lightbox.updated = false;

		var settings = jQuery.lightbox.getSettings();

		if ( jQuery.lightbox.$content && jQuery.lightbox.$lightbox && jQuery.lightbox.$overlay ) {
			jQuery.lightbox.closing = true;
			jQuery.lightbox.showing = false;

			if ( settings.speed ) {
				jQuery.lightbox.$lightbox.animate({ opacity: 0 }, settings.speed, function() {
					jQuery.lightbox.$lightbox.hide();
					jQuery.lightbox.$overlay.animate({ opacity: 0 }, settings.speed, function() {
						jQuery.lightbox.$overlay.hide();
						if ( settings.closecallback ) settings.closecallback(closer);
						jQuery.lightbox.$content.html('');
						jQuery.lightbox.closing = false;

						var $hide_objects = jQuery('iframe[lightbox_hide="true"], object[lightbox_hide="true"], embed[lightbox_hide="true"], select[lightbox_hide="true"]');

						$hide_objects.each(function() {
							$this = jQuery(this);
							$this
								.attr({
									lightbox_hide: null,
									src: $this.attr('lightbox_src'),
									lightbox_src: null
									})
								.css({visibility: 'visible'});
						});
					});
				});
			} else {
				jQuery.lightbox.$overlay.hide();
				jQuery.lightbox.$lightbox.hide();
				if ( settings.closecallback ) settings.closecallback(closer);
				jQuery.lightbox.$content.html('');
				jQuery.lightbox.closing = false;
						if ( jQuery.browser.msie ) {
							jQuery('select[lightbox_iehide="true"]')
								.show();
						}
			}
		}
	},

	// Show an image in content box
	// TODO: Figure out how to compensate for 404 errors
	showImage: function(href, speed) {
		jQuery.lightbox.show();

		var img = new Image();

		// once image is preloaded, resize image container
		img.onload = function() {
			jQuery.lightbox.resize({
				width: img.width,
				height: img.height,
				callback: function() { jQuery.lightbox.$content.append(img); }
				});
			img.onload = function(){}; // clear onLoad, IE behaves irratically with animated gifs otherwise

			jQuery.lightbox.$lightbox
				.removeClass(jQuery.lightbox.prepend + '_loading');

			$(img).click(jQuery.lightbox.hide);
		};

		img.src = href;
	},

	// Load selected dom object
	showDom: function(selector, options, fn) {
		var $dom = jQuery(selector);
		if ( !$dom.length ) return;

		jQuery.lightbox.set(options);
		jQuery.lightbox.create();
		jQuery.lightbox.$content.append($dom);

		jQuery.lightbox.show(options, function() {
			jQuery.lightbox.$lightbox
				.removeClass(jQuery.lightbox.prepend + '_loading');
			if ( fn ) fn();
		});
	},

	// Load direct input HTML
	showHtml: function(html, options, fn) {
		jQuery.lightbox.set(options);
		jQuery.lightbox.create();
		jQuery.lightbox.$content.html(html);

		jQuery.lightbox.show(options, function() {
			jQuery.lightbox.$lightbox
				.removeClass(jQuery.lightbox.prepend + '_loading');
			if ( fn ) fn();
		});
	},

	// Load a remote url via jquery's load method
	load: function(href, data, callback, options) {
		//if ( jQuery.lightbox.updated && !confirm("You will lose all changes to this form. OK to proceed?") ) return false;
		jQuery.lightbox.updated = false;

		var settings = jQuery.lightbox.getSettings(options);

		var $container = jQuery('<div/>');

		jQuery.lightbox.show(settings);

		if ( !href.match(/ +/) ) href += ' >:not(script)';

		$container.load(href, data, function() {
		/*$container.load(href + ' >:not(script)', data, function() {
			jQuery('title, meta', $container)
				.remove();*/

			jQuery.lightbox.$content.html($container.html());

			jQuery('select, input, textarea', jQuery.lightbox.$content).bind('change', function() {
				jQuery.lightbox.updated = true;
			});

			jQuery.lightbox.$lightbox
				.removeClass(settings.prepend + '_loading');

			jQuery('.' + settings.prepend + '_close', jQuery.lightbox.$content)
				.bind('click', function() {
					jQuery.lightbox.hide(this);
					return false;
				});

			if ( callback ) callback();
		});
	},

	// Resize lightbox object
	resize: function(options) {
		if ( !jQuery.lightbox.showing ) return;

		var settings = jQuery.extend({
			width: null,
			height: null,
			callback: null
		}, options || {});

		if ( !settings.center ) return;

		jQuery.lightbox.$lightbox.hide();
		jQuery.lightbox.resizeOverlay();

		var dimensions = jQuery.lightbox.getPageDimensions();
		var scroll = jQuery.lightbox.getPageScroll();

		jQuery.lightbox.$lightbox.show();

		var lightbox_width_extra = jQuery.lightbox.$lightbox.outerWidth() - jQuery.lightbox.$content.innerWidth();
		var lightbox_height_extra = jQuery.lightbox.$lightbox.outerHeight() - jQuery.lightbox.$content.innerHeight();

		var to_x = parseInt((dimensions.vpw - settings.width - lightbox_width_extra) / 2) + scroll.x;
		var to_y = parseInt((dimensions.vph - settings.height - lightbox_height_extra) / 2) + scroll.y;

		if ( to_y < 0 ) to_y = 0;
		if ( to_x < 0 ) to_x = 0;

		if ( jQuery.lightbox.speed ) {
			jQuery.lightbox.$content.animate({ width: settings.width + 'px', height: settings.height + 'px' }, jQuery.lightbox.speed, function() {
				if ( settings.callback ) settings.callback();
				jQuery.lightbox.resizeOverlay();
			});
			jQuery.lightbox.$lightbox.animate({ left: to_x + 'px', top: to_y + 'px' }, jQuery.lightbox.speed);
		} else {
			jQuery.lightbox.$content.css({ width: settings.width + 'px', height: settings.height + 'px' });
			jQuery.lightbox.$lightbox.css({ left: to_x + 'px', top: to_y + 'px' });
			settings.callback();
			jQuery.lightbox.resizeOverlay();
		}
	},

	maximize: function(options) {
		var settings = jQuery.extend({
			setWidth: true,
			setHeight: true
		}, options || {});

		var dimensions = jQuery.lightbox.getPageDimensions();
		var scroll = jQuery.lightbox.getPageScroll();
		var scrollbarWidth = jQuery.lightbox.getScrollbarWidth();

		var lightbox_width_extra = jQuery.lightbox.$lightbox.outerWidth() - jQuery.lightbox.$content.innerWidth();
		var lightbox_height_extra = jQuery.lightbox.$lightbox.outerHeight() - jQuery.lightbox.$content.innerHeight();

		var to_x = scroll.x;
		var to_y = scroll.y;

		if ( to_y < 0 ) to_y = 0;
		if ( to_x < 0 ) to_x = 0;

		var to_width = dimensions.vpw - lightbox_width_extra - scrollbarWidth;
		var to_height = dimensions.vph - lightbox_height_extra - scrollbarWidth;

		var height_width = {};

		if ( settings.setHeight ) height_width.height = to_height + 'px';
		if ( settings.setWidth ) height_width.width = to_width + 'px';

		if ( jQuery.lightbox.speed ) {
			jQuery.lightbox.$content.animate(height_width, jQuery.lightbox.speed, function() {
				jQuery.lightbox.resizeOverlay();
			});
			jQuery.lightbox.$lightbox.animate({ left: to_x + 'px', top: to_y + 'px' }, jQuery.lightbox.speed);
		} else {
			jQuery.lightbox.$content.css(height_width);
			jQuery.lightbox.$lightbox.css({ left: to_x + 'px', top: to_y + 'px' });
			jQuery.lightbox.resizeOverlay();
		}
	},

	getPageDimensions: function() {
		var xScroll, yScroll;

		if ( window.innerHeight && window.scrollMaxY ) {	
			xScroll = document.body.scrollWidth;
			yScroll = window.innerHeight + window.scrollMaxY;
		} else if ( document.body.scrollHeight > document.body.offsetHeight ) { // all but Explorer Mac
			xScroll = document.body.scrollWidth;
			yScroll = document.body.scrollHeight;
		} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
			xScroll = document.body.offsetWidth;
			yScroll = document.body.offsetHeight;
		}

		var windowWidth, windowHeight;
		if ( self.innerHeight ) { // all except Explorer
			windowWidth = self.innerWidth;
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
			pageWidth = windowWidth;
		} else {
			pageWidth = xScroll;
		}

		//arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
		return {pw: pageWidth, ph: pageHeight, vpw: windowWidth, vph: windowHeight};
		//arrayPageSize;
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
		if ( jQuery.lightbox.scrollbarWidth ) return jQuery.lightbox.scrollbarWidth;

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
		document.body.removeChild(
		document.body.lastChild);

		// Pixel width of the scroller
		var width = wNoScroll - wScroll;

		jQuery.lightbox.scrollbarWidth = width;

		return width;
	}
}