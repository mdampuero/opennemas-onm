{extends file="base/admin.tpl"}

{block name="header-js" append}
    {javascripts src="@AdminTheme/js/onm/jquery-functions.js"}
        <script text="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
{/block}

{block name="content"}
    <form action="{url name=admin_opinions}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentListController" ng-init="init('opinion', { content_status: -1, title: '', blog: {if $blog == 1}1{else}0{/if}, author: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
        <div class="page-navbar actions-navbar">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <h4>
                                <i class="fa fa-quote-right"></i>
                                {if $contentType eq 'blog'}
                                    Posts
                                {else}
                                    {t}Opinions{/t}
                                {/if}
                            </h4>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks dropdown">
                            <div data-toggle="dropdown">
                                {if $home}
                                    {t}Opinion frontpage{/t}
                                {else}
                                    {t}Listing{/t}
                                {/if}
                                <span class="caret"></span>
                            </div>
                            <ul class="dropdown-menu">
                                {acl isAllowed="OPINION_FRONTPAGE"}
                                    <li>
                                        <a href="{url name=admin_opinions_frontpage}">
                                            {t}Opinion frontpage{/t}
                                        </a>
                                    </li>
                                {/acl}
                                <li>
                                    <a href="{url name=admin_opinions}">
                                        {t}Listing{/t}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <div class="all-actions pull-right">
                        <ul class="nav quick-section">
                            {acl isAllowed="OPINION_SETTINGS"}
                                <li class="quicklinks">
                                   <a class="btn btn-link" href="{url name=admin_opinions_config}" title="{t}Config opinion module{/t}">
                                        <i class="fa fa-cog fa-lg"></i>
                                    </a>
                                </li>
                                <li class="quicklinks">
                                    <span class="h-seperate"></span>
                                </li>
                            {/acl}
                            {acl isAllowed="OPINION_FRONTPAGE"}
                                {if $home}
                                    <li>
                                        <button class="btn btn-link" id="save_positions" title="{t}Save positions{/t}">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </li>
                                    <li class="quicklinks">
                                        <span class="h-seperate"></span>
                                    </li>
                                {/if}
                            {/acl}
                            {acl isAllowed="OPINION_CREATE"}
                                <li class="quicklinks">
                                    <a class="btn btn-primary" href="{url name=admin_opinion_create}" title="{t}New opinion{/t}">
                                        <i class="fa fa-plus"></i>
                                        {t}New opinion{/t}
                                    </a>
                                </li>
                            {/acl}
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {if $home}
            {include file="opinion/partials/_opinion_list_home.tpl"}
        {else}
            {include file="opinion/partials/_opinion_list.tpl"}
        {/if}

    <script type="text/ng-template" id="modal-delete">
        {include file="common/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
</form>
    {include file="opinion/modals/_modalAccept.tpl"}
{/block}


{block name="footer-js" append}
    <script>
    jQuery(document).ready(function($) {
        {if $home}
        $( "#list-opinion tbody" ).sortable({
            items: "tr:not(.table-header)",
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
