/* jQuery(document).ready(function(){

    jQuery.fn.fadeToggle = function(speed, easing, callback) {
       return this.animate({opacity: 'toggle'}, speed, easing, callback);
    };
    setInterval(function() {
        jQuery('#teaser-0').fadeToggle('fast', "linear", function(){
          jQuery('#teaser-1').fadeToggle('fast');
        });
    },4000);

});
*/
//need jquery.cycle.all.2.72.js
jQuery(document).ready(function(){
     $('.slide_cicle').cycle({
        fx:     'fade',
        speed:    300,
        delay:  -4000
    });
});