{extends file="base/admin.tpl"}

{block name="header-js" append}
    <style type="text/css">
        .panel{ border:0 !important; }

        .drag-category{
            cursor:pointer;
            padding:10 px;
            list-style-type: none;
            border: 1px solid #CCCCCC;
            width:200px;
        }
    </style>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsMenues.js"></script>

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
                    <a  href="{$smarty.server.PHP_SELF}?action=list"  title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}newsletter/previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
                {*
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=new" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}/advertisement.png" title="New Menu" alt="New Menu"><br />{t}New Menu{/t}
                    </a>
                </li>
                *}
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
            <tbody>
                 <tr>
                    <td style="width:80%;">
                        <label>{t}Name{/t}</label>
                        <input type="text" size="100" name="name" id="name" value="{$menu->name}" />
                        <br> <br>
                        <label>{t}Description{/t}</label>
                        {assign var=params value=$menu->params|unserialize} 
                        <textarea name="description" id="description"  title={t}"description"{/t} style="width:100%; height:8em;">{t 1=$params['description']|clearslash|escape:"html"}%1{/t}</textarea>
                        <br>
                        <br>
                        <div id="divInsert">
                        </div>
                        <br>
                        *{t} Drop items for menu in the next container {/t}
                        <ul id="menu-categories" style="min-height:50px; background-color: #EEE" >                         
                            {if !empty($menu->items)}
                                {section name=c loop=$menu->items}
                                    <li id="{$menu->items[c]->pk_menu}" title="{$menu->items[c]->title}"
                                        link="{$menu->items[c]->link}" type="{$menu->items[c]->type}"
                                        class="drag-category"> {$menu->items[c]->title} </li>
                                {/section}
                            {/if}
                        </ul>

                    </td>
                    <td valign="top">
                            Categories link: <br>
                            <ul id='ul-categories' style="min-height:50px;">
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
                             <script type="text/javascript">
                            // <![CDATA[
                                   Sortable.create('menu-categories' , {
                                       tag:'li',
                                       dropOnEmpty: true,
                                       containment:[ 'ul-categories', 'menu-categories' ]

                                   });

                                    Sortable.create('ul-categories' , {
                                       tag:'li',
                                       dropOnEmpty: true,
                                       containment:[ 'ul-categories', 'menu-categories' ]

                                   });
 
                            // ]]>
                            </script>
                    </td>
                    <td style="vertical-align:top;">
                      <a onClick="addLink();" style="cursor:pointer;">
                          <img src="{$params.IMAGE_DIR}add.png" border="0" />
                          <br>{t}Add External Link{/t} </a> &nbsp;
                    </td>

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
