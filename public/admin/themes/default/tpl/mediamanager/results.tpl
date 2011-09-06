{extends file="base/admin.tpl"}


{block name="footer-js" append}
    <script defer="defer" type="text/javascript" language="javascript" src="{$params.JS_DIR}photos.js"></script>
    {if isset($smarty.request.message) && strlen($smarty.request.message) > 0}
        <div class="message" id="console-info">{$smarty.request.message}</div>
        <script defer="defer" type="text/javascript">
            new Effect.Highlight('console-info', {ldelim}startcolor:'#ff99ff', endcolor:'#999999'{rdelim})
        </script>
    {/if}

    <script defer="defer" type="text/javascript">
    function confirmar(url) {
        if(confirm('¿Está seguro de querer eliminar este fichero?')) {
            location.href = url;
        }
    }
    </script>

    {if !empty($smarty.request.alerta)}
    <script type="text/javascript">
        alert("NO SE PUEDE ELIMINAR {$smarty.request.name} .\n Esta imagen está siendo utilizada en: {$smarty.request.alerta}.");
    </script>
    {/if}
{/block}

{block name="content"}
<form id="form_upload" action="{$smarty.server.SCRIPT_NAME}?action=updateDatasPhotos" method="POST">

    <div class="top-action-bar">
        <div class="wrapper-content">
            <div style='float:left;margin-left:10px;margin-top:10px;'><h2></h2></div>
            <div class="title"><h2> {t}Media manager{/t} :: {t 1=$datos_cat[0]->title}Editing image '%1'{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self', 'updateDatasPhotos', '');">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir"  alt="Guardar y salir" />
                        <br />
                        {t}Save{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self','{$smarty.session.desde}', 0);" value="Cancelar" title="Cancelar">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" />
                        <br />
                        {t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        <ul class="pills">
            <li>
                <a href="mediamanager.php?listmode={$listmode|default:""}&category=GLOBAL" {if $category==0}class="active"{/if}>
                    {t}ALL{/t}</a>
            </li>
            {* <li>
                 <a href="{$home}?listmode={$listmode|default:""}&category=3" {if $category==3} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>
                     ALBUMS</a>
             </li>
             *}

            {acl isAllowed="ADVERTISEMENT_ADMIN"}
             <li>
                 <a href="{$smarty.server.PHP_SELF}?listmode={$listmode|default:""}&category=2" {if $category==2}class="active"{/if}>
                     PUBLICIDAD</a>
             </li>
             {/acl}
            {include file="menu_categories.tpl" home="mediamanager.php?listmode="}

        </ul>

        <input type="hidden" name="category" value="{$category}" />

        <div id="media_msg" style="float:right;width:300px;display:none;"> </div>

        {if !empty($smarty.request.mensaje)}
            <script type="text/javascript">
            showMsgContainer({ 'warn': ['Ocurrió algún error al subir: <br /> {$smarty.request.mensaje}. <br /> Compruebe su tamaño (MAX 300 Kb). <br /> ' ]},'inline','media_msg');
            </script>
        {/if}

        <input type="hidden" name="category" value="{$smarty.request.category}" />

        {section name=n loop=$photo}
            {include file="mediamanager/_partials/photo_data.tpl" display="none" photo1=$photo[n]}
        {/section}

    </div><!--wrapper-content-->

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}
