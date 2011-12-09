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
            {acl isAllowed="OPINION_DELETE"}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);return false;" name="submit_mult" value="Eliminar" title="Eliminar">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />{t}Delete{/t}
                </a>
            </li>
            {/acl}
            {acl isAllowed="OPINION_AVAILABLE"}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);return false;" name="submit_mult" value="noFrontpage" title="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />{t}Unpublish{/t}
                </a>
            </li>
            {/acl}            
            {acl isAllowed="OPINION_DELETE"}
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
            {/acl}
            
            
            {acl isAllowed="OPINION_FRONTPAGE"}
             {if $type_opinion eq '-1'}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:savePositionsOpinion();" title="Guardar Positions" alt="Guardar Posiciones">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar Cambios" alt="Guardar Posiciones"><br />{t}Save positions{/t}
                    </a>
                </li>
            {/if}
            {/acl}
            {acl isAllowed="OPINION_CREATE"}
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add" accesskey="N" tabindex="1">
                    <img border="0" src="{$params.IMAGE_DIR}opinion.png" title="Nuevo" alt="Nuevo"><br />{t escape="off"}New opinion{/t}
                </a>
            </li>
            {/acl}
            {acl isAllowed="OPINION_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config album module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Configurations{/t}
                        </a>
                    </li>
             {/acl}
             <li class="separator"> </li>

            {acl isAllowed="AUTHOR_ADMIN"}
            <li >
                <a href="author.php?action=list&desde=opinion" class="admin_add" name="submit_mult" value="Listado Autores" title="Listado Autores">
                    <img border="0" src="{$params.IMAGE_DIR}authors.png" title="Listado Autores" alt="Listado Autores"><br />Ver Autores
                </a>
            </li>
            {/acl}
        </ul>
    </div>
</div>
    <div class="wrapper-content">

        {render_messages}
     
        <div>
            <ul class="pills clearfix">
                <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=-1" id="home" {if $type_opinion==-1}class="active"{/if}>{t}HOME{/t}</a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=0" id="author" {if $type_opinion=='0'}class="active"{/if}>{t}Author Opinions{/t}</a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=1" id="editorial" {if $type_opinion=='1'}class="active"{/if}>{t}Editorial{/t}</a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&type_opinion=2" id="director" {if $type_opinion=='2'}class="active"{/if}>{t}Director opinion{/t}</a>
                </li>
            </ul>
 
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
