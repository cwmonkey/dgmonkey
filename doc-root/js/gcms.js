$(function() {
	var gcms_id;
	var date = new Date();

	// Submit form and put contents of result into the modal
	$.modal.ajaxLoad = function($form, callback, options) {
		var url = $.trim($form.attr('action'));
		if (url) url = (url.match(/^([^#]+)/)||[])[1];
		url = url || window.location.href || '';
		url += ( url.indexOf('?') >= 0 ? '&' : '?' ) + 'ajax=1&' + date.valueOf();

		if ( this.updated && !confirm("You will lose all changes to this form. OK to proceed?") ) return false;
		this.updated = false;

		this.show(options);

		var settings = $.extend(this.settings, options || {});

		if ( settings.speed ) this.$content.animate({opacity: 0}, settings.speed, function() {
			$form.ajaxSubmit({url: url, success: function(data) {
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

				if ( callback ) callback();
			}});
		});
	};


	// Intercept modal submits
	var ajax_submit = function() {
		if ($('#gcms_container #gcms_success').get(0)) {
			/*var $div = $('<div/>');
			$div.load(document.location.href.replace(/ /g, "%20") + ' #' + gcms_id, null, function() {
				$('.' + gcms_id).html($('#' + gcms_id, $div).html());
				$('.' + gcms_id + ' .gcms_edit_link').each(gcms_link);
			});
			$.modal.hide();*/
			window.location.reload(false);
		} else {
			$('#gcms_container form').bind('submit', function() {
				var $this = $(this);
				/*//$.modal.load(this.action + '&ajax=1&' + date.valueOf(), $(this).serializeArray(), ajax_submit, null);
				var url = $.trim($this.attr('action'));
				if (url) url = (url.match(/^([^#]+)/)||[])[1];
				url = url || window.location.href || '';
				url += ( url.indexOf('?') >= 0 ? '&' : '?' ) + 'ajax=1&' + date.valueOf();

				$this.ajaxSubmit({url: url, target: '#status', success: function(data) {
					$.modal.html(data);
					ajax_submit();
				}});*/

				$.modal.ajaxLoad($(this), ajax_submit, null);
				return false;
			});
		}

		$('.revisions').dropdown();

		$('.revisions a', $.modal.$content).each(gcms_link);

		var $toolbar = $('<p id="gcms_toolbar"></p>');
		$toolbar.draggable({target: $.modal.$modal});

		var $close = $('<a href="javascript:;" class="gcms_close">Close</a>');
		$close.bind('click', $.modal.hide);

		var $maximize = $('<a href="javascript:;" class="gcms_maximize">Maximize</a>');
		$maximize.bind('click', function() { $.modal.maximize({setHeight: false}) });

		$toolbar
			.append($maximize)
			.append($close);

		var $resize = $('<p id="gcms_resize">Resize</p>');
		$resize.resizable({target: $.modal.$content, vertical: false});

		$('#gcms_container .gcms_content').prepend($toolbar).append($resize);

		$('textarea', $.modal.$content).each(function() {
			var $this = $(this);
			//$this.attr({wrap: 'off'});
			$this.autosize();
			var $textarea_toolbar = $('<div class="gcms_textarea_toolbar"></div>');

			var $expand = $('<p class="gcms_textarea_expand">Expand</p>');

			$expand
				.resizable({target: $.modal.$content, vertical: false})
				.resizable({target: $this, horizontal: false});

			var $autosize = $('<p class="gcms_textarea_autosize">Autosize</p>');

			$autosize
				.bind('click', function() {
					$this.autosize();
				});

			$textarea_toolbar
				.append($autosize)
				.append($expand);
			$this.after($textarea_toolbar);

            $this.bind('keydown', catchTab);

			if ( $this.attr('scrollHeight') && $this.attr('scrollWidth') ) {
				$this.css({height: $this.attr('scrollHeight'), minWidth: $this.attr('scrollWidth')});
			}
		});

		$('.gcms_val_datetime input', $.modal.$content).datetime();
		$('.gcms_val_date input', $.modal.$content).datetime({format: 'yyyy-mm-dd'});
	}

	// Hide edit links and attach click events to gcms nodes
	var gcms_wordlet = function() {
		var $this = $(this);
		var $link = $($('a', $this).get(0));

		$link.hide();

		$('a', $this).bind('mousedown', function(e) { return false; });

		$this.bind('click', function() {
			gcms_id = $(this).attr('id');
			$.modal.load($link.attr('href') + '&ajax=1&' + date.valueOf(), null, ajax_submit, {autoResize: true, overlay: true, speed: 'fast', opacity: 0.5, center: false, height: null, width: null, wrappers: ['content'], prepend: 'gcms'});
			return false;
		});
	}

	$('.gcms_wordlet').each(gcms_wordlet);

	//var updated = false;

	var gcms_link = function() {
		/*if ( !confirm("You will lose all changes to this form. OK to proceed?") ) return false;
		updated = false;*/

		var $this = $(this);

		$this.bind('click', function() {
			$.modal.load($this.attr('href') + '&ajax=1&' + date.valueOf(), null, ajax_submit, {autoResize: true, overlay: true, speed: 'fast', opacity: 0.5, center: false, height: null, width: null, wrappers: ['content'], prepend: 'gcms'});
			return false;
		});
	}

	$('.gcms_link').each(gcms_link);
});



jQuery.fn.draggable = function(parameters) {
	var settings = jQuery.extend({
		target: null
	}, parameters || {});

	return this.each(function() {
		var mouse_start_x = null;
		var mouse_start_y = null;
		var obj_start_x = null;
		var obj_start_y = null;
		var $this = jQuery(this);
		var $target = settings.target || $this;

		var obj_drag = function(event) {
			$target.css({
				left: obj_start_x + (event.pageX - mouse_start_x),
				top: obj_start_y + (event.pageY - mouse_start_y)
				});
		}

		var return_false = function() { return false; }

		$this
			.mousedown(function(event) {
				mouse_start_x = event.pageX;
				mouse_start_y = event.pageY;

				obj_start_x = parseInt($target.css('left'));
				obj_start_y = parseInt($target.css('top'));

				var $body = jQuery(document.body);
				var old_moz_user_select = $body.css('MozUserSelect');
				$body.css({MozUserSelect: 'none'});

				$body
					.mousemove(obj_drag)
					.mouseup(function() {
						$body.css({MozUserSelect: ''});

						$body
							.unbind('mousemove', obj_drag);

						if (jQuery.browser.msie) {
							$body
								.unbind('dragstart', return_false)
								.unbind('selectstart', return_false);
						}
					});

				if (jQuery.browser.msie) {
					$body
						.bind('dragstart', return_false)
						.bind('selectstart', return_false);
				}
			});
	});
}

jQuery.fn.resizable = function(parameters) {
	var settings = jQuery.extend({
		target: null,
		horizontal: true,
		vertical: true
	}, parameters || {});

	return this.each(function() {
		var mouse_start_x = null;
		var mouse_start_y = null;
		var obj_start_w = null;
		var obj_start_h = null;
		var $this = jQuery(this);
		var $target = settings.target || $this;

		var obj_drag = function(event) {
			if ( settings.horizontal ) {
				$target.css({
					width: obj_start_w + (event.pageX - mouse_start_x) //,
					//height: obj_start_h + (event.pageY - mouse_start_y)
					});
			}

			if ( settings.vertical ) {
				$target.css({
					//width: obj_start_w + (event.pageX - mouse_start_x) //,
					height: obj_start_h + (event.pageY - mouse_start_y)
					});
			}
		}

		var return_false = function() { return false; }

		$this
			.mousedown(function(event) {
				mouse_start_x = event.pageX;
				mouse_start_y = event.pageY;

				obj_start_w = parseInt($target.innerWidth());
				obj_start_h = parseInt($target.innerHeight());

				var $body = jQuery(document.body);
				var old_moz_user_select = $body.css('MozUserSelect');
				$body.css({MozUserSelect: 'none'});

				$body
					.mousemove(obj_drag)
					.mouseup(function() {
						$body.css({MozUserSelect: ''});

						$body
							.unbind('mousemove', obj_drag);

						if (jQuery.browser.msie) {
							$body
								.unbind('dragstart', return_false)
								.unbind('selectstart', return_false);
						}
					});

				if (jQuery.browser.msie) {
					$body
						.bind('dragstart', return_false)
						.bind('selectstart', return_false);
				}
			});
	});
}

// TODO: Make this a plugin

// TAB support
function setSelectionRange(input, selectionStart, selectionEnd) {
  if (input.setSelectionRange) {
    input.focus();
    input.setSelectionRange(selectionStart, selectionEnd);
  }
  else if (input.createTextRange) {
    var range = input.createTextRange();
    range.collapse(true);
    range.moveEnd('character', selectionEnd);
    range.moveStart('character', selectionStart);
    range.select();
  }
}

function replaceSelection (input, replaceString) {
	if (input.setSelectionRange) {
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;
		input.value = input.value.substring(0, selectionStart)+ replaceString + input.value.substring(selectionEnd);
    
		if (selectionStart != selectionEnd){ 
			setSelectionRange(input, selectionStart, selectionStart + 	replaceString.length);
		}else{
			setSelectionRange(input, selectionStart + replaceString.length, selectionStart + replaceString.length);
		}

	}else if (document.selection) {
		var range = document.selection.createRange();

		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			range.text = replaceString;

			 if (!isCollapsed)  {
				range.moveStart('character', -replaceString.length);
				range.select();
			}
		}
	}
}

// We are going to catch the TAB key so that we can use it, Hooray!
function catchTab(e){
	var item;
	if (!e) var e = window.event;
	if (e.target) item = e.target;
	else if (e.srcElement) item = e.srcElement;
	if (item.nodeType == 3) // defeat Safari bug
		item = item.parentNode;

	if(navigator.userAgent.match("Gecko")){
		c=e.which;
	}else{
		c=e.keyCode;
	}
	if(c==9){
		replaceSelection(item,String.fromCharCode(9));
		setTimeout("document.getElementById('"+item.id+"').focus();",0);	
		return false;
	}
		    
}
