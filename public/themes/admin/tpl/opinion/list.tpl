{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="router.js" language="javascript" bundle="fosjsrouting" basepath="js"}
    {script_tag src="routes.js" language="javascript" common=1 basepath="js"}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
    {script_tag src="angular.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="ui-bootstrap-tpls-0.10.0.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="app.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="services.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="controllers.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="content-modal.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="content.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="fos-js-routing.js" language="javascript" bundle="backend" basepath="js/services"}
{/block}

{block name="content"}
<form action="{url name=admin_opinions}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('opinion', { available: -1, title: '', blog: -1, author: -1 }, 'title', 'backend_ws_contents_list')">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <h2>{if $contentType eq 'blog'}Posts{else}{t}Opinions{/t} {/if}::</h2>
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
            {acl isAllowed="OPINION_SETTINGS"}
                <li>
                    <a href="{url name=admin_opinions_config}" class="admin_add" title="{t}Config opinion module{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="{t}Config opinion module{/t}"/><br />
                        {t}Settings{/t}
                    </a>
                </li>
            {/acl}
            <li class="separator"></li>
            <li ng-if="selected.length > 0">
                <a href="#">
                    <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                    <br/>{t}Batch actions{/t}
                </a>
                <ul class="dropdown-menu" style="margin-top: 1px;">
                    {acl isAllowed="OPINION_AVAILABLE"}
                    <li>
                        <a href="#" ng-click="batchToggleAvailable(1, 'backend_ws_contents_batch_toggle_available')">
                            <i class="icon-eye-open"></i>
                            {t}Publish{/t}
                        </a>
                    </li>
                    <li>
                        <a href="#" ng-click="batchToggleAvailable(0, 'backend_ws_contents_batch_toggle_available')">
                            <i class="icon-eye-close"></i>
                            {t}Unpublish{/t}
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#" ng-click="batchToggleInHome(1, 'backend_ws_contents_batch_toggle_in_home')">
                            <i class="go-home"></i>
                            {t escape="off"}In home{/t}
                        </a>
                    </li>
                    <li>
                        <a href="#" ng-click="batchToggleInHome(0, 'backend_ws_contents_batch_toggle_in_home')">
                            <i class="no-home"></i>
                            {t escape="off"}Drop from home{/t}
                        </a>
                    </li>
                    {/acl}
                    {acl isAllowed="OPINION_DELETE"}
                        <li class="divider"></li>
                        <li>
                            <a href="#" ng-click="open('modal-delete-selected')">
                                <i class="icon-trash"></i>
                                {t}Delete{/t}
                            </a>
                        </li>
                    {/acl}
                </ul>
            </li>
            <li class="separator" ng-if="selected.length > 0"></li>

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
    <script type="text/ng-template" id="opinion">
        <td>
            <input type="checkbox" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)">
        </td>
        <td>
            <strong>
            <span ng-if="content.author.name">
                [% content.author.name %]
            </span>
            <span ng-if="!content.author.name">
                [% content.author %]
            </span>
            -
            [% content.title %]
            </strong>
        </td>
        <td class="center">
            [% content.views %]
        </td>
        <td class="center">
            [% content.created %]
        </td>
        <td class="center">
            {acl isAllowed="OPINION_FRONTPAGE"}
                <button class="btn-link" ng-class="{ 'loading': content.home_loading == 1, 'go-home': content.in_home == 1, 'no-home': content.in_home == 0 }" ng-if="content.author.meta.is_blog != 1" ng-click="toggleInHome(content.id, $index, 'backend_ws_content_toggle_in_home')" type="button"></button>
                <span ng-if="content.author.meta.is_blog == 1">
                    Blog
                </span>
            {/acl}
        </td>
        <td class="center">
            {acl isAllowed="OPINION_AVAILABLE"}
                <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.available == 1, unpublished: content.available == 0 }" ng-click="toggleAvailable(content.id, $index, 'backend_ws_content_toggle_available')" type="button"></button>
            {/acl}
        </td>
        <td class="center">
            {acl isAllowed="OPINION_HOME"}
            <button class="btn-link" ng-class="{ loading: content.favorite_loading == 1, 'favorite': content.favorite == 1, 'no-favorite': content.favorite != 1 }" ng-click="toggleFavorite(content.id, $index, 'backend_ws_content_toggle_favorite')" ng-if="content.type_opinion == 0" type="button"></button>
            {/acl}
        </td>
        <td class="right">
            <div class="btn-group">
                {acl isAllowed="OPINION_UPDATE"}
                <button class="btn" ng-click="edit(content.id, 'admin_opinion_show')" type="button">
                    <i class="icon-pencil"></i>
                </button>
                {/acl}
                {acl isAllowed="OPINION_DELETE"}
                <button class="btn btn-danger" ng-click="open('modal-delete', $index)" type="button">
                    <i class="icon-trash icon-white"></i>
                </button>
                {/acl}
            </ul>
        </td>
    </script>
    <script type="text/ng-template" id="modal-delete">
        {include file="opinion/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="opinion/modals/_modalBatchDelete.tpl"}
    </script>
</form>
    {include file="opinion/modals/_modalAccept.tpl"}
{/block}


{block name="footer-js" append}
    <script>
    jQuery(document).ready(function($) {
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
