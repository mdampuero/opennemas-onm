{extends file="base/admin.tpl"}

{block name="header-js" append}
    <style type="text/css">
        .panel{ border:0 !important; }
    </style>
{/block}
{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilscategory.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}MiniColorPicker.js"></script>
    <script type="text/javascript">
    // <![CDATA[
        Sortable.create('subcates',{
            tag:'table',
            dropOnEmpty: true,
            containment:["subcates"],
            constraint:false});
    // ]]>
    </script>
{/block}

{block name="content"}
<div class="wrapper-content">

    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

        {include file="botonera_up.tpl"}

        <div id="warnings-validation"></div>

        <table class="adminheading">
            <tbody>
                <tr>
                    <th>&nbsp;</th>
                </tr>
            </tbody>
        </table>
        <table class="adminlist" id="tabla"  width="99%" cellpadding=0 cellspacing=0 >
            <tbody>
                <tr>
                    <td align="right" valign="middle" style="padding:4px;text-align:right; width:100px;">
                        <label for="title">{t}Title{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap"  colspan="2">
                        <input type="text" id="title" name="title" title="Título" value="{$category->title|clearslash}"
                            class="required" size="100" />
                    </td>
                </tr>
                {if !empty($category->name)}
                    <tr>
                        <td valign="middle"  style="padding:4px;text-align:right; width:100px;">
                                <label for="title">{t}Internal name:{/t}</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap"   colspan="2">
                            <input type="text" id="name" name="name" title="carpeta categoria" readonly
                                        value="{$category->name|clearslash}" class="required" size="100" />
                        </td>
                    </tr>
                {/if}
                <tr>
                    <td align="right" valign="top" style="padding:4px;text-align:right; width:100px;">
                        <label for="title">{t}Subsection of:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" >
                        <select name="subcategory" class="required" size="12">
                            <option value="0" {if empty($category->fk_content_category) || $category->fk_content_category eq '0'}selected{/if}> -- </option>
                            {section name=as loop=$allcategorys}
                                <option value="{$allcategorys[as]->pk_content_category}" {if $category->fk_content_category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                            {/section}
                        </select>
                    </td>
                    {* TODO: gestionar dinamicamente, con tabla content_type *}
                    <td valign="top" style="text-align:left; width:340px;">
                        <h3>{t}Category available for:{/t}</h3>
                        <div class="utilities-conf" style="width:60%;">
                            <table style="padding:4px; margin-left:10px;">
                                <tr>
                                    <td  style="padding:4px;"> Global:</td>
                                    <td>
                                        <input type="radio" id="internal_category" name="internal_category"  value="1"
                                        {if empty($category->fk_content_category) || $category->internal_category eq 1} checked="checked"{/if}>
                                    </td>
                                    <!--<td  style="padding:4px;"> </td>
                                    <td  style="padding:4px;"> Álbumes:</td>
                                    <td> <input type="radio" id="internal_category" name="internal_category"  value="3"
                                        {if $category->internal_category eq 3} checked="checked"{/if}>
                                    </td>-->

                                </tr><!--
                                 <tr>
                                    <td  style="padding:4px;"> Vídeos:</td>
                                    <td> <input type="radio" id="internal_category" name="internal_category"  value="5"
                                        {if $category->internal_category eq 5} checked="checked"{/if}>
                                    </td>
                                   <td  style="padding:4px;"> </td>
                                    <td style="padding:4px;" > Kiosco: </td>
                                    <td> <input type="radio" id="internal_category" name="internal_category"  value="4"
                                        {if $category->internal_category eq 4} checked="checked"{/if}>
                                    </td>
                                </tr>-->
                            </table>
                         </div>
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="middle" style="width:100px;text-align:right">
                        <label for="inmenu">{t}Show in menu:{/t}</label>
                    </td>
                    <td style="padding:4px;" colspan="2" >
                        <input type="checkbox" id="inmenu" name="inmenu"
                               value="{if empty($category->fk_content_category) || $category->inmenu eq 1}1{else}0{/if}"
                            {if empty($category->fk_content_category) || $category->inmenu eq 1} checked="checked"{/if}>
                            {t}If this option is activated this category will be showed in menu{/t}
                    </td>
                </tr>
                 <tr>
                    <td valign="middle" style="padding:4px;text-align:right; width:100px;">
                        <label for="inmenu">{t}Frontpage logo:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" >
                        <input type="file" id="logo_path" name="logo_path"  />
                    </td>
                     <td style="padding:4px;" nowrap="nowrap" rowspan="2" >
                         {if !empty($category->logo_path)}<img src="../media/sections/{$category->logo_path}" >{/if}
                     </td>
                </tr>
                <tr>
                    <td valign="middle" style="padding:4px;text-align:right; width:100px;">
                        <label for="inmenu">{t}Color:{/t}</label>
                    </td>
                    <td style="padding:4px;" colspan="2" >
                        <script type="application/x-javascript">
                            initPicker('color','{$category->color}', 24);
                        </script>
                    </td>
                </tr>

                {if !empty($subcategorys)}
                    <tr>
                        <td valign="top" style="text-align:right; ">
                            <label>{t}Subsections:{/t}</label>
                        </td>
                        <td nowrap="nowrap" colspan="2">
                            <table border="0" class="adminlist" id="cates" style="margin:0 10px 10px 0; width:90%">
                                <thead>
                                    <tr>
    
                                        <th class="title"  style="text-align:left;" >{t}Title:{/t}</th>
                                        <th style="width:120px;text-align:left;" >{t}Internal name:{/t}</th>
                                        <th  style="width:80px;">{t}Type:{/t}</th>
                                        <th align="center"  style="width:80px;">{t}In menu:{/t}</th>
                                        <th align="center" style="width:80px;">{t}Edit{/t}</th>
                                        <th align="center" style="width:80px">{t}Delete{/t}</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td colspan="6">
                                        <div id="subcates" class="seccion" style="float:left;width:100%;">
                                            {section name=s loop=$subcategorys}
                                                <table width="100%" class="tabla" style="padding:0px;cursor:pointer;" id="{$subcategorys[s]->pk_content_category}">
                                                    <tr>
                                                        <td style="font-size: 11px;text-align:left;">
                                                             {$subcategorys[s]->title}</a>
                                                        </td>
                                                        <td style="font-size: 11px;width:120px;text-align:left;">
                                                             {$subcategorys[s]->name}</a>
                                                        </td>
                                                        <td style="font-size: 11px;width:80px;" align="center">
                                                          {if $subcategorys[s]->internal_category eq 3}
                                                             <img style="width:20px;" src="{$params.IMAGE_DIR}album.png" border="0" alt="Sección de Album" />
                                                          {elseif $subcategorys[s]->internal_category eq 5}
                                                             <img  style="width:20px;" src="{$params.IMAGE_DIR}video.png" border="0" alt="Sección de Videos" />
                                                          {else}
                                                              <img  style="width:20px;" src="{$params.IMAGE_DIR}advertisement.png" border="0" alt="Sección Global" />
                                                          {/if}
                                                        </td>
                                                        <td style="font-size: 11px;width:80px;"  align="center">
                                                            {if $subcategorys[s]->inmenu==1} {t}Yes{/t} {else}{t}No{/t}{/if}
                                                        </td>
                                                        <td style="font-size: 11px;width:80px;" align="center">
                                                            <a href="{$smarty.server.PHP_SELF}?action=read&id={$subcategorys[s]->pk_content_category}" title="Modificar">
                                                                <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                                            </a>
                                                        </td>
                                                        <td style="font-size: 11px;width:80px;" align="center">
                                                            <a href="#" onClick="javascript:confirmar(this, {$subcategorys[s]->pk_content_category});" title="Eliminar">
                                                                <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                    {/section}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                {/if}
            </tbody>
            <tfoot>
                <tr class="pagination">
                    <td colspan=3></td>
                </tr>
            </tfoot>
        </table>
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
    </form>
</div><!--fin wrapper-content-->
{/block}
