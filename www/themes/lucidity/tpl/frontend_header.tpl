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

    <div id="logo-image"class="span-8">
        {if $category_name eq 'deportes'}
            <img src="{$params.IMAGE_DIR}/main-logo.small.green.png" alt="Crónica comarcal" />
            <img src="{$params.IMAGE_DIR}/logo-sections/sports.png" alt="Deportes" />
        {else}
         <a href="/"><img src="{$params.IMAGE_DIR}/main-logo.big.png" alt="Crónica comarcal" /></a>
        {/if}
    </div>

    <div class="span-16">

    </div>

</div>