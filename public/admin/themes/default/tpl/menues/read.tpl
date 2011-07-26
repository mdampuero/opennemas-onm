{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsMenues.js"></script>
    <script type="text/javascript">
    // <![CDATA[
    Sortable.create( 'availablecategories' , {
        tag:'li',
        dropOnEmpty: true,
        hoverclass: 'active',
        constraint: 'horizontal',
        containment:[ 'availablecategories', 'menuelements' ]
    });
    Sortable.create( 'menuelements' , {
        tag:'li',
        dropOnEmpty: true,
        hoverclass: 'active',
        constraint: 'horizontal',
        containment:[ 'menuelements', 'availablecategories' ]
    });
    //]]>
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
}
.default-value {
    display:inline;
    color:#666;
    margin-left:10px;
    vertical-align:middle
}
input[type="text"] {
    width:300px;
    max-height:80%
}
#availablecategories, #menuelements { width:100%; margin:0; padding:0; min-height:20px; border:1px solid #ccc; width:100%}
#availablecategories  li, #menuelements li { padding:4px; list-style:none; border:1px solid #ddd; cursor:pointer; margin:3px; display:inline-block;}
#availablecategories  li:hover, #menuelements li:hover { background:#eee}
.left { margin-right:10px;  }
</style>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Menu manager{/t} :: {t}Editing menu{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a  onclick="saveMenu();enviar(this, '_self', 'update', '{$menu->pk_menu}');" class="admin_add"  name="submit_mult" value="Save" title="Save">
                        <img border="0" id="save-button"  src="{$params.IMAGE_DIR}save.png" title="Guardar" alt="Guardar" ><br />{t} Save {/t}
                    </a>
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
                        <img src="{$params.IMAGE_DIR}newsletter/previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
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
            {assign var=params value=$menu->params|unserialize}
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
                                    <textarea name="description" id="description" cols="60">{$params['description']|clearslash}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="description">{t}Elements{/t}</label></th>
                                <td>
                                    <div class="left">
                                        <h3>{t}Available categories{/t}</h3>
                                        <ul id='availablecategories'>
                                            {section name=as loop=$categories}
                                            <li id="{$categories[as]->pk_content_category}" title="{$categories[as]->title}" type="category" link="{$categories[as]->link}"  class="drag-category">
                                                {$categories[as]->title}
                                            </li>
                                            {*section name=su loop=$subcat[as]}
                                                 <li id="{$subcat[as][su]->pk_content_category}" title="{$subcat[as][su]->title}" type="category" link="{$subcat[as][su]->link}"  class="drag-category">
                                                     &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</li>
                                            {/section*}
                                            {/section}

                                            {foreach from=$pages item=value key=page}
                                            <li id="{$value}" title="{$page}" link="{$page}" type="internal"  class="drag-category">
                                               {t}{$page}{/t}
                                            </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                    <div class="left">
                                        <h3>{t}Menu elements{/t}</h3>
                                        <ul id="menuelements">
                                        {if !empty($menu->items)}
                                            {section name=c loop=$menu->items}
                                                <li id="{$menu->items[c]->pk_menu}" title="{$menu->items[c]->title}"
                                                    link="{$menu->items[c]->link}" type="{$menu->items[c]->type}">
                                                    {$menu->items[c]->title}
                                                </li>
                                            {/section}
                                        {/if}
                                        </ul>
                                    </div>

                                </td>
                            </tr>
                        </table>
                    </td>

                    <td valign="top">

                    </td>
                    <td style="vertical-align:top;">

                    </td>

                </tr>
                 <tr>
                    <div class="divInsert"></div>
                 </tr>
            </tbody>
            <tfooter>
                <tr>
                    <td colspan=2>&nbsp;</td>
                </tr>
            </tfooter>
        </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" size="100" name="items" id="items" value="" />
        <input type="hidden" name="id" id="id" value="{$menu->pk_menu}" />
    </div><!--fin wrapper-content-->
</form>
{/block}
