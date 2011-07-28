{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsMenues.js"></script>
    <script type="text/javascript">
         document.observe('dom:loaded', function() {
            makeSortable();
         });     
    </script>
{/block}

{block name="header-css" append}
<style type="text/css">
table th, table label {
    color: #888;
    text-shadow: white 0 1px 0;
    font-size: 13px;
}
tbody th {
    vertical-align: top;
    text-align: left;
    padding: 10px;
    width: 200px;
    font-size: 13px;
}
label{
    font-weight:normal;
}
.panel {
    background:White;
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

.awesome {
    border:0;
}
.panel {
    margin:0;
    border:none;
}
.default-value {
    display:inline;
    color:#666;
    margin-left:10px;
    vertical-align:middle
}
input[type="text"] {
    width:340px;
    max-height:80%
}
#menuelements, .elementsContainer { width:100%; margin:0; padding:10px 0; min-height:40px; border:1px solid #ccc; width:100%}
#menuelements li, .elementsContainer li { padding:4px; list-style:none; border:1px solid #ddd; cursor:pointer; margin:3px; display:inline-block;}
#menuelements li:hover, .elementsContainer li:hover { background:#eee}
.left { margin-right:10px;  }

.reveal-modal {

    top: 90px;
    left: 50%;
    margin-left: -300px;
    width: 440px;
    background: #FFF;
    background: rgba(255,255,255,.9);
    position: absolute;
    z-index: 101;
    padding: 30px 40px 34px;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
    -moz-box-shadow: 0 0 10px rgba(0,0,0,.4);
    -webkit-box-shadow: 0 0 10px rgba(0,0,0,.4);
    -box-shadow: 0 0 10px rgba(0,0,0,.4);
    }
 
.reveal-modal .close-reveal-modal {
    font-size: 22px;
    line-height: .5;
    position: absolute;
    top: 8px;
    right: 11px;
    color: #aaa;
    text-shadow: 0 -1px 1px rbga(0,0,0,.6);
    font-weight: bold;
    cursor: pointer;
    }
.save-button{
    position: absolute;
    top: 78px;
    right: 20px;
    cursor: pointer;
		}

.div-actions {
    background: #CCCCCC;
    background: rgba(255,255,255,.9);
    -moz-box-shadow: 0 0 10px rgba(0,0,0,.4);
    -webkit-box-shadow: 0 0 10px rgba(0,0,0,.4);
    -box-shadow: 0 0 10px rgba(0,0,0,.4);
    padding: 5px;
    }

</style>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Menu manager{/t} :: {t}Editing menu{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="saveMenu();sendFormValidate(this, '_self', 'validate', '{$menu->pk_menu}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                <li>
                    {if isset($menu->pk_menu)}
                       <a href="#" onClick="javascript:saveMenu();sendFormValidate(this, '_self', 'update', '{$menu->pk_menu}', 'formulario');">
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
        <table class="adminheading">
            <tr>
                <td>{t}Menu {$name} Frontpage{/t}: </td>
            </tr>
        </table>
        <table class="adminlist">
            {assign var=menuParams value=$menu->params|unserialize}
            <tbody>
                 <tr>
                    <td>
                        <table style="margin:10px ">
                            <tr>
                                <th>
                                    <label for="name">{t}Name{/t}</label>
                                </th>
                                <td>
                                    <input type="text" name="name" id="name" value="{$menu->name}" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="description">{t}Description{/t}</label></th>
                                <td>
                                    <textarea name="description" id="description" cols="60">{$menuParams['description']|clearslash}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="description">{t}Father menu{/t}</label></th>
                                <td>
                                    <select id='pk_father' name='pk_father'>
                                        <option id="0" title="Ninguno"> </option>
                                        {section loop=$menues name=m}
                                            <option value="{$menues[m]->pk_menu}" title="{$menues[m]->name}" {if $menu->pk_father eq  $menues[m]->pk_menu}selected{/if}>
                                                {$menues[m]->name}
                                            </option>
                                        {/section}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="description">{t}Elements{/t}</label></th>
                                <td>
                                    <div class="left">
                                        <h3>{t}Menu elements{/t}</h3>
                                        <ul id="menuelements">
                                        {if !empty($menu->items)}
                                            {section name=c loop=$menu->items}
                                                <li id="item_{$menu->items[c]->pk_item}" pk_item="{$menu->items[c]->pk_item}" title="{$menu->items[c]->title}"
                                                    link="{$menu->items[c]->link}" type="{$menu->items[c]->type}"
                                                    onClick="showActions('item_{$menu->items[c]->pk_item}');" >
                                                    {$menu->items[c]->title}
                                                </li>
                                            {/section}
                                        {/if}
                                        </ul>
                                    </div>
                                    <br/>
                                    <div class="left">
                                          <ul id="tabs">
                                            <li>
                                                <a href="#listado">{t}Global Categories{/t}</a>
                                            </li>
                                            <li>
                                                <a href="#listadoAlbum">{t}Album Categories{/t}</a>
                                            </li>
                                            <li>
                                                <a href="#listadoVideo">{t}Video Categories{/t}</a>
                                            </li>
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
                                                        type="category" link="{$categories[as]->name}"  class="drag-category" pk_menu="">
                                                        {$categories[as]->title}
                                                    </li>
                                                {/section}
                                            </ul>
                                         </div>
                                        <div class="panel" id="listadoAlbum">
                                            <ul id='albumCategories' class="elementsContainer">
                                                {section name=as loop=$albumCategories}
                                                        <li id="album_{$albumCategories[as]->pk_content_category}" title="{$albumCategories[as]->title}"
                                                            type="category" link="{$albumCategories[as]->name}"  class="drag-category" pk_menu="">
                                                            {$albumCategories[as]->title}
                                                        </li>                                             
                                                {/section}
                                            </ul>
                                         </div>
                                        <div class="panel" id="listadoVideo">
                                            <ul id='videoCategories' class="elementsContainer">
                                                {section name=as loop=$videoCategories}
                                                        <li id="video_{$videoCategories[as]->pk_content_category}" title="{$videoCategories[as]->title}"
                                                            type="category" link="{$videoCategories[as]->name}"  class="drag-category" pk_menu="">
                                                            {$videoCategories[as]->title}
                                                        </li>
                                                {/section}
                                            </ul>
                                         </div>
                                         <div class="panel" id="frontpages">
                                            <ul id='availablePages' class="elementsContainer">
                                                {foreach from=$pages item=value key=page}
                                                    <li id="page_{$value}" title="{$page}" link="{$page}"
                                                        type="internal"  class="drag-category" pk_menu="">
                                                       {t}{$page}{/t}
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                         <div class="panel" id="staticPages"> 
                                              <ul id='availableStatics' class="elementsContainer">
                                                 {section name=k loop=$staticPages}
                                                     <li id="static_{$staticPages[k]->id}" title="{$staticPages[k]->title}" pk_menu=""
                                                         type="static" link="{$staticPages[k]->slug}"  class="drag-category">
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
                                                             type="category" link="{$subcat[as][su]->link}"  class="drag-category" pk_menu="">
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
            <br>
            <label>{t}Link:{/t}</label> <input type="text" name="link" value="" id="link" size="60"> <br>
            <button id="saveButton" onclick="saveLink();return false;" class="save-button onm-button green" type="button">{t}Save{/t}</button>
            <a title="Close" onclick="$('linkInsertions').hide();return false;" class="close-reveal-modal">&#215;</a></div>
        </div>
        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" size="100" name="items" id="items" value="" />
        <input type="hidden" name="id" id="id" value="{$menu->pk_menu}" />
    </div><!--fin wrapper-content-->
</form>
{/block}
