{extends file="base/admin.tpl"}


{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilscategory.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}MiniColorPicker.js"></script>
{/block}
{block name="header-css" append}
    <style type="text/css">
        .panel {
            border:none !important;
        }
    </style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{$titulo_barra}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$_SERVER['PHP_SELF']}?action=new" class="admin_add" accesskey="N" tabindex="1">
                    <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="Nueva" alt="Nueva"><br />{t}New section{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
        {if !empty($msg)}
            <script type="text/javascript">
                {if $smarty.request.resp eq 'SI'}
                    showMsg({ 'warn':['{t}Deleted succesfuly.{/t}'] },'inline');
                {elseif $smarty.request.resp eq 'NO'}
                    showMsg({ 'warn':['{t}Not deleted, the section is not empty.{/t}<br />'] },'inline');
                {elseif $smarty.request.resp eq 'ya'}
                    showMsg({ 'warn':['{t}Unable to create, section is already exists.{/t}' ]},'inline');
                {elseif $smarty.request.resp eq 'EMPTY'}
                    showMsg({ 'warn':['{t}Successfully emptied.{/t}'] },'inline');
                {/if}
            </script>
        {/if}

        <ul id="tabs">
            <li>
                <a href="#listado">{t}Available sections{/t}</a>
            </li>
            <li>
                <a href="/{$smarty.const.ADMIN_DIR}/controllers/menues/menues.php">{t}Sort menues{/t}</a>
            </li>
        </ul>

        <div class="panel" id="listado">

            <table class="adminheading">
                <tr>
                    <td>{t}Global categories{/t}</td>
                </tr>
            </table>
            <table class="adminlist" id="tabla"  width="100%">
                <thead>
                    <tr>
                        <th width="360px">{t}Title{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Name{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Type{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Articles{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Photos{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Advertisements{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Published{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Edit{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Empty{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Delete{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10">
                            {section name=c loop=$categorys}
                                {if $categorys[c]->internal_category eq 1}
                                    {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                                {/if}
                            {sectionelse}
                                <h2><strong>{t}No available sections{/t}</strong></h2>
                            {/section}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="10" align="center">{$paginacion->links}</td>
                    </tr>
                </tfoot>
            </table>

            <br>


            <table class="adminheading">
                <tr>
                    <td>{t}Album categories{/t}</td>
                </tr>
            </table>
            <table class="adminlist" id="tabla"  width="100%">
                <thead>
                    <tr>
                        <th width="360px">{t}Title{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Name{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Type{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Articles{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Photos{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Advertisements{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Published{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Edit{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Empty{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Delete{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10">
                            {section name=c loop=$categorys}
                                {if $categorys[c]->internal_category eq 7}
                                    {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                                {/if}
                            {sectionelse}
                                <h2><strong>{t}No available sections{/t}</strong></h2>
                            {/section}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="10" align="center">{$paginacion->links}</td>
                    </tr>
                </tfoot>
            </table>

            <br>

            <table class="adminheading">
                <tr>
                    <td>{t}Video categories{/t}</td>
                </tr>
            </table>
            <table class="adminlist" id="tabla"  width="100%">
                <thead>
                    <tr>
                        <th width="360px">{t}Title{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Name{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Type{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Articles{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Photos{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Advertisements{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Published{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Edit{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Empty{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Delete{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10">
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq 9}
                                {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                            {/if}
                        {sectionelse}
                            <h2><strong>{t}No available sections{/t}</strong></h2>
                        {/section}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="5" align="center">{$paginacion->links}</td>
                    </tr>
                </tfoot>
            </table>

            <br>

            <table class="adminheading">
                <tr>
                    <td>{t}ePaper categories{/t}</td>
                </tr>
            </table>
            <table class="adminlist" id="tabla"  width="100%">
                <thead>
                    <tr>
                        <th width="360px">{t}Title{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Name{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Type{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Articles{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Photos{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Advertisements{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Published{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Edit{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Empty{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Delete{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10">
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq 14}
                                {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                            {/if}
                        {sectionelse}
                            <h2><strong>{t}No available sections{/t}</strong></h2>
                        {/section}

                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="5" align="center">{$paginacion->links}</td>
                    </tr>
                </tfoot>
            </table>
        </div>


        <div class="panel" id="ordenar" style="width:95%">

            {include file="botonera_up.tpl" type="order"}
            <div id="warnings-validation"></div>
            <table class="adminheading">
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>

            <table class="adminlist" id="tabla"  width="99%" cellpadding=0 cellspacing=0 >
                <thead>
                    <tr>
                        <th width="20%" class="title">T&iacute;tulo</th>
                        <th width="25%" align="center">Nombre interno</th>
                        <th align="center" width="10%">Ver En menu</th>
                        <th align="center" width="15%">Modificar</th>
                        <th align="center" width="15%">Eliminar</th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="5">
                        <div id="cates" class="seccion" style="float:left;width:100%;"> <br />
                            {section name=c loop=$ordercategorys}
                            {*NO album(3) , NO planConecta(9)*}
                                {if $ordercategorys[c]->internal_category neq "4" && $ordercategorys[c]->pk_content_category neq "9" && $ordercategorys[c]->pk_content_category neq "3"}
                                    <table width="100%"  id="{$ordercategorys[c]->pk_content_category}" class="tabla" cellpadding=0 cellspacing=0 >
                                        <tr {cycle values="class=row0,class=row1"} style="cursor:pointer;border:0px; padding:0px;margin:0px;">
                                            <td style="padding:10px;font-size: 11px;width:20%;">
                                                 {if $categorys[c]->internal_category eq 7}
                                                     <img style="width:20px;" src="{$params.IMAGE_DIR}album.png" border="0" alt="Sección de Album" />
                                                 {elseif $categorys[c]->internal_category eq 9}
                                                     <img  style="width:20px;" src="{$params.IMAGE_DIR}video.png" border="0" alt="Sección de Videos" />
                                                 {/if}
                                                {$ordercategorys[c]->title}
                                            </td>
                                            <td align="center" style="padding:10px;font-size: 11px;width:25%;">
                                                {$ordercategorys[c]->name|clearslash}
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
                                <h2><strong>Ning&uacute;na secci&oacute;n guardada</strong></h2>
                            {/section}
                        </div>
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
                // <![CDATA[
                    Sortable.create('cates',
                                    {
                                        tag:'table',
                                        dropOnEmpty: true,
                                        containment:["cates"],
                                        onChange: function(item) {
                                            $('warnings-validation').update('<div class="notice">Por favor, recuerde guardar posiciones antes de terminar.</div>');
                                        },
                                        constraint:false
                                    }
                        );
                // ]]>
            </script>
        </div>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id}" />
    </form>
</div><!--fin wrapper-content-->
{/block}
