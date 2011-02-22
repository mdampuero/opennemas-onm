{extends file="base/admin.tpl"}


{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilscategory.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}MiniColorPicker.js"></script>
{/block}

{block name="content"}
<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>


        <div id="media_msg" style="float:right;width:300px;display:none;">   </div>
        {if !empty($msg)}
            <script type="text/javascript">
                {if $smarty.request.resp eq 'SI'}
                    showMsg({ 'warn':['{t}Deleted succesfulySe ha eliminado correctamente.{/t}'] },'inline');
                {elseif $smarty.request.resp eq 'NO'}
                    showMsg({ 'warn':['{t}Not deleted, the section is not empty.{/t}<br />'] },'inline');
                {elseif $smarty.request.resp eq 'ya'}
                    showMsg({ 'warn':['{t}No se ha podido crear, la seccion ya existe.{/t}' ]},'inline');
                {elseif $smarty.request.resp eq 'EMPTY'}
                    showMsg({ 'warn':['{t}Se ha vaciado correctamente.{/t}'] },'inline');
                {/if}
            </script>
        {/if}

        <ul id="tabs">
            <li>
                <a href="#listado">{t}Available sections{/t}</a>
            </li>
            <li>
                <a href="#ordenar">{t}Sort sections{/t}</a>
            </li>
        </ul>

        <div class="panel" id="listado">

            {include file="botonera_up.tpl" type="list"}
            <table class="adminlist" id="tabla"  width="100%" style="margin-top:10px;">
                <thead>
                    <tr>
                        <th width="25%" class="title">{t}Title{/t}</th>
                        <th width="25%" align="center">{t}Internal name{/t}</th>
                        <th align="center" width="10%">{t}See in menu{/t}</th>
                        <th align="center" width="15%">{t}Edit{/t}</th>
                        <th align="center" width="25%">{t}Delete{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="9">
                            {assign var=containers value=1}
                            <div id="ctes" class="seon" style="float:left;width:100%;"> <br>
                                {section name=c loop=$categorys}
                                    {if $categorys[c]->internal_category neq 4}
                                        {if $containers eq 1 && $categorys[c]->inmenu eq 0} <hr> <h2>{t}NO MENU{/t}</h2> <hr>   {assign var=containers value=0} {/if}
                                        {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                                    {/if}
                                {sectionelse}
                                    <h2><b>{t}No available sections{/t}</b></h2>
                                {/section}
                                <hr> <h2>KIOSKO</h2> <hr>
                                {section name=c loop=$categorys}
                                    {if $categorys[c]->internal_category eq 4}
                                        {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                                    {/if}
                                {sectionelse}
                                    <h2><b>{t}No available sections{/t}</b></h2>
                                {/section}
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" align="center">{$paginacion->links}</td>
                    </tr>
                </tfoot>
            </table>
        </div>


        <div class="panel" id="ordenar" style="width:95%">

            {include file="botonera_up.tpl" type="order"}

            <table class="adminlist" id="tabla"  width="99%" cellpadding=0 cellspacing=0 >
                <thead>
                    <tr>
                        <th width="25%" class="title">T&iacute;tulo</th>
                        <th width="25%" align="center">Nombre interno</th>
                        <th align="center" width="10%">Ver En menu</th>
                        <th align="center" width="15%">Modificar</th>
                        <th align="center" width="25%">Eliminar</th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="5">
                        <div id="cates" class="seccion" style="float:left;width:100%;"> <br />
                            {section name=c loop=$ordercategorys}
                                {if $ordercategorys[c]->internal_category neq "4"}
                                    <table width="100%"  id="{$ordercategorys[c]->pk_content_category}" class="tabla" cellpadding=0 cellspacing=0 >
                                        <tr {cycle values="class=row0,class=row1"} style="cursor:pointer;border:0px; padding:0px;margin:0px;">
                                            <td style="padding:10px;font-size: 11px;width:20%;">
                                                 {if $categorys[c]->internal_category eq 3}
                                                     <img style="width:20px;" src="{$params.IMAGE_DIR}album.png" border="0" alt="Sección de Album" />
                                                 {elseif $categorys[c]->internal_category eq 5}
                                                     <img  style="width:20px;" src="{$params.IMAGE_DIR}video.png" border="0" alt="Sección de Videos" />
                                                 {/if}
                                                {$ordercategorys[c]->title}
                                            </td>
                                            <td align="center" style="padding:10px;font-size: 11px;width:25%;">
                                                {$ordercategorys[c]->name|clearslash}</a>
                                            </td>
                                            <td align="center" style="padding:10px;font-size: 11px;width:10%;">
                                                {if $ordercategorys[c]->inmenu==1}
                                                    <a href="?id={$ordercategorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                                                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                                                {else}
                                                    <a href="?id={$ordercategorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                                                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                                                {/if}
                                            </td>
                                            <td style="padding: 0px 10px; height: 40px;width:15%;" align="center">
                                                {if $ordercategorys[c]->internal_category==1}
                                                    <a href="#" onClick="javascript:enviar(this, '_self', 'read', {$ordercategorys[c]->pk_content_category});" title="Modificar">
                                                        <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                                    </a>
                                                {/if}
                                            </td>
                                            <td style="padding: 0px 10px; height: 40px;width:15%;" align="center">
                                                {if $ordercategorys[c]->internal_category==1}
                                                    <a href="#" onClick="javascript:confirmar(this, {$ordercategorys[c]->pk_content_category});" title="Eliminar">
                                                        <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                                    </a>
                                                    {else} &nbsp;
                                                {/if}
                                            </td>
                                        </tr>
                                    </table>
                                {/if}
                            {sectionelse}
                                <h2><b>Ning&uacute;na secci&oacute;n guardada</b></h2>
                            {/section}
                        </div>
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
                // <![CDATA[
                    Sortable.create('cates',{
                        tag:'table',
                        dropOnEmpty: true,
                        containment:["cates"],
                        constraint:false});
                // ]]>
            </script>
        </div>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id}" />
    </form>
</div><!--fin wrapper-content-->
{/block}
