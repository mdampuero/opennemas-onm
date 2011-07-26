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
<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}
        <div id="{$category}">
            <table class="adminheading">
                <tr>
                    <th nowrap>{t}Menues{/t}</th>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th style="padding:10px;" align='left'>{t}Title{/t}</th>
                    <th>{t}Edit{/t}</th>
                </tr>

                {foreach from=$pages item=value key=page}
                    <tr {cycle values="class=row0,class=row1"}>

                        <td style="padding:10px;font-size: 11px;width:60%;">
                            <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$page}" title="{$page}">
                                {$page}</a>
                        </td>
                   
                        <td style="padding:10px;width:10%;" align="center">
                            <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$page}" title="{$page}" title={t}"Edit"{/t}>
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        </td>

                </tr>

                {/foreach}
           
            </table>
        </div>
    
    {else if isset($smarty.request.action) && ($smarty.request.action eq "read")}
      
        <div id="menu-acciones-admin" class="clearfix">
            <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Menu Manager{/t}</h2></div>
            <ul>
                <li>
                    <a  onclick="saveMenu();enviar(this, '_self', 'save', 0);" class="admin_add"  name="submit_mult" value="Save" title="Save">
                        <img border="0" id="save-button"  src="{$params.IMAGE_DIR}save.gif" title="Guardar" alt="Guardar" ><br />{t} Save {/t}
                    </a>
                </li>

                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=new" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}/advertisement.png" title="New Menu" alt="New Menu"><br />{t}New Menu{/t}
                    </a>
                </li>
                 
            </ul>
        </div>

            <table class="adminheading">
                <tr>
                    <td>{t}Menu {$name} Frontpage{/t}: </td>
                </tr>
            </table>
            <table class="adminlist">
                <tbody>
                     <tr>
                        <td style="width:80%;">
                            <ul id="menu-categories" style="min-height:50px; background-color: #EEE" >                               
                                {section name=c loop=$menu}
                                    <li id="{$menu[c]['id']}" title="{$menu[c]['title']}" name="{$menu[c]['name']}" class="drag-category">{$menu[c]['title']} </li>
                                {/section}
                            </ul>
                                
                        </td>
                        <td valign="top">

                                <ul id='ul-categories' style="min-height:50px;">
                                    {section name=as loop=$categories}
                                        <li id="{$categories[as]->pk_content_category}" title="{$categories[as]->title}" name="{$categories[as]->name}"  class="drag-category">
                                            {$categories[as]->title}
                                        </li>
                                        {section name=su loop=$subcat[as]}
                                             <li id="{$subcat[as][su]->pk_content_category}" title="{$subcat[as][su]->title}" name="{$subcat[as][su]->name}"  class="drag-category">
                                                 &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</li>                                                 
                                        {/section}
                                    {/section}
                                    {foreach from=$pages item=value key=page}
                                        <li id="{$value}" title="{$page}" name="{$page}"  class="drag-category">
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
 
                                        saveMenu = function() {
                                            var positions = new Array();
                                            var i=0;
                                             $$('ul#menu-categories li').each( function(item) {
                                                 if(item.getAttribute('id')) {

                                                    positions[i] = { 'id':item.getAttribute('id'), 'title': item.getAttribute('title'), 'name': item.getAttribute('name') };
                                                    i++;
                                                 }
                                             });

                                            $('positions').value =  Object.toJSON(positions);
                                            //return false;
                                            
                                        }



                                // ]]>
                                </script>
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
            <input type="text" size="100" name="positions" id="positions" value="" />
            <input type="text" size="100" name="name" id="name" value="{$name}" />
            <input type="hidden" name="id" id="id" value="{$id}" />
       
    {/if}
     </form>
</div><!--fin wrapper-content-->
{/block}
