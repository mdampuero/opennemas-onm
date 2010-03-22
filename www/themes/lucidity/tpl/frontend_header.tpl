{*
    OpenNeMas project

    @theme      Lucidity
*}

 <div id="info-top" class="clearfix span-24">

    <div id="current-day">
        19:35 del Jueves, 18 de Febrero de 2010
    </div>

     {include file="widget_weather.tpl"}

</div>

<div id="logo" class="clearfix">

    <div id="logo-image" class="span-8" style="width:700px;">
        {if preg_match('/video\.php/',$smarty.server.SCRIPT_NAME) }

            <a href="/" title="Pulse aquí para ir a la portada de {$smarty.const.SITE_TITLE}">
                    <img src="{$params.IMAGE_DIR}/main-logo.small.black.png" alt="{$smarty.const.SITE_TITLE}" />
                    <img src="{$params.IMAGE_DIR}/logo-sections/video.png" alt="Video" />
            </a>

        {else}
         <a href="/">
            <img class="transparent-logo" src="{$params.IMAGE_DIR}main-logo.small.png" alt="{$smarty.const.SITE_TITLE}" />
            {if !empty($category_data.logo)}
                <img src="/media/sections/{$category_data.logo}" alt="{$category_data.title}"  />
            {/if}
         </a>
        {/if}
    </div>

    <div class="span-16">

    </div>

</div>