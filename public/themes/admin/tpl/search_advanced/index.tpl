{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_search}" method="GET" ng-app="BackendApp" ng-controller="ContentListController" ng-controller="ContentListController" ng-init="init('content', { content_type_name: -1 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-search"></i>
                        {t}Global search{/t}
                    </h4>
                </li>
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
                    <input class="no-boarder" type="text" name="name" ng-model="criteria.title_like" placeholder="{t}Filter by title{/t}" />
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <select name="content_types[]" id="content_types" ng-model="criteria.content_type_name" class="select2" data-label="{t}Type{/t}"> <!-- multiple -->
                        {html_options options=$content_types selected=$content_types_selected}
                    </select>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                </li>
                <li class="pull-right">
                    <span class="info">
                    {t}Results{/t}: {$pagination->_totalItems} {t}users{/t}
                    </span>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks">
                    <span class="h-seperate"></span>
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

    <div class="grid simple">
        <div class="grid-body no-padding">
            <div id="search-results">
                <div ng-include="'results'"></div>
            </div><!-- /search-results -->

            <script type="text/ng-template" id="results">
                {include file="search_advanced/partials/_list.tpl"}
            </script>
        </div>
    </div>
</div>

</form>
{/block}
