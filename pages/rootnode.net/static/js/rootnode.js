!function ($) {

    $(function(){

        var $window = $(window);
        var $window_width = 0;

        // Disable certain links in docs
        $('section [href=#]').click(function (e) {
            e.preventDefault()
        })

        // side bar
        $('.bs-docs-sidenav').affix({
            offset: {
                top: function () { return $window.width() <= 980 ? 290 : 210 }
                , bottom: 270
            }
        })

        // make code pretty
        window.prettyPrint && prettyPrint()

        setInterval(function() {
            if($window_width != $window.width()) {
                $window_width = $window.width();
                if($window_width < 768) {
                    $('.icon-chevron-left').removeClass('icon-chevron-left').addClass('icon-chevron-right');
                    $('.bs-docs-sidebar').parent().prepend($('.bs-docs-sidebar'));
                } else {
                    $('.icon-chevron-right').addClass('icon-chevron-left').removeClass('icon-chevron-right');
                    $('.bs-docs-sidebar').parent().append($('.bs-docs-sidebar'));
                }
            }
        }, 100);

        $('#invoice').change(function() {
            var boxHeight = $('#splitdata').height(),
                checked = $(this).is(':checked'),
                topAlign = checked ? -boxHeight : 0;
            $('#privateperson, #company').show();
            $('#privateperson').animate({top: topAlign}, function() {
                $(this).toggle(!checked);
            });
            $('#company').animate({top: topAlign + boxHeight}, function() {
                $(this).toggle(checked);
            });
        }).change();

        $('form.validate-me').submit(function(e) {
            console.log($(this).find('input, textarea, select').closest('.control-group').removeClass('error').end().filter(':visible'));
            $(this).find('input, textarea, select').closest('.control-group').removeClass('error').end().filter(':visible').each(function() {
                if($(this).data('required') == 1 && $(this).val() == '') {
                    $(this).closest('.control-group').addClass('error');
                }
		if($(this).data('format') && !$(this).val().match(RegExp($(this).data('format')))) {
                    $(this).closest('.control-group').addClass('error');
                }
            });
            if($(this).find('.error').size()) {
                e.preventDefault();
            }
        });
    })

}(window.jQuery)
