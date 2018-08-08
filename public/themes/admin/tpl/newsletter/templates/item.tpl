{extends file="base/admin.tpl"}

{block name="content"}
<script>
var newsletterTemplateTranslations = {
  contenidosRequerido: '{t}Some content is required{/t}'
};
</script>
<form name="form" ng-controller="NewsletterTemplateCtrl" ng-init="getItem({$id});">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-envelope m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_newsletters_list}" title="{t}Go back to list{/t}">
                {t}Newsletters{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <h4>{t}Template{/t}</h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks btn-group">
              <button class="btn btn-loading btn-primary text-uppercase" ng-click="saveVal()" ng-disabled="flags.http.saving || form.$invalid || (item.password && item.password !== rpassword)" type="button">
                <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                {t}Save{/t}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content newsletter-manager">

    <div class="listing-no-contents" ng-hide="!flags.loading">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
        <h3 class="spinner-text">{t}Loading{/t}...</h3>
      </div>
    </div>

    <div class="grid simple ng-cloak">
      <div class="grid-title">
        <i class="fa fa-envelope-o m-r-10"></i>{t}Name{/t}
      </div>

      <div class="grid-body">
        <input type="text" class="form-control" name="title" id="title" ng-model="item.name"/>
      </div>
    </div>

    <div class="grid simple ng-cloak" ng-if="!flags.loading">
      <div class="grid-body m-b-10">
        <div class="form-group no-margin">
          <div class="checkbox">
            <input id="status" name="status" ng-false-value="0" ng-model="item.status" ng-true-value="1" type="checkbox">
            <label class="form-label" for="status">
              {t}Enabled{/t}
            </label>
          </div>

          <input name="type" ng-model="item.type" type="hidden" value=1>
        </div>
      </div>
    </div>

    <div class="row ng-cloak">
      <div class="col-xs-12 col-sm-6">
        <div class="grid simple">
          <div class="grid-title">
            <h5><i class="fa fa-calendar m-r-10"></i>{t}Schedule{/t}</h5>
          </div>

          <div class="grid-body">
            <div class="form-group days col-xs-12">
              <h5>{t}Days{/t}</h5>

              <div class="form-group">
                <div class="m-t-15 m-b-10" ng-repeat="day in data.extra.days">
                  <div class="checkbox col-xs-6 p-b-10">
                    <input id="checkbox-days-[% $index %]" checklist-model="item.schedule.days" checklist-value="$index + 1" type="checkbox">
                    <label for="checkbox-days-[% $index %]">
                      <strong>[% day %]</strong>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group hours col-xs-12">
              <h5>{t}Hours{/t} <small class="pull-right">({t}Time zone: {/t} {date_default_timezone_get()})</small></h5>

              <tags-input ng-model="item.schedule.hours" minTags=1 add-on-paste="true" add-from-autocomplete-only="true" placeholder="{t}Add an hour{/t}">
                <auto-complete source="loadHours($query)" load-on-focus=true min-length="0" debounce-delay="0"></auto-complete>
              </tags-input>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6">
        <div class="grid simple">
          <div class="grid-title">
            <h5><i class="fa fa-users m-r-10"></i>{t}Recipients{/t}</h5>
          </div>

          <div class="grid-body">
            <div class="external" ng-if="data.extra.newsletter_handler == 'submit'">
              <h5><i class="fa fa-external-link m-r-10"></i>{t}External service{/t}</h5>

              <div class="form-group">
                <div class="m-t-15 m-b-10">
                  <div class="checkbox" ng-repeat="recipient in data.extra.recipients|filter:{ type: 'external' }">
                    <input id="checkbox-external-[% $index %]" checklist-model="item.recipients" checklist-value="recipient" type="checkbox" load-on-empty="true">
                    <label for="checkbox-external-[% $index %]">
                      [% recipient.email %]
                    </label>
                  </div>
                </div>
              </div>
            </div>

            {is_module_activated name="es.openhost.module.acton"}
            <div class="acton" ng-if="data.extra.newsletter_handler == 'acton'">
              <h5><i class="fa fa-address-book m-r-10"></i>{t}Act-On marketing lists{/t}</h5>
              <div class="form-group">
                <div class="m-t-15 m-b-10" ng-repeat="recipient in data.extra.recipients|filter:{ type: 'acton' }">
                  <div class="checkbox">
                    <input id="checkbox-acton-[% $index %]" checklist-model="item.recipients" checklist-value="recipient" type="checkbox" load-on-empty="true">
                    <label for="checkbox-acton-[% $index %]">
                      <strong>[% recipient.name %]</strong>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            {/is_module_activated}

            <div class="internal" ng-if="data.extra.newsletter_handler == 'create_subscriptor'">
              <h5><i class="fa fa-address-book m-r-10"></i>{t}Subscription lists{/t}</h5>
              <div class="form-group">
                <div class="m-t-15 m-b-10" ng-repeat="recipient in data.extra.recipients|filter:{ type: 'list' }">
                  <div class="checkbox">
                    <input id="checkbox-lists-[% $index %]" checklist-model="item.recipients" checklist-value="recipient" type="checkbox">
                    <label for="checkbox-lists-[% $index %]">
                      <strong>[% recipient.name %]</strong> - {t 1="[% recipient.subscribers %]"}%1 subscriptors{/t}
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {is_module_activated name="es.openhost.module.acton"}
        <div class="grid simple" ng-if="data.extra.newsletter_handler == 'acton'" >
          <div class="grid-title">
            <h5><i class="fa fa-address-book m-r-10"></i> {t}Act-On header and footers{/t}</h5>
          </div>
          <div class="grid-body">
              <div class="form-group col-sm-6">
                <label for="acton_header_id" class="form-label">{t}Act-On header id{/t}</label>
                <div class="controls">
                  <input id="acton_header_id" class="form-control" type="text" ng-model="item.params.acton_headerid">
                </div>
              </div>
              <div class="form-group col-sm-6">
                <label for="acton_footer_id" class="form-label">{t}Act-On footer id{/t}</label>
                <div class="controls">
                  <input id="acton_footer_id" class="form-control" type="text" ng-model="item.params.acton_footerid">
                </div>
              </div>
          </div>
        </div>
        {/is_module_activated}
      </div>
    </div>

    <div class="grid simple ng-cloak">
      <div class="grid-title">
        <i class="fa fa-envelope-o m-r-10"></i>{t}Subject{/t}
      </div>

      <div class="grid-body">
        <input type="text" class="form-control" name="title" id="title" ng-model="item.title"/>
      </div>
    </div>

    <div class="grid simple ng-cloak newsletter-contents" ng-if="!flags.loading">
      <div class="grid-title clearfix">
        <h5 class="pull-left">{t}Contents{/t}</h5>
        <div class="pull-right">
            <button type="button" class="btn" ng-click="addContainer()">
              <span class="fa fa-plus"></span> {t}Add Container{/t}
            </button>
        </div>
      </div>
      <div class="grid-body">
        <div ui-tree id="newsletter-contents">
          <ol ui-tree-nodes ng-model="item.contents" type="container">
            <li class="newsletter-container" ui-tree-node ng-repeat="container in item.contents">
              <div class="newsletter-container-title clearfix" ui-tree-handle>
                <input ng-model="container.title" type="text" data-nodrag class="form-control title pull-left" placeholder="{t}Block title{/t}">
                <div class="container-actions pull-right">
                  <button class="btn btn-white" data-nodrag ng-click="removeContainer(container)" type="button">
                    <i class="fa fa-trash-o text-danger"></i>
                  </button>
                </div>
              </div>
              <div class="newsletter-container-contents clearfix" ng-if="!container.hide" ui-tree-handle>
                <ol ui-tree-nodes="" ng-model="container.items" type="content">
                  <li ng-repeat="content in container.items" ui-tree-node ng-include="'item'">
                  </li>
                </ol>
                <div class="add-contents p-b-15" data-nodrag>
                  <h5 class="text-center">{t}Add contents{/t}</h5>
                  <div class="row">
                    <div class="col-xs-4 col-sm-offset-2">
                      <a ng-click="addDynamicContent(container)" class="btn btn-primary btn-block">
                        <i class="fa fa-bolt"></i>
                        {t}Add dynamic contents{/t}
                      </a>
                    </div>
                    <div class="col-xs-4">
                      <button type="button" class="btn btn-primary btn-block" data-nodrag content-picker content-picker-section="newsletter" content-picker-selection="true" content-picker-max-size="50" content-picker-target="container.items" content-picker-type="album,article,attachment,opinion,poll,video,special">
                        <i class="fa fa-hand-o-up"></i>
                        {t}Pick contents{/t}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </li>

          </ol>
        </div>

      </div>
    </div>
  </div>
  <script type="text/ng-template" id="item">
  <div class="newsletter-item clearfix" ui-tree-handle>
    <span></span>
    <div ng-show="content.content_type !== 'list'">
      <span>[% content.content_type_l10n_name %]</span>
      <span class="h-seperate" data-nodrag></span>
      <span class="item-title" data-nodrag>[% content.title %]</span>
    </div>
    <div ng-show="content.content_type === 'list'" class="item-list">
      <span class="item-list-title">{t}List of contents{/t}</span>
      <span class="h-seperate" data-nodrag></span>
      <span class="item-title" data-nodrag>
        <div class="criteria clearfix">
          <span class="item-list-icon fa fa-filter"></span>

          <ui-select name="content_type" theme="select2" ng-model="content.criteria.content_type">
            <ui-select-match>
              <strong>{t}Type{/t}: </strong> [% $select.selected.title %]
            </ui-select-match>
            <ui-select-choices repeat="item.value as item in data.extra.content_types | filter: { title: $select.search }">
              <div ng-bind-html="item.title | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>

          <ui-select name="category" theme="select2" ng-model="content.criteria.category">
            <ui-select-match>
              <strong>{t}Category{/t}: </strong> [% $select.selected.title %]
            </ui-select-match>
            <ui-select-choices group-by="groupCategories" repeat="item.pk_content_category as item in data.extra.categories | filter: { title: $select.search }">
              <div ng-bind-html="item.title | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </div>

        <div class="limit clearfix">
          <span class="item-list-icon fa fa-sort-amount-asc"></span>
          <ui-select name="view" theme="select2" ng-model="content.criteria.filter">
            <ui-select-match>
              <strong>{t}Filter{/t}: </strong> [% $select.selected.title %]
            </ui-select-match>
            <ui-select-choices repeat="item.value as item in data.extra.filters | filter: { title: $select.search }">
              <div ng-bind-html="item.title | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
          <ui-select name="view" theme="select2" ng-model="content.criteria.epp">
          <ui-select-match>
            <strong>{t}Amount{/t}: </strong> [% $select.selected %]
          </ui-select-match>
          <ui-select-choices repeat="item in numberOfElements  | filter: $select.search">
            <div ng-bind-html="item | highlight: $select.search"></div>
          </ui-select-choices>
        </ui-select>

        </div>
      </span>
    </div>
    <button class="btn btn-white pull-right" data-nodrag ng-click="removeContent(container, content)" type="button">
      <i class="fa fa-trash-o text-danger"></i>
    </button>
  </div>
  </script>
</form>
{/block}
