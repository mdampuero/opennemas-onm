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
<div class="wrapper-content">

    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

        {include file="mediamanager/_partials/categories.tpl"}


        <div id="menu-acciones-admin">
            <div style='float:left;margin-left:10px;margin-top:10px;'><h2> {t 1=$datos_cat[0]->title}Image manager:: Images for category "%1"{/t}</h2></div>
            <ul>
                <li>
                    <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 6);"  name="submit_mult" value="Eliminar todos">
                        <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar todos"><br />Eliminar todos
                    </a>
                </li>
                <li>
                    <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  name="submit_mult" value="Eliminar">
                        <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar"><br />Eliminar
                    </a>
                </li>
                <li>
                    <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;"
                            onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                        <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                    </button>
                </li>

                <li>
                    <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=search"name="submit_mult" value="Buscar Imágenes">
                        <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}search.png" alt="Buscar Imágenes"><br />Buscar
                    </a>
                </li>

                <li>
                    <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=upload#upload-photos" name="submit_mult" value="Subir Fotos">
                        <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}upload_web.png" alt="Subir Fotos"><br />Subir Fotos
                    </a>
                </li>
                <li>
                    <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=list_all" name="submit_mult" value="Catálogo de Fotos">
                        <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}folder_image.png" alt="Catálogo de Fotos"><br />Catálogo de Fotos
                    </a>
                </li>
                <li>
                    <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=list_today"  name="submit_mult" value="Fotos de Hoy">
                        <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}image_today.png" alt="Fotos de Hoy"><br />Fotos de Hoy
                    </a>
                </li>

            </ul>
        </div>



        <div id="{$category}" class="categ" style="padding: 6px 2px;">

                {include file="mediamanager/_partials/media-browser.tpl"}

        </div>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" id="id" name="id" value="" />

    </form>

</div>
{/block}
