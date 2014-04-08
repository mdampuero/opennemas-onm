{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="{url name=admin_acl_user}" method="get" id="userform" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init(null, { name_like: '', fk_user_group: -1, type: -1 }, 'name', 'asc', 'backend_ws_users_list')">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Users{/t}</h2></div>
			<ul class="old-button">
				<li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="USER_AVAILABLE"}
                        <li>
                            <a href="#" id="batch-publish" ng-click="updateSelectedItems('backend_ws_users_batch_set_enabled', 'activated', 1, 'loading')">
                                <i class="icon-ok"></i>
                                {t}Enable{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish" ng-click="updateSelectedItems('backend_ws_users_batch_set_enabled', 'activated', 0, 'loading')">
                                <i class="icon-remove"></i>
                                {t}Disable{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="ARTICLE_DELETE"}
                            <li class="divider"></li>
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_users_batch_delete')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
				<li class="separator" ng-if="shvs.selected > 0"></li>
				<li>
					<a href="{url name=admin_acl_user_create}" title="{t}Create new user{/t}">
						<img src="{$params.IMAGE_DIR}user_add.png" alt="Nuevo"><br />{t}New user{/t}
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="wrapper-content">
		{render_messages}
 		<div ng-include="'users'"></div>
	</div>
	<script type="text/ng-template" id="users">
		<div class="table-info clearfix">
			<div class="pull-left form-inline">
				<strong>{t}FILTER:{/t}</strong>
				&nbsp;&nbsp;
				<input type="text" id="username" name="name" value="{$smarty.request.name|default:""}" placeholder="{t}Filter by name or email{/t}" ng-model="shvs.search.name_like"/>
				&nbsp;&nbsp;
				<select id="usertype" name="type" class="select2" ng-model="shvs.search.type" data-label="{t}Type{/t}">
					{assign var=type value=$smarty.request.type}
					<option value="-1">{t}--All--{/t}</option>
                    <option value="0">{t}Backend{/t}</option>
                    <option value="1">{t}Frontend{/t}</option>
				</select>
				&nbsp;&nbsp;
				<select id="usergroup" name="group" class="select2" ng-model="shvs.search.fk_user_group" data-label="{t}Group{/t}">
					<option value="-1">{t}--All--{/t}</option>
					{html_options options=$groupsOptions selected=$smarty.request.group|default:""}
				</select>
			</div>
		</div>
		<div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
			<thead>
				<tr>
					<th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th></th>
					<th class="left">{t}Full name{/t}</th>
					<th class="center nowrap" style="width:110px">{t}Username{/t}</th>
					<th class="center" >{t}E-mail{/t}</th>
					<th class="center" >{t}Group{/t}</th>
					<th class="center" >{t}Activated{/t}</th>
					<th class="center" style="width:10px"></th>
				</tr>
			</thead>
			<tbody>
                <tr ng-if="shvs.contents.length == 0">
                    <td colspan="8" class="empty">
                        {t escape=off}There is no user created yet or <br/>not results for your searching criteria.{/t}
                    </td>
                </tr>
				<tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
					<td>
                        <checkbox index="[% content.id %]">
                    </td>
					<td>
                        <dynamic-image instance="{$smarty.const.INSTANCE_MEDIA}" path="[% shvs.extra.photos[content.avatar_img_id].path_file + '/' + shvs.extra.photos[content.avatar_img_id].name %]" transform="thumbnail,40,40" ng-if="content.avatar_img_id != 0"></dynamic-image>

                        <gravatar email="[% content.email %]" image_dir="$params.IMAGE_DIR" image=true size="40" ng-if="content.avatar_img_id == 0"></gravatar>
					</td>
					<td class="left">
						<strong>[% content.name %]</strong>
					</td>
					<td class="center nowrap">
						[% content.username %]
					</td>

					<td class="center">
						[% content.email %]
					</td>
					<td class="center">
                        <span ng-repeat="group in content.id_user_group">[% shvs.extra.groups[group].name %][% $last ? '' : ', ' %]</span>
					</td>

					<td class="center">
						<div class="btn-group">
							<button class="btn-link" ng-click="updateItem($index, content.id, 'backend_ws_user_set_enabled', 'activated', content.activated != 1 ? 1 : 0, 'loading')" type="button" ng-class="{ 'loading': content.loading == 1 }">
							<i class="icon16" ng-class="{ 'icon-ok': content.loading != 1 && content.activated == 1, 'icon-remove': content.loading != 1 && content.activated == 0 }"></i>
						</button>
						</div>
					</td>
					<td class="right nowrap">
						<div class="btn-group">
							<a class="btn" href="[% edit(content.id, 'admin_acl_user_show') %]" title="{t}Edit user{/t}">
								<i class="icon-pencil"></i> {t}Edit{/t}
							</a>

							<button class="btn btn-danger" ng-click="open('modal-delete', 'backend_ws_user_delete', $index)"
								title="{t}Delete this user{/t}" type="button">
								<i class="icon-trash icon-white"></i>
							</button>
						</div>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr >
					<td colspan="8" class="center">
		                <div class="pull-left" ng-if="shvs.contents.length > 0">
                        	{t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                        </div>
                        <div class="pull-right" ng-if="shvs.contents.length > 0">
                            <pagination max-size="0" direction-links="true" on-select-page="selectPage(page, 'backend_ws_users_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                        </div>
                        <span ng-if="shvs.contents.length == 0">&nbsp;</span>
					</td>
				</tr>
			</tfoot>
		</table>
	</script>
	<script type="text/ng-template" id="modal-delete">
		{include file="common/modals/_modalDelete.tpl"}
	</script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
</form>
{/block}
