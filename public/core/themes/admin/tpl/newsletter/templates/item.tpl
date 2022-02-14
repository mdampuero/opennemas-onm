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
        <div class="all-actions pull-right" ng-if="!flags.http.loading && item">
          <ul class="nav quick-section">
            <li class="quicklinks btn-group">
              <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.saving || form.$invalid" type="button">
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
        <i class="fa fa-envelope-o m-r-10"></i>
        <h4>{t}Subject{/t}</h4>
      </div>

      <div class="grid-body">
        <input type="text" class="form-control" name="title" id="title" ng-model="item.title"/>
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
            <i class="fa fa-calendar m-r-10"></i>
            <h4>{t}Schedule{/t}</h4>
          </div>

          <div class="grid-body">
            <div class="form-group days col-xs-12">
              <h5>{t}Days{/t}</h5>

              <div class="form-group">
                <div class="m-t-15 m-b-10" ng-repeat="day in data.extra.days">
                  <div class="checkbox col-xs-6 p-b-10">
                    <input id="checkbox-days-[% $index %]" checklist-model="item.schedule.days" checklist-value="$index + 1" type="checkbox">
                    <label for="checkbox-days-[% $index %]">
                      [% day %]
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
            <i class="fa fa-users m-r-10"></i>
            <h4>{t}Recipients{/t}</h4>
          </div>

          <div class="grid-body">
            <div class="external" ng-if="data.extra.newsletter_handler == 'submit'">
              <h5><i class="fa fa-external-link m-r-10"></i>{t}External service{/t}</h5>

              <div class="form-group">
                <div class="m-t-15 m-b-10">
                  <div class="checkbox" ng-repeat="recipient in data.extra.recipients | filter: { type: 'external' }">
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
                <div class="m-t-15 m-b-10" ng-repeat="recipient in data.extra.recipients | filter: { type: 'acton' }">
                  <div class="checkbox">
                    <input id="checkbox-acton-[% $index %]" checklist-model="item.recipients" checklist-value="recipient" type="checkbox" load-on-empty="true">
                    <label for="checkbox-acton-[% $index %]">
                      [% recipient.name %]
                    </label>
                  </div>
                </div>
              </div>
            </div>
            {/is_module_activated}

            <div class="internal" ng-if="data.extra.newsletter_handler == 'create_subscriptor'">
              <h5><i class="fa fa-address-book m-r-10"></i>{t}Subscription lists{/t}</h5>
              <div class="form-group">
                <div class="m-t-15 m-b-10" ng-repeat="recipient in data.extra.recipients | filter: { type: 'list' }">
                  <div class="checkbox">
                    <input id="checkbox-lists-[% $index %]" checklist-model="item.recipients" checklist-value="recipient" type="checkbox">
                    <label for="checkbox-lists-[% $index %]">
                      <strong>[% recipient.name %]</strong>
                      <span class="text-lowercase">
                        - [% data.extra.users[recipient.id] || 0 %] {t}Subscribers{/t}
                      </span>
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

    <div class="grid simple ng-cloak newsletter-contents" ng-if="!flags.loading">
      <div class="grid-title">
        <h4>
          {t}Contents{/t}
        </h4>
      </div>
      <div class="grid-body">
        <div ui-tree="treeOptions">
          <div ng-model="item.contents" type="container" ui-tree-nodes="">
            <div class="newsletter-container" ng-repeat="container in item.contents" ui-tree-node>
              <span ui-tree-handle>
                <span class="angular-ui-tree-icon"></span>
              </span>
              <div class="newsletter-container-title">
                <div class="row">
                  <div class="col-sm-6 col-lg-4 m-t-15">
                    <input class="form-control" ng-model="container.title" type="text">
                  </div>
                  <div class="col-sm-6 col-lg-8 m-b-10 m-t-15 text-right">
                    <button class="btn btn-default m-b-5" ng-click="markContainer($index)" content-picker content-picker-ignore="[% getItemIds(container.items) %]" content-picker-section="newsletter" content-picker-selection="true" content-picker-max-size="50" content-picker-target="target" content-picker-type="album,article,attachment,event,opinion,poll,video,special" type="button">
                      <i class="fa fa-plus m-r-5"></i>
                      {t}Add{/t}
                    </button>
                    <button class="btn btn-default m-b-5" ng-click="addSearch($index)">
                      <i class="fa fa-search m-r-5"></i>
                      {t}Search{/t}
                    </button>
                    <button class="btn btn-danger m-b-5 " ng-click="removeContainer($index)" type="button">
                      <i class="fa fa-trash-o m-r-5"></i>
                      {t}Delete{/t}
                    </button>
                    <button class="btn btn-white m-b-5 " ng-click="emptyContainer($index)" type="button">
                      <i class="fa fa-fire m-r-5"></i>
                      {t}Empty{/t}
                    </button>
                  </div>
                </div>
              </div>
              <div class="newsletter-container-items" ui-tree="treeOptions">
                <div class="newsletter-container-items-placeholder" ng-if="container.items.length == 0">
                  {t}Click on "Add" or drop contents from other containers{/t}
                </div>
                <div ng-model="container.items" type="content" ui-tree-nodes="">
                  <div class="newsletter-item" ng-repeat="content in container.items" ui-tree-node>
                    <span ui-tree-handle>
                      <span class="angular-ui-tree-icon"></span>
                    </span>
                    <span class="newsletter-item-type" ng-if="content.content_type !== 'list'">
                      <span class="fa" ng-class="{ 'fa-camera': content.content_type == 'album', 'fa-file-text-o': content.content_type == 'article', 'fa-paperclip': content.content_type == 'attachment', 'fa-calendar': content.content_type == 'event' 'fa-envelope': content.content_type == 'letter', 'fa-quote-right': content.content_type == 'opinion', 'fa-pie-chart': content.content_type == 'poll', 'fa-file': content.content_type == 'static_page', 'fa-film': content.content_type == 'video', }" tooltip-placement="right" uib-tooltip="[% content.content_type_l10n_name %]"></span>
                    </span>
                    <span class="newsletter-item-type" ng-if="content.content_type === 'list'">
                      <span class="fa fa-search" tooltip-placement="right" uib-tooltip="{t}List of contents{/t}"></span>
                    </span>
                    <div class="newsletter-item-title" ng-show="content.content_type !== 'list'">
                      [% content.title %]
                    </div>
                    <div class="newsletter-item-search" ng-show="content.content_type === 'list'">
                      <ui-select name="content_type" theme="select2" ng-model="content.criteria.content_type">
                        <ui-select-match>
                          <strong>{t}Type{/t}: </strong> [% $select.selected.title %]
                        </ui-select-match>
                        <ui-select-choices repeat="item.value as item in data.extra.content_types | filter: { title: $select.search }" position='up'>
                          <div ng-bind-html="item.title | highlight: $select.search"></div>
                        </ui-select-choices>
                      </ui-select>
                      <ui-select name="opinion_type" theme="select2" ng-model="content.criteria.opinion_type" ng-if="content.criteria.content_type === 'opinion'">
                        <ui-select-match>
                          <strong>{t}Opinion type{/t}: </strong> [% $select.selected.title %]
                        </ui-select-match>
                        <ui-select-choices repeat="item.value as item in data.extra.opinion_types | filter: { title: $select.search }" position='up'>
                          <div ng-bind-html="item.title | highlight: $select.search"></div>
                        </ui-select-choices>
                      </ui-select>
                      <onm-category-selector ng-if="!['opinion', 'letter', 'static_page'].includes(content.criteria.content_type)" default-value-text="{t}All{/t}/{t}None{/t}" label-text="{t}Categories{/t}" locale="config.locale.selected" multiple="true" ng-model="content.criteria.category" placeholder="{t}Any{/t}" position="up" selected-text="{t}selected{/t}"></onm-category-selector>
                      <ui-select name="view" theme="select2" ng-model="content.criteria.filter">
                        <ui-select-match>
                          <strong>{t}Filter{/t}: </strong> [% $select.selected.title %]
                        </ui-select-match>
                        <ui-select-choices repeat="item.value as item in data.extra.filters | filter: { title: $select.search }" position='up'>
                          <div ng-bind-html="item.title | highlight: $select.search"></div>
                        </ui-select-choices>
                      </ui-select>
                      <ui-select name="view" theme="select2" ng-model="content.criteria.epp">
                        <ui-select-match>
                          <strong>{t}Amount{/t}: </strong> [% $select.selected %]
                        </ui-select-match>
                        <ui-select-choices repeat="item in numberOfElements  | filter: $select.search" position='up'>
                          <div ng-bind-html="item | highlight: $select.search"></div>
                        </ui-select-choices>
                      </ui-select>
                    </div>
                    <button class="btn btn-danger" ng-click="removeContent(container, $index)" type="button">
                      <i class="fa fa-trash-o"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button type="button" class="btn btn-default" ng-click="addContainer()">
            <i class="fa fa-plus m-r-5"></i>
            {t}Add{/t}
          </button>
          <button class="btn btn-danger" ng-click="removeContainer()" type="button">
            <i class="fa fa-trash-o m-r-5"></i>
            {t}Delete{/t}
          </button>
          <button class="btn btn-white" ng-click="emptyContainer()" type="button">
            <i class="fa fa-fire m-r-5"></i>
            {t}Empty{/t}
          </button>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
