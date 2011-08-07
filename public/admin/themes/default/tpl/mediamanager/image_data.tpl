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
            <div class="title"><h2> {t 1=$datos_cat[0]->title}Image manager:: Editing "%1"{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$photo1->id}', 'form_upload');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self', 'updateDatasPhotos', '{$photo1->id}');">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir"  alt="Guardar y salir" />
                        <br />
                        {t}Save{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    {if !isset($smarty.request.stringSearch)}
                        <a href="{$smarty.server.PHP_SELF}?action={$smarty.session.desde}&category={$smarty.request.category}" class="admin_add" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
                    {else}
                        <a href="search_advanced.php?action=search&stringSearch={$smarty.request.stringSearch}&page={$smarty.request.page}" class="admin_add" value="Cancelar" title="Cancelar">
                    {/if}
                         <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {include file="mediamanager/_partials/photo_data.tpl" display='inline'}

        <input type="hidden" name="category" value="{$photo1->category}" />

    </div>
<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}
