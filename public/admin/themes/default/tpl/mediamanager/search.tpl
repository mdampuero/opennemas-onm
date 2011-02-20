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

    <script type="text/javascript" language="javascript">
        if($('starttime')) {
            new Control.DatePicker($('starttime'), {
                icon: './themes/default/images/template_manager/update16x16.png',
                locale: 'es_ES',
                timePicker: true,
                timePickerAdjacent: true,
                dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
            });
        
        }
        else {
            alert('Not existent');
        }

        if($('endtime')) {
            new Control.DatePicker($('endtime'), {
                icon: './themes/default/images/template_manager/update16x16.png',
                locale: 'es_ES',
                timePicker: true,
                timePickerAdjacent: true,
                dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
            });

        }
    </script>

{/block}

{block name="content"}

<div style="width:70%;margin:0 auto;">
    <div id="contenedor-gral">

        <ul class="tabs2">
            <li>
                <a href="mediamanager.php?listmode={$listmode}&category=GLOBAL" {if $category==0}style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>
                    {t}GLOBAL{/t}</a>
            </li>
            {if $smarty.server.PHP_SELF eq '/admin/controllers/mediamanager/mediamanager.php'}
                <li>
                    <a href="mediamanager.php?listmode={$listmode}&category=2" {if $category==2} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>
                        {t}PUBLICIDAD{/t}</a>
                </li>
            {/if}
            {include file="menu_categorys.tpl" home="mediamanager.php?listmode="}
        </ul>

        <div id="menu-acciones-admin">
            <div style="float: left; margin-left: 10px; margin-top: 10px;"><h2>{t}Images Manager{/t}:: {if $action eq 'search'} {t}Search{/t} {elseif $action eq 'searchResult'} {t}Search result{/t} {else} {t}Information{/t} {/if} </h2></div>
            <ul>
                <li>
                    <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=search" onmouseover="return escape('<u>B</u>uscar Imagenes');" name="submit_mult" value="Buscar Imágenes">
                        <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}search.png" alt="{t}Search images{/t}"><br />{t}Buscar{/t}
                    </a>
                </li>
                 <li>
                    <a class="admin_add" href="mediamanager.php?category={$category}" onmouseover="return escape('Listado de Categorias');" name="submit_mult" value="Listado de Categorias">
                        <img border="0" style="width:50px;"  src="{$params.IMAGE_DIR}icons.png" alt="Información"><br />{t}Information{/t}
                    </a>
                </li>
                {if $action eq 'searchResult'}
                <li>
                    <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                        <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar"><br />{t}Delete{/t}
                    </a>
                </li>
                <li>
                    <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                        <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                    </button>
                </li>
                {/if}
           </ul>
        </div>

        {include file="mediamanager/_partials/image_search.tpl"}

    </div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
</form>

</div>
{/block}
