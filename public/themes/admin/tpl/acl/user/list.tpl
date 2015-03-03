{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_acl_user}" method="get" id="userform" ng-app="BackendApp" ng-controller="ContentListController" ng-init="init(null, { name_like: '', fk_user_group: -1, type: -1 }, 'name', 'asc', 'backend_ws_users_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-user fa-lg"></i>
                            {t}Users{/t}
                        </h4>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-primary" href="{url name=admin_acl_user_create}">
                                <i class="fa fa-plus"></i>
                                {t}Create{/t}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section pull-left">
                    <li class="quicklinks">
                        <button class="btn btn-link" ng-click="selected.contents = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right"type="button">
                            <i class="fa fa-arrow-left fa-lg"></i>
                        </button>
                    </li>
                     <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h4>
                            [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
                        </h4>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right">
                    {acl isAllowed="USER_AVAILABLE"}
                    <li class="quicklinks">
                        <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_users_batch_set_enabled', 'activated', 0, 'loading')" tooltip="{t}Disable{/t}" tooltip-placement="bottom">
                            <i class="fa fa-times fa-lg"></i>
                        </button>
                    </li>
                    <li class="quicklinks">
                        <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_users_batch_set_enabled', 'activated', 1, 'loading')" tooltip="{t}Enable{/t}" tooltip-placement="bottom">
                            <i class="fa fa-check fa-lg"></i>
                        </button>
                    </li>
                    {/acl}
                    {acl isAllowed="ARTICLE_DELETE"}
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="open('modal-delete-selected', 'backend_ws_users_batch_delete')" tooltip="{t}Delete{/t}" tooltip-placement="bottom">
                                <i class="fa fa-trash-o fa-lg"></i>
                            </button>
                        </li>
                    {/acl}
                </ul>
            </div>
        </div>
    </div>
    <div class="page-navbar filters-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="m-r-10 input-prepend inside search-input no-boarder">
                        <span class="add-on">
                            <span class="fa fa-search fa-lg"></span>
                        </span>
                        <input class="no-boarder" name="title" ng-model="criteria.title_like" placeholder="{t}Search by title{/t}" type="text"/>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks dropdown hidden-xs">
                        <span class="btn btn-none dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                            <span class="dropdown-current">
                                <strong>{t}Category{/t}:</strong>
                                <span ng-if="criteria.category_name == -1">{t}All{/t}</span>
                                <span ng-if="criteria.category_name != -1">[% criteria.category_name %]</span>
                            </span>
                                <span class="caret"></span>
                        </span>
                        <ul class="dropdown-menu">
                            <li ng-click="criteria.category_name = -1">
                                <span class="a">{t}All{/t}</span>
                            </li>
                            {section name=as loop=$allcategorys}
                                {assign var=ca value=$allcategorys[as]->pk_content_category}
                                <li ng-click="criteria.category_name = '{$allcategorys[as]->name}'">
                                    <span class="a">
                                        {$allcategorys[as]->title}
                                        {if $allcategorys[as]->inmenu eq 0}
                                            {t}(inactive){/t}
                                        {/if}
                                    </span>
                                </li>
                                {section name=su loop=$subcat[as]}
                                {assign var=subca value=$subcat[as][su]->pk_content_category}
                                {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                                    {assign var=subca value=$subcat[as][su]->pk_content_category}
                                    <li ng-click="criteria.category_name = '{$subcat[as][su]->name}'">
                                        <span class="a">
                                            &rarr;
                                            {$subcat[as][su]->title}
                                            {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                                                {t}(inactive){/t}
                                            {/if}
                                        </span>
                                    </li>
                                {/acl}
                                {/section}
                            {/section}
                        </ul>
                    </li>
                    <li class="quicklinks dropdown hidden-xs">
                        <button class="btn btn-none dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                            <span class="dropdown-current">
                                {t}Status{/t}:
                                <span ng-if="criteria.content_status == -1">{t}All{/t}</span>
                                <span ng-if="criteria.content_status == 0">{t}Published{/t}</span>
                                <span ng-if="criteria.content_status == 1">{t}No published{/t}</span>
                            </span>
                            <span class="caret"></span>
                        </button>
                      <ul class="dropdown-menu">
                        <li ng-click="criteria.content_status = -1">
                            <span class="a">{t}All{/t}</span>
                        </li>
                        <li ng-click="criteria.content_status = 1">
                            <span class="a">{t}Published{/t}</span>
                        </li>
                        <li ng-click="criteria.content_status = 0">
                            <span class="a">{t}No Published{/t}</span>
                        </li>
                      </ul>
                    </li>
                    <li class="quicklinks dropdown hidden-xs">
                        <button class="btn btn-none dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                            <span class="dropdown-current">
                                {t}Author{/t}: [% shvs.extra.authors[content.fk_author].name %]
                                <span ng-if="criteria.fk_author == -1">{t}All{/t}</span>
                                <span ng-if="criteria.fk_author != -1">[% criteria.category_name %]</span>
                            </span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li ng-click="criteria.fk_author = -1">
                                <span class="a">{t}All{/t}</span>
                            </li>
                            {foreach $authors as $author}
                                <li ng-click="criteria.fk_author = {$author->id}">
                                    <span class="a">{$author->name}</span>
                                </li>
                            {/foreach}
                        </ul>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <select class="select2 input-medium" name="status" ng-model="criteria.elements_per_page" data-label="{t}View{/t}">
                            <option value="10a">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right simple-pagination ng-cloak">
                    <li class="quicklinks hidden-xs">
                        <span class="info">
                        [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
                        </span>
                    </li>
                    <li class="quicklinks form-inline pagination-links">
                        <div class="btn-group">
                            <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content">
        {render_messages}
        <div class="grid simple">
            <div class="grid-body no-padding">
                <div class="spinner-wrapper" ng-if="loading">
                    <div class="loading-spinner"></div>
                    <div class="spinner-text">{t}Loading{/t}...</div>
                </div>
                <div class="ng-cloak" ng-if="contents.length == 0">
                  <h5>{t escape=off}There is no user created yet or <br/>not results for your searching criteria.{/t}</h5>
                </div>
                <div class="table-wrapper ng-cloak" ng-if="!loading">
                    <table class="table table-hover no-margin" ng-if="contents.length > 0">
                        <thead>
                            <tr>
                                <th class="checkbox-cell">
                                    <div class="checkbox checkbox-default">
                                        <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                        <label for="select-all"></label>
                                    </div>
                                </th>
                                <th class=" hidden-xs"></th>
                                <th class="left">{t}Full name{/t}</th>
                                <th class="center nowrap hidden-xs" style="width:110px">{t}Username{/t}</th>
                                <th class="center hidden-xs hidden-sm" >{t}E-mail{/t}</th>
                                <th class="center hidden-xs" >{t}Group{/t}</th>
                                <th class="center hidden-xs" >{t}Activated{/t}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-if="contents.length > 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                                <td class="checkbox-cell">
                                    <div class="checkbox check-default">
                                        <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                                        <label for="checkbox[%$index%]"></label>
                                    </div>
                                </td>
                                <td class="hidden-xs">
                                    <dynamic-image instance="{$smarty.const.INSTANCE_MEDIA}" path="[% shvs.extra.photos[content.avatar_img_id].path_file + '/' + shvs.extra.photos[content.avatar_img_id].name %]" transform="thumbnail,40,40" ng-if="content.avatar_img_id != 0"></dynamic-image>

                                    <gravatar email="[% content.email %]" image_dir="$params.IMAGE_DIR" image=true size="40" ng-if="content.avatar_img_id == 0"></gravatar>
                                </td>
                                <td class="left">
                                    <strong>[% content.name %] </strong>
                                    <span class="visible-xs visible-sm">([% content.email %])</span>

                                    <div class="visible-xs">[% content.username %]</div>

                                    <span ng-repeat="group in content.id_user_group" class="visible-xs">{t}Group{/t}: [% extra.groups[group].name %][% $last ? '' : ', ' %]</span>

                                    <div class="listing-inline-actions">
                                        <a class="link" href="[% edit(content.id, 'admin_acl_user_show') %]" title="{t}Edit user{/t}">
                                            <i class="fa fa-pencil"></i> {t}Edit{/t}
                                        </a>
                                        <button class="link link-danger" ng-click="open('modal-delete', 'backend_ws_user_delete', $index)" type="button">
                                            <i class="fa fa-trash-o"></i>
                                            {t}Delete{/t}
                                        </button>
                                    </div>
                                </td>
                                <td class="center nowrap hidden-xs">
                                    [% content.username %]
                                </td>

                                <td class="center hidden-xs hidden-sm">
                                    [% content.email %]
                                </td>
                                <td class="center hidden-xs">
                                    <span ng-repeat="group in content.id_user_group">
                                      [% extra.groups[group].name %][% $last ? '' : ', ' %]
                                    </span>
                                </td>
                                <td class="right">
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_user_set_enabled', 'activated', content.activated != 1 ? 1 : 0, 'loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && content.activated == '1', 'fa-times text-error': !content.loading && content.activated == '0' }"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-footer clearfix ng-cloak" ng-if="!loading">
                <div class="pagination-info pull-left" ng-if="contents.length > 0">
                    {t}Showing{/t} [% ((pagination.page - 1) > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% pagination.page * pagination.epp %] {t}of{/t} [% pagination.total %]
                </div>
                <div class="pull-right pagination-wrapper" ng-if="contents.length > 0">
                    <pagination class="no-margin" max-size="5" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
                </div>
            </div>
        </div>
    </div>

	<script type="text/ng-template" id="users">

	</script>
	<script type="text/ng-template" id="modal-delete">
		{include file="common/modals/_modalDelete.tpl"}
	</script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
</form>
{/block}
