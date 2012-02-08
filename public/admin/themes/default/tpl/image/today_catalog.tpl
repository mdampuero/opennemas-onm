{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/photos.js" defer="defer" language="javascript"}
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
<form action="{$smarty.server.SCRIPT_NAME}" style="margin:0 auto !important;">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t 1=$datos_cat[0]->title}Today images in "%1"{/t}</h2></div>
        <ul class="old-button">
            {if $action neq 'upload'}
            {acl isAllowed="IMAGE_DELETE"}
            <li>
                <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 6);"  onmouseover="return escape('<u>E</u>liminar todos');" name="submit_mult" value="Eliminar todos">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar todos"><br />{t}Delete all{/t}
                </a>
            </li>
            <li>
                <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar"><br />{t}Delete{/t}
                </a>
            </li>
            {/acl}
            <li>
                <button type="button" style="cursor:pointer;width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                    <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                </button>
            </li>
            {/if}

            <li>
                <a class="admin_add" href="{$smarty.server.PHP_SELF}?ction=search&amp;acategory={$category}"name="submit_mult" value="Buscar Imágenes">
                    <img border="0"  src="{$params.IMAGE_DIR}search.png" alt="Buscar Imágenes"><br />{t}Search{/t}
                </a>
            </li>
            {acl isAllowed="IMAGE_CREATE"}
            <li>
                <a class="admin_add" href="{$smarty.server.PHP_SELF}?action=create_improved&amp;category={$category}" name="submit_mult" value="Subir Fotos">
                    <img border="0"  src="{$params.IMAGE_DIR}upload.png" alt="Subir Fotos"><br />{t}New image{/t}
                </a>
            </li>
            {/acl}
            <li>
                <a class="admin_add" href="{$smarty.server.PHP_SELF}?action=category_catalog&amp;category={$category}" name="submit_mult" value="Catálogo de Fotos">
                    <img border="0"  src="{$params.IMAGE_DIR}folder_image.png" alt="Catálogo de Fotos"><br />{t}Photo catalog{/t}
                </a>
            </li>
            <li>
                <a class="admin_add" href="{$smarty.server.PHP_SELF}?action=today_catalog&amp;category={$category}"  name="submit_mult" value="Fotos de Hoy">
                    <img border="0"  src="{$params.IMAGE_DIR}image_today.png" alt="Fotos de Hoy"><br />{t}Today photos{/t}
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="wrapper-content">

    {render_messages}

    {include file="image/_partials/categories.tpl" home="{$smarty.server.PHP_SELF}?action=today_catalog"}

    {include file="image/_partials/_media_browser.tpl"}


</div><!--fin wrapper-content-->
</form>
{/block}
