{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script>
    jQuery(document).ready(function($) {
        $('.minput, #toggleallcheckbox').on('click', function() {
            checkbox = $(this).find('input[type="checkbox"]');
            checkbox.attr(
               'checked',
               !checkbox.is(':checked')
            );
            var checked_elements = $('input[type="checkbox"]:checked').length;
            if (checked_elements > 0) {
                $('.old-button .batch-actions').fadeIn('fast');
            } else {
                $('.old-button .batch-actions').fadeOut('fast');
            }
        });
        $('#batch-inhome, #batch-noinhome').on('click', function(e, ui){
            e.preventDefault();
            $('#formulario').attr('action', "{url name=admin_opinions_batch_inhome}");
            $('#formulario').submit();
        });
        $('#batch-publish, batch-unpublish').on('click', function(e, ui){
            e.preventDefault();
            $('#formulario').attr('action', "{url name=admin_opinions_batch_publish}");
            $('#formulario').submit();
        });
        $('#batch-delete').on('click', function(e, ui){
            e.preventDefault();
            $('#formulario').attr('action', "{url name=admin_opinions_batch_delete}");
            $('#formulario').attr('method', "POST");
            $('#formulario').submit();
        });

        $('#opinion_clearcache').on('click', function(e, ui) {
            e.preventDefault();
            jQuery.ajax({
                url: "{url name=admin_tpl_manager_cleanfrontpage category=opinion}",
                success: function(data){
                    jQuery('#warnings-validation').html(data);
                }
            });
        });

        {if $home}
        $( "#list_opinion tbody" ).sortable({
            items: "tr:not(.header)",
            containment: 'parent'
        });
        $( "#sortable" ).disableSelection();

        $('#save_positions').on('click', function(e, ui) {
            e.preventDefault();
            var content_positions = [
                'director-opinion',
                'editorial-opinion',
                'normal-opinion'
            ];
            var elements = [];
            $.each(content_positions, function(key, position_name) {

                var name = '.'+position_name
                var items = jQuery(name);

                var elements_in_position = [];
                items.each(function(key, item) {
                    elements_in_position.push($(item).data('id'));
                });

                if (elements_in_position.length > 0) {
                    elements.push(elements_in_position);
                };
            });
            $.ajax({
                url : '{url name=admin_opinions_savepositions}',
                method: 'POST',
                data: { positions: JSON.stringify(elements)},
                success: function(data) {
                    $('#warnings-validation').html(data);
                }
            });
        });
        {/if}


    });
    </script>
{/block}


{block name="content"}
<form action="{url name=admin_opinions}" method="get" name="formulario" id="formulario">
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Opinion Manager{/t} :: {t}Listing opinions{/t}</h2></div>
        <ul class="old-button">
            {acl isAllowed="OPINION_AVAILABLE"}
            <li class="batch-actions">

                <a href="#">
                    <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                    <br/>{t}Batch actions{/t}
                </a>

                <ul class="dropdown-menu">
                    <li>
                        <button type="submit" name="status" value="0" href="#" id="batch-publish">
                            {t}Batch publish{/t}
                        </button>
                    </li>
                    <li>
                        <button type="submit" name="status" value="1" id="batch-unpublish">
                            {t}Batch unpublish{/t}
                        </a>
                    </li>
                    <li>
                        <button type="submit" name="status" value="0" id="batch-inhome">
                            {t escape="off"}Batch in home{/t}
                        </a>
                    </li>
                    <li>
                        <button type="submit" name="status" value="1" id="batch-noinhome">
                            {t escape="off"}Batch drop from home{/t}
                        </a>
                    </li>
                    {acl isAllowed="OPINION_DELETE"}
                    <li>
                        <button type="submit" id="batch-delete" title="{t}Delete{/t}">
                            {t}Delete{/t}
                        </button>
                    </li>
                    {/acl}
                </ul>

            </li>

            {/acl}

            {acl isAllowed="OPINION_CREATE"}
            <li>
                <a href="{url name=admin_opinion_create}" class="admin_add" accesskey="N" tabindex="1">
                    <img border="0" src="{$params.IMAGE_DIR}opinion.png" title="Nuevo" alt="Nuevo"><br />{t escape="off"}New opinion{/t}
                </a>
            </li>
            {/acl}
            <li class="separator"></li>
            {acl isAllowed="OPINION_FRONTPAGE"}
            {if $home}
                <li>
                    <button id="save_positions" title="{t}Save positions{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar Cambios" alt="Guardar Posiciones"><br />{t}Save positions{/t}
                    </button>
                </li>
            {/if}
            {/acl}
            {acl isAllowed="OPINION_SETTINGS"}
            <li>
                <button type="submit" id="opinion_clearcache">
                    <img border="0" src="{$params.IMAGE_DIR}clearcache.png" title="{t}Clean cache{/t}"/>
                    <br />{t}Clean cache{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_opinions_config}" class="admin_add" title="{t}Config album module{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" /><br />
                    {t}Settings{/t}
                </a>
            </li>
            {/acl}
        </ul>
    </div>
</div>
    <div class="wrapper-content">

        {render_messages}

        <div id="warnings-validation"></div><!-- /warnings-validation -->

        <div>
            <ul class="pills clearfix">
                <li>
                <a href="{url name=admin_opinions_frontpage}" {if $home}class="active"{/if}>{t}Opinion frontpage{/t}</a>
                </li>
                <li>
                    <a href="{url name=admin_opinions}" {if !$home}class="active"{/if}>{t}Listing{/t}</a>
                </li>
            </ul>

            <div id="list_opinion">
            {if $home}
                {include file="opinion/partials/_opinion_list_home.tpl"}
            {else}
                {include file="opinion/partials/_opinion_list.tpl"}
            {/if}
            </div>
         </div>
    </div>
</form>
    {include file="opinion/modals/_modalDelete.tpl"}
    {include file="opinion/modals/_modalBatchDelete.tpl"}
    {include file="opinion/modals/_modalAccept.tpl"}
{/block}