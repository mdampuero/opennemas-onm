jQuery(document).ready(function(){

    jQuery.fn.fadeToggle = function(speed, easing, callback) {
       return this.animate({opacity: 'toggle'}, speed, easing, callback);
    };
    setInterval(function() {
        jQuery('#teaser-0').fadeToggle('fast', "linear", function(){
          jQuery('#teaser-1').fadeToggle('fast');
        });
    },4000);

});
