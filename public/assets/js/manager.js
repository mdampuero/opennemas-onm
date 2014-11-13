$(document).ready(function() {
    var current   = 0;
    var positions = [];

    $(window).scroll(function () {
        positions.push(current);
        current = $(window).scrollTop();

        if (positions.length < 10) {
            return false;
        }

        positions.splice(0, 1);

        if (!$('.filters-navbar').hasClass('show-qr') && current > 0
                && current < positions[7]) {
            $('.filters-navbar').stop(true, true).addClass('show-qr');
        } else if ($('.filters-navbar').hasClass('show-qr')
                && current > positions[7]) {
            $('.filters-navbar').stop(true, true).addClass('hide-qr').delay(250).queue(function() {
                $(this).removeClass('show-qr').removeClass('hide-qr').dequeue();
            });
        } else {
            $(this).removeClass('show-qr').removeClass('hide-qr');
        }
    });
});
