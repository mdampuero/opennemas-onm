{*
    OpenNeMas project

    @category   OpenNeMas
    @package    OpenNeMas
    @theme      Lucidity

    @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)

    Smarty template: frontpage.tpl
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">

    {include file="module_head.tpl"}

    <body>
        {include file="widget_ad_top.tpl"}

        <div class="wrapper video clearfix">
            <div class="container clearfix span-24">
                <div id="header" class="">

                    {include file="frontend_header.tpl"}

                    {include file="frontend_menu.tpl"}

                </div>

                <div id="main_content" class="single-article span-24">
                    <div class="span-24">
                        <div class="layout-column first-column span-16">
	                    <div class="span-16 toolbar">
                                <div class="vote-block span-10 clearfix">
                                        <div class="vote">
                                                {include file="widget_votes.tpl"}
                                        </div>
                                </div><!-- /vote-block -->

                                <div class="utilities span-6 last">
                                    <ul>
                                        <li><img src="images/utilities/share-black.png" alt="Share" /></li>
                                    </ul>
                                </div><!-- /utilities -->

                            </div><!--fin toolbar -->

                            <div id="main-video">
		                <div id="video-content" class="clearfix span-16">
                                    <object width="601" height="338">
                                        <param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=9851483&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" />
                                        <embed src="http://vimeo.com/moogaloop.swf?clip_id=9851483&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="601" height="338"></embed>
                                    </object>
                                </div>
                                <div class="video-explanation">
                                            <h1>Gorillaz lanza su nuevo disco "Plastic Beach"</h1>
                                                            <p class="in-subtitle">
                                                            Este jueves le han preguntado a Xabi Alonso al respecto y el ex jugador
                                                            del Liverpool ha sido tajante: "Cada uno puede comentar lo que quiera pero
                                                            mi relación con CR9 es fantástica. El otro día tiró CR9 un penati, el
                                                            anterior lo tiré yo, pero igual otro día no lo tiro yo", comentó.</p>
                                </div>
							
							
                            </div><!-- .main-video -->
	                    	                    
                        </div>

                        <div class="layout-column last-column opacity-reduced last span-8">
                            {include file="widget_most_videos.tpl"}
						
                    </div>
                </div><!-- span-24 -->
                <div class="span-24">
        		    <hr class="new-separator"/>
                    <div class="span-24 toolbar-bottom ">
                    	
                    	<div class="span-7 utilities-bottom vert-separator">
                    		<ul>
                    			<li class="span-3"><img src="images/utilities/share-black.png" alt="Share" /> Compartir</li>
                    			<li class="span-4 last" onclick="increaseFontSize()" ><img src="images/utilities/increase-text-black.png" alt="Increase text" /> Ampliar el texto</li>
                    			<li class="span-3"><img src="images/utilities/print-black.png" alt="Print" /> Imprimir</li>
                    			<li class="span-4 last" onclick="decreaseFontSize()" ><img src="images/utilities/decrease-text-black.png" alt="Decrease text" /> Reducir el texto</li>

                    		</ul>
                    	</div><!-- /utilities -->
                    	
                    	<div class=" span-7">
                            <div class="vote-black">
                                <div class="vote vert-separator">
                                        Vote
                                        <ul class="voting">
                                                <li><img src="images/utilities/e-star-black.png" alt="Email" /></li>
                                                <li><img src="images/utilities/e-star-black.png" alt="Email" /></li>
                                                <li><img src="images/utilities/e-star-black.png" alt="Email" /></li>
                                                <li><img src="images/utilities/e-star-black.png" alt="Email" /></li>
                                                <li><img src="images/utilities/e-star-black.png" alt="Email" /></li>
                                        </ul><br/><br/>
                                        Resultados
                                        <ul class="voting">
                                                <li><img src="images/utilities/f-star-black.png" alt="Email" /></li>
                                                <li><img src="images/utilities/f-star-black.png" alt="Email" /></li>
                                                <li><img src="images/utilities/s-star-black.png" alt="Email" /></li>
                                                <li><img src="images/utilities/e-star-black.png" alt="Email" /></li>
                                                <li><img src="images/utilities/e-star-black.png" alt="Email" /></li>
                                        </ul>
                                        <br/>
                                </div>
                            </div><!-- /vote-bloc -->
                    	</div><!-- /utilities -->
                    	<div class="span-9 last ">
                            {include file="wdiget_ad_button.tpl"}
                    	</div><!-- /utilities -->
                    	
                    </div><!--fin toolbar-bottom -->

					<hr class="new-separator"/>
                </div>
                <div class="span-24 opacity-reduced">
                    <div class="layout-column first-column span-16">
                        <div class="border-dotted">
							<div class="article-comments">
								<div class="title-comments"><h3><span>7 Comentarios<span></h3></div>
								<div class="utilities-comments">
									<div class="num-pages span-7">Página 1 de 6</div>
									<div class="span-9 pagination last clearfix">
										<ul>
											<li class="active"><a href="#">1</a></li>				
											<li><a href="#">2</a></li>
											<li><a href="#">3</a></li>
										...
											<li><a href="#">9</a></li>
											<li class="next"><a href="#">Siguiente</a></li>
										</ul>
									</div>
								</div><!-- .utilities-comments -->
							<div class="list-comments span-16">
								<div class="comment-wrapper clearfix">
									<div class="comment-number">9</div>
									<div class="comment-content span-14 prepend-2">
										"Es muy buen jugador, no lo voy a descubrir yo. Lleva muchos años a un gran nivel 
										 y no me sorprendió nada de lo que ví ayer".
									</div>
									<div class="">
										<div class="span-5"> <img src="images/vote-down-black.png" />&nbsp;<img src="images/vote-up-black.png" /> 6 votos</div>
										<div class="span-10">
											escrito por 
											<span class="comment-author">lenine100@hotmail.com</span> 
											hace 
											<span class="comment-time">7 horas 59 minutos</span>
										</div>
									</div>
								</div><!--comment-wrapper-->
								
								<div class="comment-wrapper clearfix">
									<div class="comment-number">10</div>
									<div class="comment-content span-14 prepend-2">
										"Es muy buen jugador, no lo voy a descubrir yo. Lleva muchos años a un gran nivel 
										 y no me sorprendió nada de lo que ví ayer".
									</div>
									<div class="">
										<div class="span-5"> <img src="images/vote-down-black.png" />&nbsp;<img src="images/vote-up-black.png" /> 6 votos</div>
										<div class="span-10">
											escrito por 
											<span class="comment-author">lenine100@hotmail.com</span> 
											hace 
											<span class="comment-time">7 horas 59 minutos</span>
										</div>
									</div>
								</div><!--comment-wrapper-->
							</div>
							</div>
						</div>
                    </div>
                    
                    <div class="layout-column last-column last span-8">
                        {include file=""}
                    </div>
                    
                </div>

            </div><!-- fin #main_content -->

        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->

    <div class="wrapper clearfix">

       <div class="container clearfix span-24">
              {include file="frontend_footer.tpl"}
        </div><!-- fin .container -->


    </div>
    
    <script type="text/javascript" src="javascripts/jquery-1.4.1.min.js"></script>
    <script type="text/javascript" src="javascripts/jquery-ui.js"></script>
    <script type="text/javascript" src="javascripts/functions.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            $('#tabs').height($('#video-content').height()+15);
            $('#').height($('#video-content').height()+15);
        });
    </script>
  </body>
</html>
