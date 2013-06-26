{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script>
        var opinion_manager_urls = {
            batch_delete: '{url name=admin_opinions_batch_delete category=$category page=$page}'
        }
    </script>
    {script_tag src="/onm/jquery-functions.js" language="javascript"}

{/block}

{block name="content"}
<form action="{url name=admin_opinions}" method="get" name="formulario" id="formulario">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Opinions{/t} :: </h2>
            <div class="section-picker">
                <div class="title-picker btn">
                    <span class="text">{if $home}{t}Opinion frontpage{/t}{else}{t}Listing{/t}{/if}</span>
                    <span class="caret"></span>
                </div>
                <div class="options">
                    {acl isAllowed="OPINION_FRONTPAGE"}
                    <a href="{url name=admin_opinions_frontpage}" {if $home}class="active"{/if}>{t}Opinion frontpage{/t}</a>
                    {/acl}
                    <a href="{url name=admin_opinions}" {if !$home}class="active"{/if}>{t}Listing{/t}</a>
                </div>
            </div>
        </div>
        <ul class="old-button">
            {acl isAllowed="OPINION_AVAILABLE"}
            <li class="batch-actions">

                <a href="#">
                    <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                    <br/>{t}Batch actions{/t}
                </a>

                <ul class="dropdown-menu">
                    <li>
                        <button type="submit" name="new_status" value="1" href="#" id="batch-publish">
                            {t}Batch publish{/t}
                        </button>
                    </li>
                    <li>
                        <button type="submit" name="new_status" value="0" href="#" id="batch-unpublish">
                            {t}Batch unpublish{/t}
                        </button>
                    </li>
                    <li>
                        <button type="submit" name="new_status" value="1" id="batch-inhome">
                            {t escape="off"}Batch in home{/t}
                        </button>
                    </li>
                    <li>
                        <button type="submit" name="new_status" value="0" id="batch-noinhome">
                            {t escape="off"}Batch drop from home{/t}
                        </button>
                    </li>
                </ul>
                {acl isAllowed="OPINION_DELETE"}
                <li>
                    <a class="delChecked" data-controls-modal="modal-opinion-batchDelete" href="#" title="{t}Delete{/t}">
                    <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />
                    {t}Delete{/t}
                </a>
                </li>
                {/acl}

            </li>
            {/acl}

            {acl isAllowed="OPINION_FRONTPAGE"}
            {if $home}
                <li>
                    <button id="save_positions" title="{t}Save positions{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save positions{/t}" alt="{t}Save positions{/t}"><br />
                        {t}Save positions{/t}
                    </button>
                </li>
            {/if}
            {/acl}
            {acl isAllowed="OPINION_SETTINGS"}
            <li>
                <button type="submit" id="opinion_clearcache">
                    <img border="0" src="{$params.IMAGE_DIR}clearcache.png" title="{t}Clean cache{/t}" alt="{t}Clean cache{/t}"/>
                    <br />{t}Clean cache{/t}
                </button>
            </li>
            <li>
                <a href="{url name=admin_opinions_config}" class="admin_add" title="{t}Config opinion module{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="{t}Config opinion module{/t}"/><br />
                    {t}Settings{/t}
                </a>
            </li>
            {/acl}
            {acl isAllowed="OPINION_CREATE"}
            <li>
                <a href="{url name=admin_opinion_create}" class="admin_add" title="{t}New opinion{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}opinion.png" title="{t}New opinion{/t}" alt="{t}New opinion{/t}"><br />
                    {t}New opinion{/t}
                </a>
            </li>
            {/acl}
        </ul>
    </div>
</div>
    <div class="wrapper-content">

        {render_messages}

        <div id="warnings-validation"></div><!-- /warnings-validation -->

        <div id="list_opinion">
        {if $home}
            {include file="opinion/partials/_opinion_list_home.tpl"}
        {else}
            {include file="opinion/partials/_opinion_list.tpl"}
        {/if}
        </div>
    </div>
</form>
    {include file="opinion/modals/_modalDelete.tpl"}
    {include file="opinion/modals/_modalBatchDelete.tpl"}
    {include file="opinion/modals/_modalAccept.tpl"}
{/block}


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
        $('#batch-inhome, #batch-noinhome').on('click', function(e, ui) {
            $('#formulario').attr('action', "{url name=admin_opinions_batch_inhome}");
        });

        $('#batch-publish, #batch-unpublish').on('click', function(e, ui) {
            $('#formulario').attr('action', "{url name=admin_opinions_batch_publish}");
        });


        $('#batch-delete').on('click', function(e, ui){
            e.preventDefault();
            $('#formulario').attr('action', "{url name=admin_opinions_batch_delete}");
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