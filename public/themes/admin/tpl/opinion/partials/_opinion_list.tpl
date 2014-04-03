<div class="table-info clearfix">
    <div class="pull-left form-inline">
        <strong>{t}FILTER:{/t}</strong>
        &nbsp;&nbsp;
        <input type="text" autofocus placeholder="{t}Search by title{/t}" name="title" ng-model="shvs.search.title_like"/>
        &nbsp;&nbsp;
        <select class="select2" ng-model="shvs.search.blog" data-label="{t}Type{/t}">
            <option value="-1">-- All --</option>
            <option value="0">Opinion</option>
            <option value="1">Blog</option>
        </select>
        &nbsp;&nbsp;
        <select class="select2" ng-model="shvs.search.available" data-label="{t}Status{/t}">
            <option value="-1">{t}-- All --{/t}</option>
            <option value="1">{t}Published{/t}</option>
            <option value="0">{t}No published{/t}</option>
        </select>
        &nbsp;&nbsp;
        <select class="select2" ng-model="shvs.search.author" data-label="{t}Author{/t}">
            <option value="-1">{t}-- All authors --{/t}</option>
            <option value="-2">{t}Director{/t}</option>
            <option value="-3">{t}Editorial{/t}</option>
            {section name=as loop=$autores}
                <option value="{$autores[as]->id}" {if isset($author) && $author == $autores[as]->id} selected {/if}>{$autores[as]->name} {if $autores[as]->meta['is_blog'] eq 1} (Blogger) {/if}</option>
            {/section}
        </select>
    </div>
</div>
<div ng-include="'opinions'"></div>
<script type="text/ng-template" id="opinions">
    <div class="spinner-wrapper" ng-if="loading">
        <div class="spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
    </div>
    <table class="table table-hover table-condensed" ng-if="!loading">
        <thead>
            <tr>
                <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                <th>{t}Author name{/t} - {t}Title{/t}</th>
                <th class="center">{t}Created on{/t}</th>
                <th class="center" style="width:40px"><i class="icon-eye-open" style="font-size: 130%;"></i></th>
                <th class="center" style="width:70px;">{t}In home{/t}</th>
                <th class="center" style="width:20px;">{t}Published{/t}</th>
                <th class="center" style="width:20px;">{t}Favorite{/t}</th>
                <th class="center" style="width:10px;"></th>
          </tr>
        </thead>
        <tbody>
            <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected($index) }">
                <td>
                    <checkbox index="[% $index %]">
                </td>
                <td>
                    <strong>
                        <span ng-if="content.author.name">
                            [% content.author.name %]
                        </span>
                        <span ng-if="!content.author.name">
                            [% content.author %]
                        </span>
                    </strong>
                    -
                    [% content.title %]
                </td>
                <td class="center nowrap">
                        [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                    </td>
                <td class="center">
                    [% content.views %]
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
                        <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="setContentStatus($index, 'backend_ws_content_set_content_status', content.content_status != 1 ? 1 : 0)" type="button"></button>
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
                        <a class="btn" href="[% edit(content.id, 'admin_opinion_show') %]">
                            <i class="icon-pencil"></i>
                        </a>
                        {/acl}
                        {acl isAllowed="OPINION_DELETE"}
                        <button class="btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                            <i class="icon-trash icon-white"></i>
                        </button>
                        {/acl}
                    </ul>
                </td>
            </tr>
            <tr ng-if="shvs.contents.length == 0">
                <td class="empty" colspan="11">
                    {t}There is no opinions yet.{/t}
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11" class="center">
                    <div class="pull-left" ng-if="shvs.contents.length > 0">
                        {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                    </div>
                    <div class="pull-right" ng-if="shvs.contents.length > 0">
                        <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_opinions_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                    </div>
                    <span ng-if="shvs.contents.length == 0">&nbsp;</span>
                </td>
            </tr>
        </tfoot>
    </table>
</script>
