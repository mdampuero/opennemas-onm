	<script type="text/javascript" src="{$params.JS_DIR}/jcarousel/jquery.jcarousel.pack.js"></script>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c056c9a5f92f5b4"></script>
	<script type="text/javascript">
	{literal}
	$(document).ready(function () {
        
        var showLeyend = true;
        var leyendMessage = ['Mostrar leyenda', 'Ocultar leyenda'];
		
        //jCarousel Plugin
        $('#carousel').jcarousel({
            vertical: false,
            scroll: 3,
            auto: 0,
            wrap: 'last',
            initCallback: mycarousel_initCallback
        });
        
        //Carousel Tweaking
        function mycarousel_initCallback(carousel) {
            
            // Pause autoscrolling if the user moves with the cursor over the clip.
            carousel.clip.hover(function() {
                carousel.stopAuto();
            }, function() {
                carousel.startAuto();
            });
            
            jQuery('#carousel-prev').bind('click', function() {
                var actual = $('#slideshow-main').find('li.active');
                var prev = $('#slideshow-main').find('li.active').prev();
                if (prev.length > 0){
                    actual.removeClass('active');
                    prev.addClass('active');
                }
                if(!showLeyend){
                    $('#slideshow-main .content').hide();
                }
                return false;
            });
            
            jQuery('#carousel-next').bind('click', function() {
                var actual = $('#slideshow-main').find('li.active');
                var next = $('#slideshow-main').find('li.active').next();
                if (next.length > 0){
                    actual.removeClass('active');
                    next.addClass('active');
                }
                if(!showLeyend){
                    $('#slideshow-main .content').hide();
                }
                return false;
            });
            
            // Toggle autoslideshow images, so start/stop it.
            jQuery('#start-stop-slideshow').bind('click', function() {
                if(StopCarousel == true){
                    $(this).css('background-color','#999');
                }else{
                    $(this).css('background-color','#505050');
                }
                StopCarousel = !(StopCarousel);
                return false;
            });
            
            $('#hide-leyend').click(function (){
                if(showLeyend == true){
                    $(this).css('background-color','#999');
                    $(this).text(leyendMessage[0]);
                    $('#slideshow-main .content').fadeOut('slow');
                }else{
                    $(this).css('background-color','#505050');
                    $(this).text(leyendMessage[1]);
                    $('#slideshow-main .content').fadeIn('slow');
                }
                showLeyend = !(showLeyend);
                return false;
            });
        }
        
        jQuery('#slideshow-main').hover(function() {
            jQuery('#carousel-prev, #carousel-next').fadeIn('slow');
        }, function() {
            jQuery('#carousel-prev, #carousel-next').fadeOut('slow');
        });
    
        //Front page Carousel - Initial Setup
        $('div#slideshow-carousel a img').css({'opacity': '0.5'});
        $('div#slideshow-carousel a img:first').css({'opacity': '1.0'});
        
        //Combine jCarousel with Image Display
        $('div#slideshow-carousel li a').hover(
            function () {
                    
                if (!$(this).has('span').length) {
                    $('div#slideshow-carousel li a img').stop(true, true).css({'opacity': '0.5'});
                    $(this).stop(true, true).children('img').css({'opacity': '1.0'});
                }
            },
            function () {
                    
                $('div#slideshow-carousel li a img').stop(true, true).css({'opacity': '0.5'});
                $('div#slideshow-carousel li a').each(function () {
                    if ($(this).has('span').length) $(this).children('img').css({'opacity': '1.0'});
                });
                    
            }
        ).click(function () {
    
            $('span.arrow').remove();        
            $(this).append('<span class="arrow"></span>');
            $('div#slideshow-main li').removeClass('active');        
            $('div#slideshow-main li.' + $(this).attr('rel')).addClass('active');	
                
            return false;
        });
        
    
		$('#tabs').tabs();
		$('#tabs2').tabs();
    });

    {/literal}
	</script>