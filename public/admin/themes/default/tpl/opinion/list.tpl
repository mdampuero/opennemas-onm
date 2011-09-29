{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/utilsopinion.js" language="javascript"}
    {script_tag src="/photos.js" language="javascript"}
{/block}


{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Opinion Manager{/t} :: {t}Listing opinions{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);return false;" name="submit_mult" value="Eliminar" title="Eliminar">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />{t}Delete{/t}
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);return false;" name="submit_mult" value="noFrontpage" title="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />{t}Unpublish{/t}
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);return false;" name="submit_mult" value="Frontpage" title="Frontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Frontpage" alt="Frontpage" ><br />{t}Publish{/t}
                </a>
            </li>
             {if $type_opinion neq '-1'}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 1);return false;" name="submit_mult" value="Frontpage" title="Frontpage">
                    <img border="0" src="{$params.IMAGE_DIR}gohome50.png"  title="Frontpage" alt="Frontpage" ><br />{t}Put in home{/t}
                </a>
            </li>
            {/if}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 0);return false;" name="submit_mult" value="Frontpage" title="Frontpage">
                    <img border="0" src="{$params.IMAGE_DIR}home_no50.png"  title="Frontpage" alt="Frontpage" ><br />{t escape="off"}Delete from home{/t}
                </a>
            </li>
            <li>
                <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                    <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
                </button>
            </li>
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add" accesskey="N" tabindex="1">
                    <img border="0" src="{$params.IMAGE_DIR}opinion.png" title="Nuevo" alt="Nuevo"><br />{t escape="off"}New opinion{/t}
                </a>
            </li>
             {if $type_opinion eq '-1'}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:savePositionsOpinion();" title="Guardar Positions" alt="Guardar Posiciones">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar Cambios" alt="Guardar Posiciones"><br />{t}Save positions{/t}
                    </a>
                </li>
            {/if}
             <li class="separator"> </li>

            <li >
                <a href="author.php?action=list&desde=opinion" class="admin_add" name="submit_mult" value="Listado Autores" title="Listado Autores">
                    <img border="0" src="{$params.IMAGE_DIR}authors.png" title="Listado Autores" alt="Listado Autores"><br />Ver Autores
                </a>
            </li>
        </ul>
    </div>
</div>
    <div class="wrapper-content">

        <div>
            <ul class="pills clearfix">
                <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=-1" id="home" {if $type_opinion==-1}class="active"{/if}>{t}HOME{/t}</font></a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=0" id="author" {if $type_opinion=='0'}class="active"{/if}>{t}Author Opinions{/t}</font></a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=1" id="editorial" {if $type_opinion=='1'}class="active"{/if}>{t}Editorial{/t}</font></a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=2" id="director" {if $type_opinion=='2'}class="active"{/if}>{t}Director opinion{/t}</font></a>
                </li>
            </ul>

            {if $type_opinion eq '0'}
                {assign value='Opinión del Autor' var='accion'}
            {elseif $type_opinion eq '1'}
                {assign value='Editorial' var='accion'}
            {elseif $type_opinion eq '2'}
                {assign value='Opinión del Director' var='accion'}
              {elseif $type_opinion eq '-1'}
                {assign value='Home' var='accion'}
            {/if}

            {if (isset($smarty.get.alert) && ($smarty.get.alert neq "")) or (isset($msg_alert) && ($msg_alert neq ""))}
            <div class="notice" style="margin-top:3px;">
                <p>{$smarty.get.alert|default:""}</p>
                <p>{$msg_alert|default:""}</p>
            </div>
            {/if}
            <div id="list_opinion">
                 {if $type_opinion=='-1'}
                     {include file="opinion/partials/_opinion_list_home.tpl"}
                 {else}
                     {include file="opinion/partials/_opinion_list.tpl"}
                 {/if}
            </div>
         </div>

        {dialogo script="print"}

        <script type="text/javascript" language="javascript">
        document.observe('dom:loaded', function() {
            if($('title')){
                new OpenNeMas.Maxlength($('title'), { });
            }
        });
        </script>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div><!--fin wrapper-content-->
</form>
{/block}

{block name="footer-js" append}
{if isset($smarty.get.msgdelete) && $smarty.get.msgdelete eq 'ok'}
    <script type="text/javascript" language="javascript">
        alert('{$smarty.get.msg}');
    </script>
{/if}
{/block}
