<div class="table-info clearfix">
    <div class="pull-left"><strong>[% total %] {t}items{/t}</strong></div>
    <div class="pull-right form-inline">
        {t}Type:{/t}
        <select class="input-small select2" ng-model="filters.search.blog">
            <option value="-1">-- All --</option>
            <option value="0">Opinion</option>
            <option value="1">Blog</option>
        </select>
        {t}Status:{/t}
        <select class="input-small select2" ng-model="filters.search.available">
            <option value="-1">{t}-- All --{/t}</option>
            <option value="1">{t}Published{/t}</option>
            <option value="0">{t}No published{/t}</option>
        </select>
         &nbsp;
        {t}Select an author{/t}
        <select class="select2 input-large" ng-model="filters.search.author">
            <option value="-1"> {t}All authors{/t} </option>
            <option value="-2"> {t}Director{/t} </option>
            <option value="-3"> {t}Editorial{/t} </option>
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
                <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
                <th>{t}Author name{/t} - {t}Title{/t}</th>
                <th class="center" style="width:40px"><i class="icon-eye-open" style="font-size: 130%;"></i></th>
                <th class="center" style="width:110px;">{t}Created in{/t}</th>
                <th class="center" style="width:70px;">{t}In home{/t}</th>
                <th class="center" style="width:20px;">{t}Published{/t}</th>
                <th class="center" style="width:20px;">{t}Favorite{/t}</th>
                <th class="center" style="width:70px;">{t}Actions{/t}</th>
          </tr>
        </thead>
        <tbody>
            <tr ng-if="contents.length > 0" ng-repeat="content in contents">
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
                        <button class="btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                            <i class="icon-trash icon-white"></i>
                        </button>
                        {/acl}
                    </ul>
                </td>
            </tr>
            <tr ng-if="contents.length == 0">
                <td class="empty" colspan="11">
                    {t}There is no opinions yet.{/t}
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr >
                <td colspan="11" class="center">
                    <div class="pull-left">
                        [% (page - 1) * 10 %]-[% (page * 10) < total ? page * 10 : total %] of [% total %]
                    </div>
                    <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_opinions_list')" page="page" total-items="total" num-pages="pages"></pagination>
                    <div class="pull-right">
                        [% page %] / [% pages %]
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</script>
