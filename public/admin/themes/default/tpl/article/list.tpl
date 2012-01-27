{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsarticle.js" language="javascript"}
    {script_tag src="/editables.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
{/block}

{block name="footer-js" append}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#contents-provider').tabs();
    });

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

  /*  $('tabs').observe('click', makealldivssortable);

    function makealldivssortable(event) {
        setTimeout('make_sortable_divs_portadas(\'{$category}\');', 800);
    }

*/
    Draggables.observers.each(function(item){
        item.onEnd= avisoGuardarPosiciones;
    });

    // Activate tabs in contents provider
    $fabtabs = new Fabtabs('tabs');

// ]]>
</script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Frontpage Manager{/t} :: {if $category eq 0}{t}HOME{/t}{else}{$datos_cat[0]->title}{/if}</h2></div>
        <ul class="old-button">

            {acl isAllowed="ARTICLE_CREATE"}
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add">
                    <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New article{/t}
                </a>
            </li>
            {/acl}
            <li class="separator"></li>
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

    {render_messages}

    <ul class="pills">
        {acl hasCategoryAccess=0}
        <li>
            <a href="article.php?action=list&category=home" id='link_home' {if $category=='home'}class="active"{/if}>{t}HOME{/t}</a>
        </li>
        {/acl}
        {include file="menu_categories.tpl" home="article.php?action=list"}
    </ul>

    {*PROVISIONAL alert eliminar varias noticias con relacionados*}
    {if isset($smarty.get.alert) && ($smarty.get.alert eq 'ok')}
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

            <table id="columns" class="adminform clearfix">
                <tr  valign="top">
                   <td colspan=3>
                        {include  file="frontpage/placeholders/article_column_highlighted.tpl" place='placeholder_highlighted'}
                   </td>
                </tr>
                <tr valign="top" width="100%">
                    <td >
                        {include  file="frontpage/placeholders/article_column.tpl" place='placeholder_0'}
                    </td>
                    <td >
                        {include  file="frontpage/placeholders/article_column.tpl" place='placeholder_1'}
                    </td>
                    <td>
                        {include  file="frontpage/placeholders/article_column.tpl" place='placeholder_2'}
                    </td>
                </tr>
            </table>

            <div id="contents-provider" class="tabs" style="margin-top:20px;">
                <ul>
                    {if $category neq 'home'}
                    <li><a href="#other-articles">{t}Other articles in this category{/t}</a></li>
                    {else}
                    <li><a href="#suggested-elements">{t}Suggested articles{/t}</a></li>
                    {/if}
                    <li><a href="#available-widgets">{t}Widgets{/t}</a></li>
                    <li><a href="#available-opinions">{t}Opinions{/t}</a></li>
                    <li><a href="#drop">{t}Stage{/t}</a></li>
                </ul>

                {if $category neq 'home'}
                <div id="other-articles">
                    {include file="frontpage/blocks/others_articles_in_category.tpl"}
                </div>
                {else}
                <div id="suggested-elements">
                    {include file="frontpage/blocks/articles_suggested.tpl"}
                </div>
                {/if}

                <div id="available-widgets">
                    {include file="frontpage/blocks/widgets_available.tpl"}
                </div>

                <div id="available-opinions">
                    {include file="frontpage/blocks/opinions_available.tpl"}
                </div>

                <div id="drop">
                    {t}Drop an element here to get it out of this frontpage{/t}
                </div>
            </div>

        </div>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default}" />
    </div>
</form>
{/block}
