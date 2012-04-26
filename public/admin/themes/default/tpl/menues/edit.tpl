{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/utilsMenues.js" language="javascript"}
    {script_tag src="/onm/jquery.menues.js"}
    <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery( "#menu-form" ).tabs();
    });
    </script>
{/block}

{block name="header-js" append}{/block}

{block name="header-css" append}
{css_tag href="/managerMenu.css" media="screen,projection"}
<style type="text/css">
label {
    display:block;
    color:#666;
    text-transform:uppercase;
}
.utilities-conf label {
    text-transform:none;
}

fieldset {
    border:none;
    border-top:1px solid #ccc;
}
legend {
    color:#666;
    text-transform:uppercase;
    font-size:13px;
    padding:0 10px;
}
</style>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Menu manager{/t} :: {t 1=$menu->name}Editing menu "%1"{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="saveMenu();sendFormValidate(this, '_self', 'validate', '{$menu->pk_menu|default:""}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                <li>
                    {if isset($menu->pk_menu)}
                       <a href="#" onClick="javascript:saveMenu();sendFormValidate(this, '_self', 'update', '{$menu->pk_menu|default:""}', 'formulario');">
                    {else}
                       <a href="#" onClick="javascript:saveMenu();sendFormValidate(this, '_self', 'create', '0', 'formulario');">
                    {/if}
                    <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />{t}Save and Exit{/t}</a>
                </li>
                <li>
                    <a onClick="addLink();" style="cursor:pointer;">
                        <img src="{$params.IMAGE_DIR}list-add.png" border="0" />
                        <br>{t}Add External Link{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a  href="{$smarty.server.PHP_SELF}?action=list"  title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>

    </div>
    <div class="wrapper-content">
        <table class="adminform">
            {if isset($menu)}
                {assign var=menuParams value=$menu->params|unserialize}
            {/if}
            <tbody>
                 <tr>
                    <td>
                        <table  style="margin:10px ">
                            <tr>
                                <td style="width:70%; padding:4px 0;">
                                    <label for="name">{t}Name{/t}</label>
                                    <input type="text" name="name" class="required"
                                           id="name" value="{$menu->name|default:""}"  style="width:97%"
                                           {if (!empty($menu) && $menu->type neq 'user')} readonly="readonly" {/if}/>
                                </td>
                                <td rowspan="3" valign="top" style="padding:10px">
                                    <div class="help-block">
                                        <div class="title"><h4>{t}Help{/t}</h4></div>
                                        <div class="content">
                                            <ul>
                                                <li>{t} Sort items from bottom lists into menu element list.  {/t}</li>
                                                <li>{t} Drag menu items to order the menu.  {/t}</li>
                                                <li>{t} Use add button for create a new link in the menu{/t}</li>
                                                <li>{t} Use dobleClick if you want delete or edit one element{/t}</li>
                                                <li>{t} Only allow two levels in menus{/t}</li>
                                            </ul>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            <tr>
                                <td style="padding:4px 0;">
                                    <label for="description">{t}Description{/t}</label>
                                    <textarea name="description" id="description"   style="width:97%">{$menuParams['description']|clearslash|default:""}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" style="padding:4px 0;">
                                    <label for="description">{t}Father menu{/t}</label>
                                    <select id='pk_father' name='pk_father' {if (!empty($menu) && $menu->type neq 'user')} disabled="disabled" {/if}>
                                        <option value="0" title="Ninguno">{t}- Root menu -{/t}</option>
                                        {section loop=$menues name=m}
                                            {assign var=items value=$menues[m]->items}
                                            <option value="{$items[0]->pk_item}" name="{$items[0]->title} in {$menues[m]->name|default:""}" style="font-weight:bold;" >
                                                   Menu {$menues[m]->name}
                                            </option>
                                            {if isset($items) && !empty($items)}
                                            {section name=su loop=$items}
                                            <option value="{$items[su]->pk_item}" name="{$items[su]->name|default:""}"
                                                {if isset($menu) && $menu->pk_father eq $items[su]->pk_item} selected {/if}>
                                                &nbsp;&nbsp;&nbsp;&nbsp;{$items[su]->title}
                                            </option>
                                            {/section}
                                            {/if}
                                        {/section}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3>
                                    <fieldset>
                                        <legend>{t}Menu components{/t}</legend>
                                        <div class="left">
                                        <h3>{t}Menu elements{/t}</h3>
                                        <ul id="menuelements" class="menuelements">
                                        {if isset($menu) && !empty($menu->items)}
                                            {section name=c loop=$menu->items}
                                                <li class="menuItem" id="item_{$menu->items[c]->pk_item}" pk_item="{$menu->items[c]->pk_item}" title="{$menu->items[c]->title}"
                                                    link="{$menu->items[c]->link}" type="{$menu->items[c]->type}" >
                                                    {$menu->items[c]->title}
                                                </li>
                                            {/section}
                                        {/if}
                                        </ul>
                                    </div>
                                    <br/>
                                    <div id="menu-form" class="left tabs">
                                        <ul>
                                            <li>
                                                <a href="#listado">{t}Global Categories{/t}</a>
                                            </li>
                                             <li>
                                                <a href="#subcategories">{t}Subcategories{/t}</a>
                                            </li>
                                            {is_module_activated name="ALBUM_MANAGER"}
                                            <li>
                                                <a href="#listadoAlbum">{t}Album Categories{/t}</a>
                                            </li>
                                            {/is_module_activated}
                                            {is_module_activated name="VIDEO_MANAGER"}
                                            <li>
                                                <a href="#listadoVideo">{t}Video Categories{/t}</a>
                                            </li>
                                            {/is_module_activated}
                                            {is_module_activated name="POLL_MANAGER"}
                                            <li>
                                                <a href="#listadoPoll">{t}Poll Categories{/t}</a>
                                            </li>
                                            {/is_module_activated}
                                            <li>
                                                <a href="#frontpages">{t}Available Modules{/t}</a>
                                            </li>
                                            <li>
                                                <a href="#staticPages">{t}Static Pages{/t}</a>
                                            </li>

                                        </ul>

                                        <div id="listado">
                                            <ul id='availableCategories' class="elementsContainer">
                                                {section name=as loop=$categories}
                                                    <li id="cat_{$categories[as]->pk_content_category}" title="{$categories[as]->title}"
                                                        type="category" link="{$categories[as]->name}"
                                                        pk_item="{$categories[as]->pk_content_category}"
                                                        class="drag-category" pk_menu="">
                                                        {$categories[as]->title}
                                                    </li>
                                                {/section}
                                            </ul>
                                         </div>
                                        <div id="listadoAlbum">
                                            <ul id='albumCategories' class="elementsContainer">
                                                {section name=as loop=$albumCategories}
                                                <li id="album_{$albumCategories[as]->pk_content_category}" title="{$albumCategories[as]->title}"
                                                    type="albumCategory" link="{$albumCategories[as]->name}"
                                                    pk_item="{$albumCategories[as]->pk_content_category}"
                                                    class="drag-category" pk_menu="">
                                                    {$albumCategories[as]->title}
                                                </li>
                                                {/section}
                                            </ul>
                                         </div>
                                        <div id="listadoVideo">
                                            <ul id='videoCategories' class="elementsContainer">
                                                {section name=as loop=$videoCategories}
                                                        <li id="video_{$videoCategories[as]->pk_content_category}" title="{$videoCategories[as]->title}"
                                                            type="videoCategory" link="{$videoCategories[as]->name}"
                                                             pk_item="{$videoCategories[as]->pk_content_category}"
                                                             class="drag-category" pk_menu="">
                                                            {$videoCategories[as]->title}
                                                        </li>
                                                {/section}
                                            </ul>
                                         </div>
                                         <div id="listadoPoll">
                                            <ul id='pollCategories' class="elementsContainer">
                                                {section name=as loop=$pollCategories}
                                                        <li id="video_{$pollCategories[as]->pk_content_category}" title="{$pollCategories[as]->title}"
                                                         type="pollCategory" link="{$pollCategories[as]->name}"
                                                             pk_item="{$pollCategories[as]->pk_content_category}"
                                                            class="drag-category" pk_menu="">
                                                            {$pollCategories[as]->title}
                                                        </li>
                                                {/section}
                                            </ul>
                                         </div>
                                         <div id="frontpages">
                                            <ul id='availablePages' class="elementsContainer">
                                                {foreach from=$pages item=value key=page}
                                                    <li id="page_{$page}" pk_item="{$value}"  title="{$page}"
                                                        link={if $page eq 'frontpage'}"home"
                                                                {elseif $page eq 'poll'}"encuesta"
                                                                {elseif $page eq 'letter'}"cartas-al-director"
                                                                {elseif $page eq 'kiosko'}"portadas-papel"
                                                                {elseif $page eq 'letter'}"cartas-al-director"
                                                                {elseif $page eq 'boletin'}"newsletter"
                                                                {else}{$page}{/if}
                                                        type="internal"  class="drag-category" pk_menu="">
                                                        {if $page eq 'frontpage'}home
                                                                {elseif $page eq 'poll'}Encuesta
                                                                {elseif $page eq 'letter'}Cartas Al Director
                                                                 {elseif $page eq 'kiosko'}Portadas Papel
                                                                 {elseif $page eq 'boletin'}Bolet&iacute;n
                                                                {else}{$page}{/if}
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                         <div id="staticPages">
                                              <ul id='availableStatics' class="elementsContainer">
                                                 {section name=k loop=$staticPages}
                                                     <li id="static_{$staticPages[k]->id}" title="{$staticPages[k]->title}" pk_menu=""
                                                         type="static" link="{$staticPages[k]->slug}"
                                                          pk_item="{$staticPages[k]->id}"
                                                          class="drag-category">
                                                            {$staticPages[k]->title}
                                                     </li>
                                                 {/section}
                                             </ul>
                                         </div>
                                        <div id="subcategories" style="border:1px solid #CCCCCC;padding: 4px;">
                                            {section name=as loop=$categories}
                                                {if !empty($subcat[as])}
                                                    <b>{$categories[as]->title}</b>
                                                    <ul  class="elementsContainer" id="subCategories{$categories[as]->pk_content_category}">
                                                    {section name=su loop=$subcat[as]}
                                                         <li id="subcat_{$subcat[as][su]->pk_content_category}" title="{$subcat[as][su]->title}"
                                                             type="category" link="{$subcat[as][su]->name}"
                                                             pk_item="{$subcat[as][su]->pk_content_category}" class="drag-category" pk_menu="">
                                                            {$subcat[as][su]->title}</li>
                                                    {/section}
                                                    </ul>
                                                {/if}
                                            {/section}
                                        </div>

                                    </div>
                                    </fieldset>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="linkInsertions" class="reveal-modal" style="display:none;">
            <label>{t}Title:{/t}</label> <input type="text" name="itemTitle" value="" id="itemTitle" size="60">
            <br><br>
            <label>{t}Link:{/t}</label> <input type="text" name="link" value="" id="link" size="60"> <br>
             <input type="hidden" name="IdItem" id="IdItem" value="" />
            <button id="saveButton"  class="save-button onm-button green" type="button">{t}Save{/t}</button>
            <a title="Close" onclick="hideDiv();" class="close-reveal-modal">&#215;</a></div>
        </div>
        <input type="hidden" id="action" name="action" value="" />

        <input type="hidden" name="id" id="id" value="{$menu->pk_menu|default:""}" />
        <input type="hidden" size="100" name="items" id="items" value="" />
        <input type="hidden" id="forDelete" name="forDelete" value="" />
    </div><!--fin wrapper-content-->

</form>
{/block}
