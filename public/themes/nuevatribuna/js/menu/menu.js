$(document).ready(function() {
    var ul_subsections = $('div#submenu ul li.active').find('ul.nav');
    if(ul_subsections.length > 0) {
        // There is subsections so lets handle them.
        var submenu_width = 0;
        ul_subsections.find('li').each(function(index) {
            submenu_width += $(this).width();
        });
        var submenu_too_big = (ul_subsections.position().left + submenu_width) > $('div#submenu').width();
        if(submenu_too_big){
            ul_subsections.each(function(){
                $(this).css('right','80px')
            });
        }
    }
    
    $("div#submenu ul li").hover(
        function(){
            if(!$(this).hasClass('active')){
                $("div#submenu > ul > li.active").each(function(index) {
                    $(this).toggleClass("active");
                });
            };
        },
        function(){
            $("div#submenu > ul > li."+current_section).addClass("active");
        }
    );
 });