$(document).ready(function() {
    var current = 0;
    var previous = 0;


    $(window).scroll(function () {
        var previous = current;
        current = $(window).scrollTop();

        if (current > 0 && current < previous) {
            $('.filters-navbar').addClass("qr");
        } else {
            $('.filters-navbar').removeClass("qr");
        }
    });
});
