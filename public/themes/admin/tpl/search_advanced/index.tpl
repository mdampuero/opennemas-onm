{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
#search-form button {
    width:100%;
}
#search_string{
    width:195px;
    margin-bottom:5px;
}
#content_types {
    min-height:150px;
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
    {script_tag src="router.js" language="javascript" bundle="fosjsrouting" basepath="js"}
    {script_tag src="routes.js" language="javascript" common=1 basepath="js"}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
    {script_tag src="angular.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="ui-bootstrap-tpls-0.10.0.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="app.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="services.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="controllers.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="directives.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="content-modal.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="content.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="fos-js-routing.js" language="javascript" bundle="backend" basepath="js/services"}
    {script_tag src="shared-vars.js" language="javascript" bundle="backend" basepath="js/services"}
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
                        <input type="text" placeholder="{t}Search by title:{/t}" name="title" ng-model="shvs.search.title_like"/>
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
