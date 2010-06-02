{*
    OpenNeMas project

    @theme      Lucidity
*}

<div id="info-top" class="clearfix span-24">

    <div id="current-day">
         {insert name="time"} 
    </div>

    {include file="widget_weather.tpl"}

</div>

<div id="logo" class="clearfix">

    <div id="logo-image" class="span-8" style="width:700px;">
        {if $request->getControllerName() == 'video' }

            <a href="/" title="Pulse aquí para ir a la portada de {$smarty.const.SITE_TITLE}">
                <img src="{$params.IMAGE_DIR}/main-logo.big-black.png" alt="{$smarty.const.SITE_TITLE}" />
                <img src="{$params.IMAGE_DIR}/logo-sections/video.png" alt="Video" />
            </a>

        {else}
         <a href="/">
            {if ($category_name eq 'home')}
                <img  class="transparent-logo" src="{$params.IMAGE_DIR}main-logo.big.png" alt="{$smarty.const.SITE_TITLE}" />
            {else}
                <img class="transparent-logo" alt="{$smarty.const.SITE_TITLE}" src="{$params.IMAGE_DIR}main-logo.small.png" >
                {if !empty($category_data.logo)}
                    <img src="/media/sections/{$category_data.logo}" alt="{$category_data.title}"  />
                {/if}
            {/if}
         </a>
        {/if}
    </div>

    <div class="span-16">
        
    </div>

</div>