$(function () {
    var $body = $('body');
    var $nav = $('#nav');

    $body.delegate('#nav', 'click touchstart', function () {
        if ($nav.is('.clickedon')) {
            $nav.removeClass('clickedon');
        } else {
            $nav.addClass('clickedon');
        }
    })
        .delegate('#nav a', 'click touchstart', function (e) {
        e.stopPropagation();
    })
        .delegate('#nav', 'focusin', function (e) {
        $nav.addClass('clickedon');
    })
        .delegate('#nav', 'focusout', function (e) {
        $nav.removeClass('clickedon');
    });

    /* Home */
    /*$('#home #nav ul').each(function() {
		var $nav = $(this);
		var width = $nav.outerWidth();
		var height = $nav.outerHeight();
		$nav.css({width: 0});
		$nav.animate({width: width}, 'slow', 'swing', function() { });
	});*/

    /* Tournament Registration */
    $body.delegate('#registration #content form', 'submit', function (e) {
        var $form = $(this).closest('form');
        $('input[type="text"], select', $form).each(function () {
            var $input = $(this);
            if (!$input.val()) {
                alert('Please fill out your Full Name, Disc, Shirt Size and/or PDGA# to continue');
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    });

    $('.movie').each(function () {
        if ($.browser.msie) return;
        var $this = $(this);
        var $object = $('object', this);
        var $embed = $('embed', this);
        var $play = $('<a href="javascript:;" />');
        var $hide = $('<a href="javascript:;" />');
        var $toolbar = $('<span />');
        var $alt = $('.movie_alt', this);

        $object.wrap('<span class="object_wrapper"></span>');

        var $object_span = $('span.object_wrapper', this);

        // var object_html = $('<p/>').append($object.eq(0).clone()).html(); //$object.html(); //($.browser.msie)?$object[0].outerHTML:'';
        var $object_clone;

        $toolbar.addClass('movie_toolbar');

        var play_click = function (e) {
            if ($.browser.msie) {
                $object_span.append($object_clone);
                $object = $object_clone;
                $embed = $('embed', $object);
            }
            $play.hide();
            $object.show();
            $embed.show();
            $hide.show();
            $alt.hide();
            e.stopImmediatePropagation();
            return false;
        };

        $play.html('Play Video')
            .addClass('movie_play')
            .bind('click', play_click);

        $('.movie_play', this).bind('click', play_click);

        var hide = function () {
            $play.show();
            $object.hide();
            $embed.hide();
            $hide.hide();
            $alt.show();
            if ($.browser.msie) {
                $object_clone = $object.clone(true);
                $object.remove();
            }
        };

        $hide.html('Close Video')
            .addClass('movie_close')
            .bind('click', hide);

        hide();

        $toolbar.append($play)
            .append($hide);

        $this.append($toolbar);
    });

    $("a[href$='.jpg'], a[href$='.gif'], a[href$='.png']").modal();

    /* $("a[href^='http://'], a[href$='.pdf']") */
    $("a[href$='.pdf']")
        .click(function () {
        window.open(this.href, '_blank');
        return false;
    });

    $("a[href$='.pdf']").each(function () {
        $(this).addClass('pdf_link');
    });
});