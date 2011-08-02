{extends file="base/admin.tpl"}
{block name="header-css" append}
<link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}css/utilities.css" />
{/block}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsarticle.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}editables.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsGallery.js"></script>

    {if $smarty.request.action == 'list_pendientes' || $smarty.request.action == 'list_agency'}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}editables.js"></script>
    {/if}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Frontpage Manager{/t} :: {if $category eq 0}{t}HOME{/t}{else}{$datos_cat[0]->title}{/if}</h2></div>
        <ul class="old-button">
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="{t}Delete{/t}" title="{t}Delete{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="noFrontpage" ><br />{t}Unpublish{/t}
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:confirmar_hemeroteca(this,{$category}, 0);" name="submit_mult" value="Archivar" title="Archivar">
                    <img border="0" src="{$params.IMAGE_DIR}archive.gif" title="{t}Arquive{/t}" alt="{t}Arquive{/t}" ><br />{t}Arquive{/t}
                </a>
            </li>
            {if $category!='home'}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 2);" name="submit_mult" value="Frontpage" title="Sugerir Home">
                    <img border="0" src="{$params.IMAGE_DIR}gosuggest50.png" title="{t}Suggest to home{/t}" alt="{t}Suggest to home{/t}" ><br />{t}Suggest to home{/t}
                </a>
            </li>
            {/if}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 0);" name="submit_mult" value="Frontpage" title="Frontpage">
                    <img border="0" src="{$params.IMAGE_DIR}home_no50.png" title="{t}No home{/t}" alt="Frontpage" ><br />{t}No home{/t}
                </a>
            </li>
            <li>
                <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                    <img id="select_button" class="icon" status="0" src="{$params.IMAGE_DIR}select_button.png" title="{t}Select all{/t}" alt="{t}Select all{/t}"  status="0">
                </button>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:savePositions('{$category}');" title="Guardar Positions" alt="Guardar Cambios">
                    <img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save changes{/t}" alt="{t}Save changes{/t}" ><br />{t}Save changes{/t}
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:previewFrontpage('{$category}');return false;" title="Previsualizar posiciones en portada">
                    <img border="0" src="{$params.IMAGE_DIR}preview.png" title="{t}Preview{/t}" alt="{t}Preview{/t}" ><br />{t}Preview{/t}
                </a>
            </li>
            <li>
                 <a href="#" onclick="clearcache('{$category}'); return false;" id="button_clearcache">
                     <img border="0" src="{$params.IMAGE_DIR}clearcache.png" title="{t}Clean cache{/t}" alt="" /><br />{t}Clean cache{/t}
                 </a>
            </li>
        </ul>
    </div>
</div>


<div class="wrapper-content">

    <ul class="tabs2" style="margin-bottom: 28px;">
        {acl hasCategoryAccess=0}
        <li>
            <a href="article.php?action=list&category=home" id='link_home' {if $category=='home'} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>{t}HOME{/t}</a>
        </li>
        <script type="text/javascript">
        // <![CDATA[
            Event.observe($('link_home'), 'mouseover', function(event) {
                $('menu_subcats').setOpacity(0);
                e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
            });
        // ]]>
        </script>
        {/acl}
        {include file="menu_categorys.tpl" home="article.php?action=list"}
    </ul>

    {*PROVISIONAL alert eliminar varias noticias con relacionados*}
    {if $smarty.get.alert eq 'ok'}
    <script type="text/javascript" language="javascript">
        alert('{$smarty.get.msg}');
    </script>
    {/if}
    {* MENSAJES DE AVISO GUARDAR POS******* *}
    <div id="warnings-validation"></div>

    <input type="hidden"  id="category" name="category" value="{$category}">
    <div id="{$category}">
        <table class="adminheading">
            <tr>
                <th align="center">{t}Frontpage articles{/t} <img  border="0" style="margin-left:10px; cursor:pointer;" src="{$params.IMAGE_DIR}iconos/info.png" onmouseover="Tip('<img src={$params.IMAGE_DIR}leyenda_programadas.png >', SHADOW, true, ABOVE, true, WIDTH, 300)" onmouseout="UnTip()" ></th>
            </tr>
        </table>
        <div id="pagina">

            <table id="columns" class="adminform">
                <tr  valign="top">
                   <td colspan=3>
                        {include  file="frontpage/placeholders/article_column_highlighted.tpl" place='placeholder_highlighted'}
                   </td>
                </tr>
                <tr valign="top">
                    <td width="33%">
                          {include  file="frontpage/placeholders/article_column.tpl" place='placeholder_0'}
                    </td>
                     <td width="33%">
                          {include  file="frontpage/placeholders/article_column.tpl" place='placeholder_1'}
                    </td>
                     <td width="33%">
                          {include  file="frontpage/placeholders/article_column.tpl" place='placeholder_2'}
                    </td>
                </tr>
            </table>

            <div id="contents-provider">
                <ul id="tabs">
                    {if $category neq 'home'}
                    <li><a href="#other-articles">{t}Other articles in this category{/t}</a></li>
                    {else}
                    <li><a href="#suggested-elements">{t}Suggested articles{/t}</a></li>
                    {/if}
                    <li><a href="#available-widgets">{t}Widgets{/t}</a></li>
                    <li><a href="#available-opinions">{t}Opinions{/t}</a></li>
                </ul>

                {if $category neq 'home'}
                <div id="other-articles" class="panel no-border tabs-panel" style="width:100%">
                    {include file="frontpage/blocks/others_articles_in_category.tpl"}
                </div>
                {else}
                <div id="suggested-elements" class="panel no-border tabs-panel" style="width:100%">
                    {include file="frontpage/blocks/articles_suggested.tpl"}
                </div>
                {/if}

                <div id="available-widgets" class="panel no-border tabs-panel" style="width:100%">
                    {include file="frontpage/blocks/widgets_available.tpl"}
                </div>

                <div id="available-opinions" class="panel no-border tabs-panel" style="width:100%">
                    {include file="frontpage/blocks/opinions_available.tpl"}
                </div>
            </div>

            <div id="no_en_home" style="margin:10px 0">
                <table class="adminheading">
                    <tr>
                        <th align="center">{t}Homepage articles{/t} <img  border="0" style="margin-left:10px; cursor:pointer;" src="{$params.IMAGE_DIR}iconos/info.png" onmouseover="Tip('<img src=http://demo.opennemasweb.es/admin/themes/default/images/leyenda_programadas.png >', SHADOW, true, ABOVE, true, WIDTH, 300)" onmouseout="UnTip()" ></th>
                    </tr>
                </table>
                <table class="adminlist" border=0 style="border:1px solid #ccc !important;">
                    <tr>
                        <td width="100%">
                            <div id="div_no_home" style="width:100%;min-height:80px;padding:5px;overflow:auto;">
                              {if $category eq 'home'} {t}NOT IN HOME{/t} {else} {t 1=$datos_cat[0]->title}NOT IN FRONTPAGE OF %1{/t} {/if}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>




        </div> {* div id=pagina *}
        <script type="text/javascript">


            // <![CDATA[
            make_sortable_divs_portadas('{$category}');
            // Controlar o cambio de posicións para amosar un aviso
            var posicionesIniciales = null;
            var posicionesInicialesWarning = false; // Mellorar o rendemento
            avisoGuardarPosiciones = function() {
                //Provisional repite innecesariamente.
                {if $category eq 'home'}
                changedTables({$category});
                {/if}
                if(!posicionesInicialesWarning) {
                    $$('input[type=checkbox]').each( function(item, idx) {
                        if(item.value != posicionesIniciales[idx].value) {
                            $('warnings-validation').update('<div class="notice">{t}Please, remember save positions after finish.{/t}</div>');
                            posicionesInicialesWarning = true;
                            $break;
                        }
                    });
                }
            };

            document.observe('dom:loaded', function() {
                posicionesIniciales = $$('input[type=checkbox]');
            });
            make_sortable_divs_portadas('{$category}');

            $('tabs').observe('click', makealldivssortable);

            function makealldivssortable(event) {
                setTimeout('make_sortable_divs_portadas(\'{$category}\');', 800);
            }


            Draggables.observers.each(function(item){
                item.onEnd= avisoGuardarPosiciones;
            });

            // Activate tabs in contents provider
            $fabtabs = new Fabtabs('tabs');

        // ]]>
        </script>
    </div> {* div id=$category *}
    <td valign="top" align="right" style="padding:4px;" width="30%">

            <script type="text/javascript" language="javascript">
            document.observe('dom:loaded', function() {
                if($('title')){
                    new OpenNeMas.Maxlength($('title'), {});
                    $('title').focus(); // Set focus first element
                }
                getGalleryImages('listByCategory','{$category}','','1');
                getGalleryVideos('listByCategory','{$category}','','1');
            });

            if($('starttime')) {
                new Control.DatePicker($('starttime'), {
                    icon: './themes/default/images/template_manager/update16x16.png',
                    locale: 'es_ES',
                    timePicker: true,
                    timePickerAdjacent: true,
                    dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
                });

                new Control.DatePicker($('endtime'), {
                    icon: './themes/default/images/template_manager/update16x16.png',
                    locale: 'es_ES',
                    timePicker: true,
                    timePickerAdjacent: true,
                    dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
                });
            }
            </script>


            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" name="id" id="id" value="{$id}" />
        </div>
    </form>
</div>
{/block}
