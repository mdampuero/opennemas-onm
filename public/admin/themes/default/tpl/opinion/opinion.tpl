{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsopinion.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}photos.js"></script>
{/block}


{block name="content"}
<div class="wrapper-content">

    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

    {* LISTADO ******************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}

        <div>
            <ul class="tabs">
                <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=-1" {if $type_opinion==-1} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>{t}HOME{/t}</font></a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=0" {if $type_opinion=='0'} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>{t}Author Opinions{/t}</font></a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=1" {if $type_opinion=='1'} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>{t}Editorial{/t}</font></a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=2" {if $type_opinion=='2'} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>{t}Director opinion{/t}</font></a>
                </li>
            </ul>
            {* Zona mensajes cambiar a msgShow *}
            <div style="padding:4px;">
                <h2  style="color:#D00; text-align:center;">{if $smarty.get.alert}{$smarty.get.alert} {else}{$msg_alert}{/if}</h2>
            </div>
            <br />
            {if $type_opinion eq '0'}
                {assign value='Opinión del Autor' var='accion'}
            {elseif $type_opinion eq '1'}
                {assign value='Editorial' var='accion'}
            {elseif $type_opinion eq '2'}
                {assign value='Opinión del Director' var='accion'}
              {elseif $type_opinion eq '-1'}
                {assign value='Home' var='accion'}
            {/if}
            {include file="botonera_up.tpl"}
            <div id="list_opinion">
                 {if $type_opinion=='-1'}
                     {include file="opinion/opinion_list_home.tpl"}
                 {else}
                     {include file="opinion/opinion_list.tpl"}
                 {/if}
            </div>
             {if $smarty.get.msgdelete eq 'ok'}
                 <script type="text/javascript" language="javascript">
                       alert('{$smarty.get.msg}');
                </script>
            {/if}
            <br />
         </div>
    {/if}

    {dialogo script="print"}
    {* FORMULARIO PARA ENGADIR ************************************** *}

    {if isset($smarty.request.action) && ($smarty.request.action eq "read" || $smarty.request.action eq "new")}

        {include file="opinion/opinion_edit.tpl"}

        <script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
        {literal}
            <script type="text/javascript" language="javascript">
                tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
            </script>

            <script type="text/javascript" language="javascript">
                OpenNeMas.tinyMceConfig.advanced.elements = "body";
                tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
            </script>
        {/literal}
    {/if}

    <script type="text/javascript" language="javascript">
    {literal}
        document.observe('dom:loaded', function() {
            if($('title')){
                new OpenNeMas.Maxlength($('title'), {});
            }
        });
    {/literal}
    </script>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
    </form>

</div><!--fin wrapper-content-->
{/block}
