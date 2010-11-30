<div id="wrapper" class="gallery-wrapper">
        <div id="title">
            <span class="datetime"><small><strong>{$gallery->created|date_format:"%A, %e de %B de %Y"}</strong>  {if isset($gallery->updated)}&Uacute;ltima actualizacion {$gallery->updated|date_format:"%A, %e de %B de %Y"}{/if}</small></span>
            <h2>{$gallery->title}</h2>
        </div>

		<div id="slideshow-main">

            <a id="carousel-next" href="#">&nbsp;</a>
            <a id="carousel-prev" href="#">&nbsp;</a>

            <ul>
            {foreach name=slideshow key=k item=photo from=$albumPhotos2}
                <li class="p{$smarty.foreach.slideshow.index} {if $smarty.foreach.slideshow.index == 0}active{/if}">
                    <a href="#">
                        <img height="538" src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo.photo->path_file}{$photo.photo->name}" alt="{$photo.photo->description|clearslash|escape:'html'}" longdesc="{$photo.photo->description}" class="image13">
						<span class="content"><p>{$photo.photo->description}</p></span>
					</a>
				</li>
            {/foreach}
			</ul>
		</div>

		<div id="slideshow-carousel">
			  <ul id="carousel" class="jcarousel jcarousel-skin-tango">
                {foreach name=slideshow2 key=k item=photo from=$albumPhotos2}
                    <li ><a href="#" rel="p{$smarty.foreach.slideshow2.index}"><img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo.photo->path_file}{$photo.photo->name}" alt="{$photo.photo->description|clearslash|escape:'html'}" longdesc="{$photo.photo->description}" class="image13"></a></li>
                {/foreach}
			  </ul>
		</div>

		<div class="clear"></div>

        <div id="gallery-toolbar" class="clearfix">

            <div class="left">
                <div class="actual-position">
                    {include file="utilities/widget_ratings.tpl" }
                </div>
            </div>
            <div class="right">
                <div class="shares-toolbar">
                    <ul id="shares-toolbar">
                        <li class="first">Compartir esta galer&iacute;a:</li>
                        <li class="share-on-mail"><a href="#" class="addthis_button_email"><img src="{$params.IMAGE_DIR}share-icons/mail.png" alt="" /></a></li>
                        <li class="share-on-sharethis"><a href="#" class="addthis_button_more"><img src="{$params.IMAGE_DIR}/share-icons/sharethis.png" alt="" /></a></li>
                        <li class="share-on-twitter"><a href="#" class="addthis_button_twitter"><img src="{$params.IMAGE_DIR}/share-icons/twitter.png" alt="" /></a></li>
                        <li class="share-on-facebook"><a href="#" class="addthis_button_facebook"><img src="{$params.IMAGE_DIR}/share-icons/facebook.png" alt="" /></a></li>
                    </ul>
                </div>
                <div class="control-buttons">
                    <ul>
                        <!--<li><a href="#" id="start-stop-slideshow">Detener presentaci&oacute;n</a></li>-->
                        <li><a href="#" id="hide-leyend">Ocultar leyenda</a></li>
                    </ul>
                </div>
            </div>

        </div>

	</div>
