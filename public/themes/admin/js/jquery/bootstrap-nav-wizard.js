$(document).ready(function() {

    var navListItems = $('.nav-wizard li a');
    var allWells     = $('.wizard-content');

    allWells.hide();

    navListItems.click(function(e) {
        e.preventDefault();
        var $li = $(this).parent();
        var $target = $($(this).attr('href'));

        if (!$li.hasClass('disabled')) {
            navListItems.closest('li').removeClass('active');
            $li.addClass('active');

            allWells.hide();
            $target.show();
        }
    });

    $('.nav-wizard li.active a').trigger('click');

    $('.wizard .activate').on('click', function(e) {
        if (!$(this).hasClass('disabled')) {
            var $target = $(this).data('target');
            $('.nav-wizard li a[href=' + $target + ']').parent().removeClass('disabled');
            $('.nav-wizard li a[href=' + $target + ']').trigger('click');
        }
    });

    $('.wizard #accept-terms').on('click', function() {
        var target   = $('.terms-accepted').data('target')
        var accepted = $(this).is(':checked');

        var url = $(this).data('url');

        $.ajax({
            url: url,
            type: 'post',
            data: { accept : accepted }
        });

        if ($(this).is(':checked')) {
            $('.terms-accepted').removeClass('disabled');
        } else {
            $('.terms-accepted').addClass('disabled');
            $('.nav-wizard li a[href=#step3]').parent().addClass('disabled');
            $('.nav-wizard li a[href=#step4]').parent().addClass('disabled');
        }
    });
});
