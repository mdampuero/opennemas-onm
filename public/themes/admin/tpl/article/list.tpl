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
<form action="{url name=admin_articles}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('article', { available: -1, category_name: -1, title: '' }, 'title', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix" >
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Articles{/t}</h2>
            </div>
            <ul class="old-button">
                <li ng-if="selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="ARTICLE_AVAILABLE"}
                        <li>
                            <a href="#" id="batch-publish" ng-click="batchToggleAvailable(1, 'backend_ws_contents_batch_toggle_available')">
                                <i class="icon-eye-open"></i>
                                {t}Publish{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish" ng-click="batchToggleAvailable(0, 'backend_ws_contents_batch_toggle_available')">
                                <i class="icon-eye-close"></i>
                                {t}Unpublish{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="ARTICLE_DELETE"}
                            <li class="divider"></li>
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
                <li class="separator" ng-if="selected.length > 0"></li>
                {acl isAllowed="ARTICLE_CREATE"}
                    <li>
                        <a href="{url name=admin_article_create category=$category}">
                            <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New article{/t}
                        </a>
                    </li>
                {/acl}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            {acl hasCategoryAccess=$category}<div class="pull-left"><strong>[% total %] {t}articles{/t}</strong></div> {/acl}
            <div class="pull-right">
                <div class="form-inline">
                    <input type="text" placeholder="{t}Search by title:{/t}" name="title" ng-model="filters.search.title"/>
                    <label for="category">{t}Category:{/t}</label>
                    <select class="input-medium select2" id="category" ng-model="filters.search.category_name">
                        <option value="-1">{t}-- All --{/t}</option>
                            {section name=as loop=$allcategorys}
                                {assign var=ca value=$allcategorys[as]->pk_content_category}
                                <option value="{$allcategorys[as]->name}">
                                    {$allcategorys[as]->title}
                                    {if $allcategorys[as]->inmenu eq 0}
                                        <span class="inactive">{t}(inactive){/t}</span>
                                    {/if}
                                </option>
                                {section name=su loop=$subcat[as]}
                                {assign var=subca value=$subcat[as][su]->pk_content_category}
                                {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                                    {assign var=subca value=$subcat[as][su]->pk_content_category}
                                    <option value="{$subcat[as][su]->name}">
                                        &rarr;
                                        {$subcat[as][su]->title}
                                        {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                                            <span class="inactive">{t}(inactive){/t}</span>
                                        {/if}
                                    </option>
                                {/acl}
                                {/section}
                            {/section}
                    </select>
                    {t}Status:{/t}
                    <select class="select2 input-medium" name="status" ng-model="filters.search.available">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>
                </div>
            </div>
        </div>
        <div ng-include="'articles'"></div>
    </div>
    <script type="text/ng-template" id="articles">
        <div ng-if="loading" style="text-align: center; padding: 40px 0px;">
            <img src="/assets/images/facebox/loading.gif" style="margin: 0 auto;">
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
                <th class="left" >{t}Title{/t}</th>
                {if $category eq 'all' || $category == 0}
                    <th class="left">{t}Section{/t}</th>
                {/if}
                <th  class="left" style="width:80px;">{t}Author{/t}</th>
                <th class="center" style="width:130px;">{t}Created{/t}</th>
                <th class="center" style="width:80px;">{t}Last Editor{/t}</th>
                <th class="center" style="width:10px;">{t}Available{/t}</th>
                <th class="center" style="width:70px;">{t}Actions{/t}</th>
            </thead>
            <tbody>
            {*acl hasCategoryAccess=$category*}
            {acl hasCategoryAccess=$article->category}
                <tr ng-if="contents.length == 0">
                    <td class="empty" colspan="10">{t}No available articles.{/t}</td>
                </tr>
                <tr ng-if="contents.length >= 0" ng-repeat="content in contents">
                    <td>
                        <input type="checkbox" class="minput" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)" value="[% content.id %]">
                    </td>
                    <td class="left" >
                        <span  rel="tooltip" data-original-title="{t}Last author: {/t}[% content.editor %]">[% content.title %]</span>
                    </td>
                    {if $category eq 'all' || $category == 0}
                    <td class="left">
                        <span ng-if="content.category_name == 'unknown'">
                            {t}Unasigned{/t}
                        </span>
                        <span ng-if="content.category_name != 'unknown'">
                            [% content.category_name %]
                        </span>
                    </td>
                    {/if}
                    <td class="left" >
                        <span ng-if="content.author != 0">
                            [% content.author.name %]
                        </span>
                        <span ng-if="content.author == 0 && content.agency != ''">
                            [% content.agency %]
                        </span>
                        <span ng-if="content.author == 0 && content.agency == ''">
                            [% content.editor %]
                        </span>
                    </td>
                    <td class="center">[% content.created %]</td>
                    <td class="center">[% content.editor %]</td>
                    <td class="center">
                        <span ng-if="content.category != 20">
                        {acl isAllowed="ARTICLE_AVAILABLE"}
                            <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.available == 1, unpublished: content.available == 0 }" ng-click="toggleAvailable(content.id, $index, 'backend_ws_content_toggle_available')" type="button"></button>
                        {/acl}
                    </td>
                    <td class="right">
                        <div class="btn-group">
                            <button class="btn" ng-click="edit(content.id, 'admin_article_show')" type="button">
                                <i class="icon-pencil"></i>
                            </button>
                            <button class="del btn btn-danger" ng-click="open('modal-delete', $index)" type="button">
                                <i class="icon-trash icon-white"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            {/acl}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="page" total-items="total" num-pages="pages"></pagination>
                    </td>
                </tr>
            </tfoot>
            {*/acl*}
        </table>
    </script>
    <script type="text/ng-template" id="modal-delete">
        {include file="article/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="article/modals/_modalBatchDelete.tpl"}
    </script>
</form>
{/block}
