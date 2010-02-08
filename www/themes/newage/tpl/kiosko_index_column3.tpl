<div class="column3">
    <div class="actualidadVideosNew">
        {foreach from=$months_kiosko key=k item=v}
        <h2 style="margin-top: 20px; margin-left:4px; font-weight:normal; color:#024685;">Año {$k}</h2>
        <div id="kiosko_menu">
            {section name=i loop=$v}
            <div><a href="/portadas/{$k}/{$v[i]}">{$v[i]|month_spanish} {$k}</a></div>
            {/section}
        </div>
        {/foreach}
    </div>
    <div class="separadorHorizontal"></div>    
    {include file="modulo_weather.tpl"}
</div>
