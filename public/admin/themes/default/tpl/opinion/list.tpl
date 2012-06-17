{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/utilsopinion.js"}
    {script_tag src="/photos.js"}
    {script_tag src="/jquery-onm/jquery.inputlength.js"}
    <script>
    jQuery(document).ready(function($){
        $('#title').inputLengthControl();
    });
    jQuery('#buton-batchNoInHome').on('click', function(){
        jQuery('#action').attr('value', "batchInHome");
        jQuery('#status').attr('value', "0");
        jQuery('#formulario').attr('method', "POST");
        jQuery('#formulario').submit();
        e.preventDefault();
    });
    jQuery('#buton-batchInHome').on('click', function(){
        jQuery('#action').attr('value', "batchInHome");
        jQuery('#status').attr('value', "1");
        jQuery('#formulario').attr('method', "POST");
        jQuery('#formulario').submit();
        e.preventDefault();
    });
    jQuery('#buton-batchnoFrontpage').on('click', function(){
        jQuery('#action').attr('value', "batchFrontpage");
        jQuery('#status').attr('value', "0");
        jQuery('#formulario').attr('method', "POST");
        jQuery('#formulario').submit();
        e.preventDefault();
    });
    jQuery('#buton-batchFrontpage').on('click', function(){
        jQuery('#action').attr('value', "batchFrontpage");
        jQuery('#status').attr('value', "1");
        jQuery('#formulario').attr('method', "POST");
        jQuery('#formulario').submit();
        e.preventDefault();
    });

    jQuery('#opinion_clearcache').on('click', function(e, ui) {
        e.preventDefault();
        jQuery.ajax({
            url: "{url name=admin_tpl_manager_cleanfrontpage category=opinion}",
            success: function(data){
                jQuery('#warnings-validation').html(data);
            }
        });
    });
    jQuery('.minput').on('click', function() {
        checkbox = jQuery(this).find('input[type="checkbox"]');
        checkbox.attr(
           'checked',
           !checkbox.is(':checked')
        );
        var checked_elements = jQuery('input[type="checkbox"]:checked').length;
        if (checked_elements > 0) {
            jQuery('.old-button .batch-actions').fadeIn('fast');
        } else {
            jQuery('.old-button .batch-actions').fadeOut('fast');
        }
    });


    </script>
{/block}


{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" {$formAttrs}>
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Opinion Manager{/t} :: {t}Listing opinions{/t}</h2></div>
        <ul class="old-button">
            {acl isAllowed="OPINION_DELETE"}
            <li>
                 <a class="delChecked" data-controls-modal="modal-opinion-batchDelete" href="#" title="{t}Delete{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />{t}Delete{/t}
                </a>
            </li>
            {/acl}
            {acl isAllowed="OPINION_AVAILABLE"}
            <li class="batch-actions">

                <a href="#">
                    <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                    <br/>{t}Batch actions{/t}
                </a>

                <ul class="dropdown-menu">
                    <li>
                        {if $type_opinion eq '-1'}
                        <a href="#" id="buton-batchnoFrontpage">
                            {t}Unpublish{/t}
                        </a>
                        {else}
                        <a href="#" id="buton-batchFrontpage">
                            {t}Publish{/t}
                        </a>
                        {/if}
                    </li>
                    {if $type_opinion neq '-1'}
                    <li>
                        <a href="#" id="buton-batchInHome">
                            {t}Put in home{/t}
                        </a>
                    </li>
                    {/if}
                    <li>
                        <a href="#" id="buton-batchNoInHome">
                            {t escape="off"}Delete from home{/t}
                        </a>
                    </li>
                </ul>

            </li>

            {/acl}

            {acl isAllowed="OPINION_FRONTPAGE"}
             {if $type_opinion eq '-1'}
                <li>
                    <a href="#" class="admin_add" onClick="savePositionsOpinion();" title="Guardar Positions" alt="Guardar Posiciones">
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
                    <li>
                        <a href="#" id="opinion_clearcache">
                            <img border="0" src="{$params.IMAGE_DIR}clearcache.png" title="{t}Clean cache{/t}" alt="" />
                            <br />{t}Clean cache{/t}
                        </a>
                    </li>
             {/acl}
             <li class="separator"> </li>

            {acl isAllowed="AUTHOR_ADMIN"}
            <li >
                <a href="author.php?action=list&amp;desde=opinion" class="admin_add" name="submit_mult" value="Listado Autores" title="Listado Autores">
                    <img border="0" src="{$params.IMAGE_DIR}authors.png" title="Listado Autores" alt="Listado Autores"><br />Ver Autores
                </a>
            </li>
            {/acl}
        </ul>
    </div>
</div>
    <div class="wrapper-content">

        {render_messages}
        <div id="msg"></div>
        <div id="warnings-validation"></div><!-- /warnings-validation -->

        <div>
            <ul class="pills clearfix">
                <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&amp;type_opinion=-1" id="home" {if $type_opinion==-1}class="active"{/if}>{t}Opinion frontpage{/t}</a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&amp;type_opinion=0" id="author" {if $type_opinion=='0'}class="active"{/if}>{t}Author Opinions{/t}</a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&amp;type_opinion=1" id="editorial" {if $type_opinion=='1'}class="active"{/if}>{t}Editorial{/t}</a>
                </li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&amp;type_opinion=2" id="director" {if $type_opinion=='2'}class="active"{/if}>{t}Director opinion{/t}</a>
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

    <input type="hidden" name="category" id="category" value="{$category}" />
    <input type="hidden" id="status" name="status" value="" />
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
    {include file="opinion/modals/_modalDelete.tpl"}
    {include file="opinion/modals/_modalBatchDelete.tpl"}
    {include file="opinion/modals/_modalAccept.tpl"}
{/block}