(function(window, $, undefined) {
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

		$('form.wordlets', $.modal.$content).each(function() {
			if ( $('#wysiwyg').val() == 1 ) {
				$('#value')
					.wysiwyg()
					.closest('.wysiwyg_container').find('.wysiwyg_html').addClass('cms');
			}
		});
	};

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
	};

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
	};

	$('.gcms_link').each(gcms_link);
});

$.fn.wysiwyg = function(parameters) {
	var settings = jQuery.extend({
		prepend: 'wysiwyg_'
	}, parameters || {});

	var prepend = settings.prepend;

	return this.each(function() {
		var $container = $('<div/>').addClass(prepend + 'container');
		var $wysiwyg = $('<div/>')
			.attr({contenteditable: 'true'})
			.addClass(prepend + 'html')
			.css({overflow: 'hidden'});
		var $textarea = $(this);
		var $textarea_wrapper = $('<div/>')
			.addClass(prepend + 'textarea')
			.css({overflow: 'hidden'});
		var $ctrl_wrapper = $('<div/>')
			.addClass(prepend + 'ctrl_container');
		var $ctrl_code = $('<a href="#"/>')
			.html('Code')
			.bind('click', function() {
				$textarea.focus();
				return false;
			});
		var $ctrl_lines = $('<select/>')
			.addClass(prepend + 'lines')
			.bind('change', function() {
				var savedSel = rangy.saveSelection();

				var el = window.getSelection().focusNode.parentNode;
				var elhtml = el.outerHTML;
				var val = $ctrl_lines.val();
				var searcho = new RegExp('^<' + el.tagName + '', 'i');
				var searchc = new RegExp('\/' + el.tagName + '>$', 'i');

				elhtml = elhtml.replace(searcho, '<' + val).replace(searchc, '/' + val + '>');

				$elnew = $(elhtml);
				$(el).replaceWith($elnew);

				rangy.restoreSelection(savedSel);
			});
		var lines = [
			{value: 'P', text: 'Paragraph'},
			{value: 'H1', text: 'Header 1'},
			{value: 'H2', text: 'Header 2'},
			{value: 'H3', text: 'Header 3'},
			{value: 'H4', text: 'Header 4'},
			{value: 'H5', text: 'Header 5'},
			{value: 'H6', text: 'Header 6'}
		];

		var val = $textarea.val();
		if ( val.charAt(0) != '<' ) val = '<p>' + val + '</p>';

		for ( var i in lines ) {
			var line = lines[i];
			var $option = $('<option value="' + line.value + '">' + line.text + '</option>');
			$ctrl_lines.append($option);
		}

		$container
			.append(
				$ctrl_wrapper
					.append(
						$ctrl_code,
						$ctrl_lines
					)
			)
			.insertAfter($textarea)
			.append($textarea);

		$textarea_wrapper
			.insertAfter($textarea)
			.append($textarea);

		$wysiwyg
			.insertAfter($textarea_wrapper)
			.bind('blur keyup paste', function() {
				$textarea.val(
					cleanHTML($wysiwyg.html())
				);
			})
			.bind('focus', function() {
				$textarea_wrapper.css({height: 0});
				$wysiwyg.css({height: 'auto'});
			})
			.bind('click keyup paste', function() {
				var el = window.getSelection().focusNode.parentNode;
				$ctrl_lines.val(el.tagName);
				console.log(el.tagName);
			})
			.html(val);

		$textarea
			.val($wysiwyg.html())
			.bind('change', function() {
				$wysiwyg.html($textarea.val());
			})
			.bind('focus', function() {
				$textarea_wrapper.css({height: 'auto'});
				//$wysiwyg.css({height: 0});
			})
			.css({minHeight: 0});

		$wysiwyg.focus();
	});
};

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
		};

		var return_false = function() { return false; };

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
};

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
		};

		var return_false = function() { return false; };

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
};

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
})(window, jQuery);

// http://tools.arantius.com/tabifier
//gogo global variable
var level=0;

function finishTabifier(code) {
	code=code.replace(/\n\s*\n/g, '\n');  //blank lines
	code=code.replace(/^[\s\n]*/, ''); //leading space
	code=code.replace(/[\s\n]*$/, ''); //trailing space

	document.getElementById('o_code').value=code;
	level=0;

	hideProgress();
}

function showProgress(done, total) {
	var perc=Math.floor(100*done/total);

	var bar=document.getElementById('bar');
	bar.innerHTML=perc+'%\u00A0';
	bar.style.width=perc+'%';
	bar.style.visibility='visible';
}

function hideProgress() {
	var bar=document.getElementById('bar');
	bar.style.visibility='hidden';
}

function cleanHTML(code) {
	var i=0;
	function cleanAsync() {
		var iStart=i;
		for (; i<code.length; i++) {
			point=i;

			//if no more tags, copy and exit
			if (-1==code.substr(i).indexOf('<')) {
				out+=code.substr(i);
				finishTabifier(out);
				return;
			}

			//copy verbatim until a tag
			while (point<code.length && '<'!=code.charAt(point)) point++;
			if (i!=point) {
				cont=code.substr(i, point-i);
				if (!cont.match(/^\s+$/)) {
					if ('\n'==out.charAt(out.length-1)) {
						out+=tabs();
					} else if ('\n'==cont.charAt(0)) {
						out+='\n'+tabs();
						cont=cont.replace(/^\s+/, '');
					}
					cont=cont.replace(/\s+/g, ' ');
					out+=cont;
				} if (cont.match(/\n/)) {
					out+='\n'+tabs();
				}
			}
			start=point;

			//find the end of the tag
			while (point<code.length && '>'!=code.charAt(point)) point++;
			tag=code.substr(start, point-start);
			i=point;

			//if this is a special tag, deal with it!
			if ('!--'==tag.substr(1,3)) {
				if (!tag.match(/--$/)) {
					while ('-->'!=code.substr(point, 3)) point++;
					point+=2;
					tag=code.substr(start, point-start);
					i=point;
				}
				if ('\n'!=out.charAt(out.length-1)) out+='\n';
				out+=tabs();
				out+=tag+'>\n';
			} else if ('!'==tag[1]) {
				out=placeTag(tag+'>', out);
			} else if ('?'==tag[1]) {
				out+=tag+'>\n';
			} else if (t=tag.match(/^<(script|style)/i)) {
				t[1]=t[1].toLowerCase();
				tag=cleanTag(tag);
				out=placeTag(tag, out);
				end=String(code.substr(i+1)).toLowerCase().indexOf('</'+t[1]);
				if (end) {
					cont=code.substr(i+1, end);
					i+=end;
					out+=cont;
				}
			} else {
				tag=cleanTag(tag);
				out=placeTag(tag, out);
			}
		}

		//showProgress(i, code.length);
		//if (i<code.length) {
			//setTimeout(cleanAsync, 0);
		//} else {
		//	finishTabifier(out);
		//}
		return out.replace(/^\s+|\s+$/g, '');
	}

	var point=0, start=null, end=null, tag='', out='', cont='';
	return cleanAsync();
}

function tabs() {
	var s='';
	for (var j=0; j<level; j++) s+='\t';
	return s;
}

function cleanTag(tag) {
  var tagout='';
  tag=tag.replace(/\n/g, ' ');       //remove newlines
  tag=tag.replace(/[\s]{2,}/g, ' '); //collapse whitespace
  tag=tag.replace(/^\s+|\s+$/g, ' '); //collapse whitespace
  var suffix='';
  if (tag.match(/\/$/)) {
    suffix='/';
    tag=tag.replace(/\/+$/, '');
  }
  var m, partRe = /\s*([^= ]+)(?:=((['"']).*?\3|[^ ]+))?/;
  while (m = partRe.exec(tag)) {
    if (m[2]) {
      tagout += m[1].toLowerCase() + '=' + m[2];
    } else if (m[1]) {
      tagout += m[1].toLowerCase();
    }
    tagout += ' ';

    // Why is this necessary?  I thought .exec() went from where it left off.
    tag = tag.substr(m[0].length);
  }
  return tagout.replace(/\s*$/, '')+suffix+'>';
}

/////////////// The below variables are only used in the placeTag() function
/////////////// but are declared global so that they are read only once
//opening and closing tag on it's own line but no new indentation level
var ownLine=['area', 'body', 'head', 'hr', 'i?frame', 'link', 'meta',
	'noscript', 'style', 'table', 'tbody', 'thead', 'tfoot'];

//opening tag, contents, and closing tag get their own line
//(i.e. line before opening, after closing)
var contOwnLine=['li', 'dt', 'dt', 'h[1-6]', 'option', 'script'];

//line will go before these tags
var lineBefore=new RegExp(
	'^<(/?'+ownLine.join('|/?')+'|'+contOwnLine.join('|')+')[ >]'
);

//line will go after these tags
lineAfter=new RegExp(
	'^<(br|/?'+ownLine.join('|/?')+'|/'+contOwnLine.join('|/')+')[ >]'
);

//inside these tags (close tag expected) a new indentation level is created
var newLevel=['blockquote', 'div', 'dl', 'fieldset', 'form', 'frameset',
	'map', 'ol', 'p', 'pre', 'select', 'td', 'th', 'tr', 'ul'];
newLevel=new RegExp('^</?('+newLevel.join('|')+')[ >]');
function placeTag(tag, out) {
	var nl=tag.match(newLevel);
	if (tag.match(lineBefore) || nl) {
		out=out.replace(/\s*$/, '');
		out+="\n";
	}

	if (nl && '/'==tag.charAt(1)) level--;
	if ('\n'==out.charAt(out.length-1)) out+=tabs();
	if (nl && '/'!=tag.charAt(1)) level++;

	out+=tag;
	if (tag.match(lineAfter) || tag.match(newLevel)) {
		out=out.replace(/ *$/, '');
		out+="\n";
	}
	return out;
}
