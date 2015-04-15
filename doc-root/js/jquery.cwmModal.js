/*! cwmModal plugin

Usage:
	var modal = $.cwmModal.create();
	modal.$content.append($('#some_node').show());
	modal.show();

*/

;(function($, undefined) {

// For triggering events
var $window = $(window);

// Used for class names and plugin name
var plugin_name = 'cwmModal';

// Modal class
var Modal = function(params) {
	var me = this;
	this.speed = params.speed;
	var name = this.name = params.name;
	this.$body = $('body');
	this.$overlay = $('<div/>').addClass(name + '-overlay ' + plugin_name + '-overlay').hide();
	this.$window = $('<div/>').addClass(name + '-window ' + plugin_name + '-window').hide();
	this.$contentWrapper = $('<div/>').addClass(name + '-content-wrapper ' + plugin_name + '-content-wrapper');
	this.$content = $('<div/>').addClass(name + '-content ' + plugin_name + '-content');
	this.$close = $('<button/>').addClass(name + '-close ' + plugin_name + '-close').html('Close');

	this.$close.bind('click', function() {
		me.hide('button');
	});

	// Close modal on click of overlay
	this.$overlay.bind('click', function() {
		me.hide('overlay');
	});

	this.$window.bind('click', function() {
		me.hide('overlay');
	});

	this.$contentWrapper.bind('click', function(e) {
		e.stopPropagation();
	});

	// Close modal on click of any plugin_name + -close class'd elements
	this.$window.delegate('.' + plugin_name + '-close', 'click', function(e) {
		e.preventDefault();
		me.hide('close-button');
	});

	// Add elements to body
	this.$contentWrapper.append(this.$content, this.$close);

	this.$window.append(this.$contentWrapper);

	this.$body.append(this.$overlay, this.$window);
};

// Show modal
Modal.prototype.show = function() {
	var me = this;
	this.$overlay.show();
	this.$window.show();
	this.$body.addClass(plugin_name + '-showing');

	setTimeout(function() {
		me.$overlay.addClass(plugin_name + '-show');
		me.$window.addClass(plugin_name + '-show');
	}, 10);
};

// Hide modal
Modal.prototype.hide = function(method) {
	var me = this;
	this.$overlay.removeClass(plugin_name + '-show');
	this.$window.removeClass(plugin_name + '-show');

	setTimeout(function() {
		me.$overlay.hide();
		me.$window.hide();
		me.$body.removeClass(plugin_name + '-showing');
	}, this.speed);
};

// Cache modals
var modals = {};

// Plugin
$[plugin_name] = {
	create: function(params) {
		var settings = $.extend({}, {name: plugin_name, speed: 500}, params);

		if ( modals[settings.name] == undefined ) {
			modals[settings.name] = new Modal(settings);
		}

		return modals[settings.name];
	}
};

})(window.jQuery);