{extends file="base/admin.tpl"}

{block name="header-js" append}
    {javascripts src="@AdminTheme/js/onm/jquery-functions.js"}
        <script text="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
{/block}

{block name="content"}
    <form action="{url name=admin_opinions}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('opinion', { content_status: -1, title: '', blog: {if $blog == 1}1{else}0{/if}, author: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
        <div class="page-navbar actions-navbar">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <h4>
                                <i class="fa fa-home fa-lg"></i>
                                {if $contentType eq 'blog'}Posts{else}{t}Opinions{/t}{/if}
                            </h4>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
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

        <div class="page-navbar filters-navbar">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <ul class="nav quick-section">
                        <li class="m-r-10 input-prepend inside search-form no-boarder">
                            <select class="select2" id="category" ng-model="shvs.search.fk_content_categories" data-label="{t}Category{/t}">
                                <option value="-1">{t}-- All --{/t}</option>
                                <optgroup label="{t}Special elements{/t}">
                                    <option value="0">{t}HOMEPAGE{/t}</option>
                                    <option value="4">{t}OPINION{/t}</option>
                                    <option value="3">{t}ALBUM{/t}</option>
                                    <option value="6">{t}VIDEO{/t}</option>
                                </optgroup>
                                <optgroup label="Categories">
                                    {section name=as loop=$allcategorys}
                                    {assign var=ca value=$allcategorys[as]->pk_content_category}
                                        <option value="{$allcategorys[as]->pk_content_category}">
                                            {$allcategorys[as]->title}
                                            {if $allcategorys[as]->inmenu eq 0}
                                                <span class="inactive">{t}(inactive){/t}</span>
                                            {/if}
                                        </option>
                                            {section name=su loop=$subcat[as]}
                                            {assign var=subca value=$subcat[as][su]->pk_content_category}
                                            {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                                                {assign var=subca value=$subcat[as][su]->pk_content_category}
                                                <option value="{$subcat[as][su]->pk_content_category}">
                                                    &rarr;
                                                    {$subcat[as][su]->title}
                                                    {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                                                        <span class="inactive">{t}(inactive){/t}</span>
                                                    {/if}
                                                </option>
                                            {/acl}
                                            {/section}
                                    {/section}
                                </optgroup>
                            </select>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li>
                            <select class="select2" name="filter[type_advertisement]" ng-model="shvs.search.type_advertisement" data-label="{t}Banner type{/t}">
                                {html_options options=$filter_options.type_advertisement selected=$filterType}
                            </select>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li>
                            <select class="input-medium select2" ng-model="shvs.search.content_status" data-label="{t}Status{/t}">
                                {html_options options=$filter_options.content_status selected=$filterAvailable}
                            </select>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li>
                            <select class="input-medium select2" ng-model="shvs.search.with_script" data-label="{t}Type{/t}">
                                {html_options options=$filter_options.type}
                            </select>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks hidden-xs">
                            <select class="xmedium" ng-model="pagination.epp">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                            </select>
                        </li>
                        <li class="quicklinks hidden-xs">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="criteria = {  name_like: [ { value: '', operator: 'like' } ]}; orderBy = [ { name: 'last_login', value: 'desc' } ]; pagination = { page: 1, epp: 25 }; refresh()">
                                <i class="fa fa-trash-o fa-lg"></i>
                            </button>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="refresh()">
                                <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': loading, 'fa-repeat': !loading }"></i>
                            </button>
                        </li>
                    </ul>
                    <ul class="nav quick-section pull-right">
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks form-inline pagination-links">
                            <div class="btn-group">
                                <button class="btn btn-white" ng-click="pagination.page = pagination.page - 1" ng-disabled="pagination.page - 1 < 1" type="button">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                                <button class="btn btn-white" ng-click="pagination.page = pagination.page + 1" ng-disabled="pagination.page == pagination.pages" type="button">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <div class="section-picker">
                <div class="title-picker btn">
                    <span class="text">{if $home}{t}Opinion frontpage{/t}{else}{t}Listing{/t}{/if}</span>
                    <span class="caret"></span>
                </div>
                <div class="options">
                    {acl isAllowed="OPINION_FRONTPAGE"}
                    <a href="{url name=admin_opinions_frontpage}" {if $home}class="active"{/if}>{t}Opinion frontpage{/t}</a>
                    {/acl}
                    <a href="{url name=admin_opinions}" ng-class="{ active: !shvs.search.blog }">{t}Opinion{/t}</a>
                    {is_module_activated name="BLOG_MANAGER"}
                    <a href="{url name=admin_blogs}" ng-class="{ active: shvs.search.blog }">{t}Blog{/t}</a>
                    {/is_module_activated}
                </div>
            </div>
        </div>
        <ul class="old-button">

            <li class="separator"></li>
            <li ng-if="shvs.selected.length > 0">
                <a href="#">
                    <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                    <br/>{t}Batch actions{/t}
                </a>
                <ul class="dropdown-menu" style="margin-top: 1px;">
                    {acl isAllowed="CONTENT_OTHER_UPDATE"}
                    {acl isAllowed="OPINION_AVAILABLE"}
                    <li>
                        <a href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')">
                            <i class="icon-eye-open"></i>
                            {t}Publish{/t}
                        </a>
                    </li>
                    <li>
                        <a href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')">
                            <i class="icon-eye-close"></i>
                            {t}Unpublish{/t}
                        </a>
                    </li>
                    {/acl}
                    {acl isAllowed="OPINION_HOME"}
                    <li class="divider"></li>
                    <li>
                        <a href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')">
                            <i class="go-home"></i>
                            {t escape="off"}In home{/t}
                        </a>
                    </li>
                    <li>
                        <a href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')">
                            <i class="no-home"></i>
                            {t escape="off"}Drop from home{/t}
                        </a>
                    </li>
                    {/acl}
                    {/acl}
                    {acl isAllowed="CONTENT_OTHER_DELETE"}
                    {acl isAllowed="OPINION_DELETE"}
                        <li class="divider"></li>
                        <li>
                            <a href="#" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')">
                                <i class="icon-trash"></i>
                                {t}Delete{/t}
                            </a>
                        </li>
                    {/acl}
                    {/acl}
                </ul>
            </li>
            <li class="separator" ng-if="shvs.selected.length > 0"></li>

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
