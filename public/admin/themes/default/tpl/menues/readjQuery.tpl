{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/utilsMenues.js" language="javascript"}
    {script_tag src="/jquery/jquery.min.js"}
    {script_tag src="/jquery/jquery-ui.js"}
    {*{script_tag src="/jquery/jquery.json-2.2.min.js"}*}
    {script_tag src="/jquery/jquery-menues.js"}
    <script type="text/javascript">
        $.noConflict();
        jQuery(document).ready(function() {
            jQuery( "#tabs-div" ).tabs();
            makeSortable();
          
         });


         
    </script>

{/block}

{block name="header-css" append}
    {css_tag href="/managerMenu.css" media="screen,projection"}
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
                                <th>
                                    <label for="name">{t}Name{/t}</label>
                                </th>
                                <td>
                                    <input type="text" name="name" id="name" value="{$menu->name|default:""}" />
                                </td>
                                <td rowspan="3" valign="top">
                                    <div class="help-block">
                                            <div class="title"><h4>Help</h4></div>
                                            <div class="content">
                                                <ul>
                                                    <li>{t} Sort items from bottom lists into menu element list.  {/t}</li>
                                                    <li>{t} Drag menu items to order the menu.  {/t}</li>
                                                    <li>{t} Use add button for create a new link in the menu{/t}</li>
                                                    <li>{t} Use dobleClick if you want delete or edit one element{/t}</li>
                                                </ul>
                                            </div>
                                    </div>

                                </td>
                            </tr>
                            <tr>
                                <th><label for="description">{t}Description{/t}</label></th>
                                <td>
                                    <textarea name="description" id="description" cols="60">{$menuParams['description']|clearslash|default:""}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="description">{t}Father menu{/t}</label></th>
                                <td colspan="2">
                                    <select id='pk_father' name='pk_father'>
                                        <option value="0" title="Ninguno">{t}- Root menu -{/t}</option>
                                        {section loop=$menues name=m}
                                            <option value="{$menues[m]->pk_menu}" name="{$menues[m]->name|default:""}"
                                                {if isset($menu) && $menu->pk_father eq  $menues[m]->pk_menu} selected {/if}>
                                                    {$menues[m]->name}
                                            </option>
                                            {assign var=items value=$menues[m]->items}
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
                                <th><label for="description">{t}Elements{/t}</label></th>
                                <td  colspan="2">
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
                                    <div class="left" id="tabs-div">
                                          <ul id="tabs">
                                            <li>
                                                <a href="#listado">{t}Global Categories{/t}</a>
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
                                                <a href="#frontpages">{t}Available Frontpages{/t}</a>
                                            </li>
                                            <li>
                                                <a href="#staticPages">{t}Static Pages{/t}</a>
                                            </li>
                                            <li>
                                                <a href="#subcategories">{t}Subcategories{/t}</a>
                                            </li>
                                        </ul>

                                        <div class="panel" id="listado">
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
                                        <div class="panel" id="listadoAlbum">
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
                                        <div class="panel" id="listadoVideo">
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
                                         <div class="panel" id="listadoPoll">
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
                                         <div class="panel" id="frontpages">
                                            <ul id='availablePages' class="elementsContainer">
                                                {foreach from=$pages item=value key=page}
                                                    <li id="page_{$value}"   pk_item="{$value}" 
                                                        title="{if $page eq 'frontpage'}home{elseif $page eq 'poll'}encuesta{else}{$page}{/if}"
                                                        link="{if $page eq 'frontpage'}home{elseif $page eq 'poll'}encuesta{else}{$page}{/if}"
                                                        type="internal"  class="drag-category" pk_menu="">
                                                       {if $page eq 'frontpage'}home{elseif $page eq 'poll'}encuesta{else}{$page}{/if}
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                         <div class="panel" id="staticPages">
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
                                        <div class="panel" id="subcategories" style="border:1px solid #CCCCCC;padding: 4px;">
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
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                 <tr>
                     <td style="vertical-align:top;">

                     </td>
                 </tr>
            </tbody>
            <tfooter>
                <tr>
                    <td colspan=2>&nbsp;</td>
                </tr>
            </tfooter>
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
        <input type="hidden" size="100" name="items" id="items" value="" />
        <input type="hidden" name="id" id="id" value="{$menu->pk_menu|default:""}" />
        <input type="hidden" id="forDelete" name="forDelete" value="" />
    </div><!--fin wrapper-content-->

</form>
{/block}
