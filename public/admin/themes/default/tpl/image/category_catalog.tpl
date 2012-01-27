{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/mediamanager.css"}
{/block}

{block name="footer-js" append}
    {script_tag src="/photos.js" defer="defer" language="javascript"}

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
<form action="#" method="post" name="formulario" id="formulario">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>
            {if $datos_cat[0]}
                {t 1=$datos_cat[0]->title}Image manager:: Images for category "%1"{/t}
            {elseif $category eq "2"}
                    {t}Image manager:: Images for category "Advertisement"{/t}
            {else}
                {t}Image manager:: Images for category "GLOBAL"{/t}
            {/if}</h2></div>
            <ul class="old-button">
                {acl isAllowed="IMAGE_DELETE"}
                <li>
                    <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 6);"  name="submit_mult" value="Eliminar todos">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar todos"><br />{t}Delete all{/t}
                    </a>
                </li>
                <li>
                    <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  name="submit_mult" value="Eliminar">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar"><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                <li>
                    <button type="button" style="cursor:pointer;  border: 0px;"
                            onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                        <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="{t}Select all{/t}" >
                    </button>
                </li>

                <li>
                    <a class="admin_add" href="{$smarty.server.PHP_SELF}?action=search&amp;category={$category}"name="submit_mult" value="Buscar Imágenes">
                        <img border="0" src="{$params.IMAGE_DIR}search.png" alt="{t}Search images{/t}"><br />{t}Search{/t}
                    </a>
                </li>
                {acl isAllowed="IMAGE_CREATE"}
                <li>
                    <a class="admin_add" href="{$smarty.server.PHP_SELF}?action=create&amp;category={$category}" name="submit_mult" value="Subir Fotos">
                        <img border="0" src="{$params.IMAGE_DIR}upload.png" alt="{t}Upload{/t}"><br />{t}New image{/t}
                    </a>
                </li>
                {/acl}
                <li>
                    <a class="admin_add" href="{$smarty.server.PHP_SELF}?action=category_catalog&amp;category={$category}" name="submit_mult" value="Catálogo de Fotos">
                        <img border="0" src="{$params.IMAGE_DIR}folder_image.png" alt="{t}Catalog{/t}"><br />{t}Catalog{/t}
                    </a>
                </li>
                <li>
                    <a class="admin_add" href="{$smarty.server.PHP_SELF}?action=today_catalog&amp;category={$category}"  name="submit_mult" value="Fotos de Hoy">
                        <img border="0" src="{$params.IMAGE_DIR}image_today.png" alt="{t}Today catalog{/t}"><br />{t}Today catalog{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        {include file="image/_partials/categories.tpl" home="{$smarty.server.PHP_SELF}?action=category_catalog"}

        {include file="image/_partials/media-browser.tpl"}


    </div>

    <input type="hidden" id="id" name="id" value="" />
</form>
{/block}
