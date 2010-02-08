<a href="/seccion/suplementos/">
    <img src="{$params.IMAGE_DIR}logos/suplementos_xornal.gif" alt="Contexto" style="margin-top: 20px; margin-bottom: 5px;margin-left: 5px;" />
</a>
<div class="separadorHorizontal"></div>
<div style="display:inline;float:left;position:relative;">
    {if isset($frontpage_contexto_img) AND preg_match('/\.jpg$/', $frontpage_contexto_img) }
    <div style="margin-right:5px;text-align: center;">
        <a href="/seccion/suplementos/contexto/" title="Contexto">
            <img src="/media/images/kiosko/{$frontpage_contexto_img}" border="0" alt="Contexto Frontpage"/>
        </a>
    </div>
    {/if}
    <div class="CContendorSuplementos">
        <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
        <a style="color:#004B8E;" href="{$titulares_contexto->permalink|default:"#"}">{$titulares_contexto->title|clearslash}</a>
    </div>
</div>
<div class="fileteFotoVideoDia"></div>
<div style="margin-top:10px;display:inline;float:left;position:relative;">
    {if isset($frontpage_estratexias_img) AND preg_match('/\.jpg$/', $frontpage_estratexias_img) }
    <div style="margin-right:5px;text-align: center;">
        <a href="/seccion/suplementos/estratexias/" title="Estratexias">
            <img src="/media/images/kiosko/{$frontpage_estratexias_img}" border="0" alt="Estratexias Frontpage"/>
        </a>
    </div>
    {/if}
    <div class="CContendorSuplementos">
        <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
        <a style="color:#004B8E;" href="{$titulares_estratexias->permalink|default:"#"}">{$titulares_estratexias->title|clearslash}</a>
    </div>
</div>
{*EXIT & NOS - WILL NOT BE DISPLAYED
<div class="fileteFotoVideoDia"></div>
<div style="margin-top:10px;display:inline;float:left;position:relative;">
    <div style="margin-right:5px;text-align: center;">
        <a href="/seccion/suplementos/exit/" title="Exit">
            <img src="/media/images/exit/logo_exit.jpg" border="0" alt="Exit Frontpage" />
        </a>
    </div>
    <div class="CContendorSuplementos">
        <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
        <a style="color:#004B8E;" href="{$titulares_exit->permalink|default:"#"}">{$titulares_exit->title|clearslash}</a>
    </div>
</div>
<div class="fileteFotoVideoDia"></div>
<div style="margin-top:10px;display:inline;float:left;position:relative;">
    {if isset($frontpage_nos_img) AND preg_match('/\.jpg$/', $frontpage_nos_img) }
    <div style="margin-right:5px;text-align: center;">
        <a href="/seccion/suplementos/nos/" title="N&oacute;s">
            <img src="/media/images/kiosko/{$frontpage_nos_img}" border="0" alt="Nos Frontpage" />
        </a>
    </div>
    {/if}
    <div class="CContendorSuplementos">
        <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
        <a style="color:#004B8E;" href="{$titulares_nos->permalink|default:"#"}">{$titulares_nos->title|clearslash}</a>
    </div>
</div>*}