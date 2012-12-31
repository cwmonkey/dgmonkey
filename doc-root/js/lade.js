$(function() {
	$('#nav ul').dropdown();
});

jQuery.fn.dropdown = function() {
	return this.each(function() {
		var $this = $(this);
		var has_focus = false;
		$this.hide();
		$this.css({position: 'absolute'});

		var show = function($node) {
			return function() { $node.show(); };
		}($this);

		var hide = function($node) {
			return function() { $node.hide(); };
		}($this);

		$this.parent()
			.bind('mouseover', show)
			.bind('mouseout', function() {
				if (!has_focus) hide();
			})
		
		$('a', $this.parent())
			.bind('focus', function() {
				has_focus = true;
				show();
			})
			.bind('blur', function() {
				has_focus = false;
				setTimeout(function() {
					if (!has_focus) hide();
				}, 0);
			});
	});
}
