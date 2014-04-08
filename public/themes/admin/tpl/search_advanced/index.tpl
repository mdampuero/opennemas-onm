{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
#search_string{
    width:195px;
    margin-bottom:5px;
}
#search-form {
    height:100%
}
span.highlighted {
    color:Red
}
</style>
{/block}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Global search{/t}</h2></div>
    </div>
</div>
<div class="wrapper-content">
    <form action="{url name=admin_search}" method="GET" ng-app="BackendApp" ng-controller="ContentCtrl" ng-controller="ContentCtrl" ng-init="init('content', { }, 'created', 'desc', 'backend_ws_contents_list')">
        <div class="wrapper-content">
            <div class="table-info clearfix">
                <div class="pull-left">
                    <div class="form-inline">
                        <strong>{t}FILTER:{/t}</strong>
                        &nbsp;&nbsp;
                        <input type="text" autofocus placeholder="{t}Search by title:{/t}" name="title" ng-model="shvs.search.title_like"/>
                        &nbsp;&nbsp;
                        <strong>{t}CONTENT TYPE:{/t}</strong>
                        <select class="select2 input-large" name="content_types[]" id="content_types" multiple ng-model="shvs.search.content_type_name">
                            {html_options options=$content_types selected=$content_types_selected}
                        </select>
                    </div>
                </div>
            </div>
            <div id="search-results">
                <div ng-include="'results'"></div>
            </div><!-- /search-results -->
        </div>
        <script type="text/ng-template" id="results">
            {include file="search_advanced/partials/_list.tpl"}
        </script>
    </form>
</div>
{/block}
