{*
    OpenNeMas project

    @theme      Clarity
*}

<div id="logo" class="clearfix span-24">

    <div id="logo-image" class="span-8 clearfix">
        <a href="/"><img src="{$params.IMAGE_DIR}main-logo.big.png" class="transparent-logo" alt="También Arquitectura" /></a>
    </div>

    <div id="info-top" class="span-16">
        <ul>
            <li><a href="/estaticas/quen.html">Quienes somos</a></li>
            <li><a href="#">Mapa del sitio</a></li>
            <li class="last"><a href="/estaticas/contacto.html">Contacto</a></li>
        </ul>
    </div>
{if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)}
<div id="description-category">
    <ul>
        <li class="">INTERIORISMO</li>
    </ul>
</div>
{else}
    <div id="description">
        <ul>
            <li>su lugar de <span class="red">a</span>rquitectura,</li>
            <li>diseño, <span class="red">i</span>nteriorismo, exposiciones,<li>
            <li>arquitectura  <span class="red">s</span>ostenible, formación ...</li>
        </ul>
    </div>
{/if}
</div>