{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}News Agency{/t} > {t}Servers{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="NewsAgencyServerCtrl" ng-init="getItem({$id});flags.block.password = true;"
{/block}

{block name="icon"}
  <i class="fa fa-microphone m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_news_agency_resource_list}">
    {t}News Agency{/t}
  </a>
{/block}

{block name="extraTitle"}
  <li class="hidden-xs quicklinks m-l-5 m-r-5">
    <h4>
      <i class="fa fa-angle-right"></i>
    </h4>
  </li>
  <li class="hidden-xs quicklinks">
    <h4>
      <a class="no-padding" href="{url name=backend_news_agency_server_list}">
        {t}Servers{/t}
      </a>
    </h4>
  </li>
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" type="button">
      <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
      {t}Save{/t}
    </button>
  </li>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Enabled{/t}" field="activated"}
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Automatic import{/t}" field="auto_import"}
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': flags.expanded.color }" ng-click="flags.expanded.color = !flags.expanded.color">
        <i class="fa fa-paint-brush m-r-10"></i>{t}Color{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': flags.expanded.color }"></i>
        <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase" ng-style="{ 'background-color': item.color }" ng-show="!flags.expanded.color && item.color">
          &nbsp;&nbsp;
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': flags.expanded.color }">
        <div class="form-group no-margin">
          <div class="controls">
            {include file="ui/component/input/color.tpl" ngModel="item.color"}
          </div>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': flags.expanded.sync_from }" ng-click="flags.expanded.sync_from = !flags.expanded.sync_from">
        <i class="fa fa-clock-o m-r-10"></i>{t}Synchronization{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': flags.expanded.sync_from }"></i>
        <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-bold text-uppercase" ng-show="!flags.expanded.sync_from && item.sync_from">
          [% data.extra.sync_from[item.sync_from] %]
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': flags.expanded.sync_from }">
        <div class="form-group no-margin">
          <label for="sync_from" class="form-label">
            {t}Sync elements newer than{/t}
          </label>
          <div class="controls">
            <select class="form-control" name="sync_from" ng-model="item.sync_from" required>
              <option value="[% key %]" ng-repeat="(key, value) in data.extra.sync_from">[% value %]</option>
            </select>
            <div class="help m-l-3 m-t-5">
              <i class="fa fa-info-circle m-r-5 text-info"></i>
              {t}Less time means faster synchronizations{/t}
            </div>
          </div>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': flags.expanded.external_link }" ng-click="flags.expanded.external_link = !flags.expanded.external_link">
        <i class="fa fa-globe m-r-10"></i>{t}External link{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': flags.expanded.external_link }"></i>
        <a class="badge badge-default m-r-10 pull-right text-bold text-uppercase" ng-click="$event.stopPropagation()" ng-href="[% item.external_link %]" ng-show="!flags.expanded.external_link && item.external_link" target="_blank">
          <i class="fa fa-external-link"></i>
          {t}Link{/t}
        </a>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': flags.expanded.external_link }">
        <div class="form-group no-margin">
          <div class="controls">
            <input class="form-control" id="external-link" name="external_link" ng-model="item.external_link" type="text">
          </div>
          <div class="help m-l-3 m-t-5">
            <i class="fa fa-info-circle m-r-5 text-info"></i>
            {t}When importing assign an external link to the elements{/t}
          </div>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': flags.expanded.agency }" ng-click="flags.expanded.agency = !flags.expanded.agency">
        <i class="fa fa-pencil m-r-10"></i>{t}Agency{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': flags.expanded.agency }"></i>
        <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-bold text-uppercase" ng-show="!flags.expanded.agency && item.agency_string">
          [% item.agency_string %]
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': flags.expanded.agency }">
        {include file="ui/component/input/text.tpl" iClass="no-margin" iHelp="{t}When importing elements this will be the signature{/t}" iField="agency_string"}
      </div>
      <div ng-if="item.auto_import">
        <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': flags.expanded.author }" ng-click="flags.expanded.author = !flags.expanded.author">
          <i class="fa fa-cloud-download m-r-10"></i>
          {t}Import{/t} {t}as{/t}
          <span ng-if="!flags.expanded.author">
            {include file="ui/component/icon/status.tpl" iField="target" iRequired=true iValidation=true}
          </span>
          <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': flags.expanded.author }"></i>
          <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-bold text-uppercase" ng-show="!flags.expanded.author && item.target">
            <span ng-if="item.target === 'article'">{t}Article{/t}</span>
            <span ng-if="item.target === 'opinion'">{t}Opinion{/t}</span>
          </span>
        </div>
        <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': flags.expanded.author }">
          <div class="form-group">
            <div class="controls controls-validation">
              <select class="block" name="target" ng-model="item.target" required>
                <option value="">{t}Select a type{/t}…</option>
                <option value="article">{t}Article{/t}</option>
                <option value="opinion">{t}Opinion{/t}</option>
              </select>
              {include file="ui/component/icon/status.tpl" iClass="form-status-absolute" iField="target" iRequired=true iValidation=true}
            </div>
          </div>
        </div>
        <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': flags.expanded.author }" ng-click="flags.expanded.author = !flags.expanded.author">
          <i class="fa fa-edit m-r-10"></i>{t}Author{/t}
          <span ng-if="!flags.expanded.author">
            {include file="ui/component/icon/status.tpl" iField="author" iRequired=true iValidation=true}
          </span>
          <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': flags.expanded.author }"></i>
          <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-bold text-uppercase" ng-show="!flags.expanded.author && item.author">
            [% defaultAuthor.name %]
          </span>
        </div>
        <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': flags.expanded.author }">
          <div class="form-group">
            <label class="form-label" for="author">
              {t}Default author{/t}
            </label>
            <div class="controls controls-validation">
              <onm-author-selector class="block" default-value-text="{t}Select an author{/t}…" export-model="defaultAuthor" name="author" ng-model="item.author" placeholder="{t}Select an author{/t}…" required></onm-author-selector>
              {include file="ui/component/icon/status.tpl" iClass="form-status-absolute" iField="author" iRequired=true iValidation=true}
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">
              {t}Author mapping{/t}
            </label>
            <span class="help m-l-5">
              {t}Source{/t} - {t}Target{/t}
            </span>
            <div class="controls">
              <div class="row m-t-15" ng-repeat="author in item.authors_map track by $index">
                <div class="col-lg-5 col-md-9 col-sm-5 col-xs-6 m-b-15">
                  <input class="form-control" ng-model="author.slug" placeholder="{t}Author name from source{/t}" required type="text">
                </div>
                <div class="col-lg-5 col-md-9 col-sm-5 col-xs-6 m-b-15">
                  <onm-author-selector class="block" default-value-text="{t}Select an author{/t}…" ng-model="author.id" placeholder="{t}Select an author{/t}…" required></onm-author-selector>
                </div>
                <div class="col-lg-2 col-lg-offset-0 col-md-3 col-md-offset-0 col-sm-2 col-sm-offset-0 col-xs-4 col-xs-offset-4">
                  <button class="btn btn-block btn-danger ng-cloak" ng-click="removeFromMap('authors', $index)" type="button">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
              <button class="btn btn-block btn-default" ng-click="addToMap('authors')" type="button">
                <i class="fa fa-plus"></i>
                {t}Add{/t}
              </button>
            </div>
          </div>
        </div>
        <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': flags.expanded.category }" ng-click="flags.expanded.category = !flags.expanded.category">
          <i class="fa fa-bookmark m-r-10"></i>{t}Category{/t}
          <span ng-if="!flags.expanded.category">
            {include file="ui/component/icon/status.tpl" iField="category" iRequired=true iValidation=true}
          </span>
          <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': flags.expanded.category }"></i>
          <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-bold text-uppercase" ng-show="!flags.expanded.category && item.category">
            [% defaultCategory.title %]
          </span>
        </div>
        <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': flags.expanded.category }">
          <div class="form-group">
            <label class="form-label" for="category">
              {t}Default category{/t}
            </label>
            <div class="controls controls-validation">
              <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" export-model="defaultCategory" name="category" ng-model="item.category" placeholder="{t}Select a category{/t}…" required></onm-category-selector>
              {include file="ui/component/icon/status.tpl" iClass="form-status-absolute" iField="category" iRequired=true iValidation=true}
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="category_mapping">
              {t}Category mapping{/t}
            </label>
            <span class="help m-l-5">
              {t}Source{/t} - {t}Target{/t}
            </span>
            <div class="controls">
              <div class="row m-t-15" ng-repeat="category in item.categories_map track by $index">
                <div class="col-lg-5 col-md-9 col-sm-5 col-xs-6 m-b-15">
                  <input class="form-control" ng-model="category.slug" placeholder="{t}Category name from source{/t}" type="text">
                </div>
                <div class="col-lg-5 col-md-9 col-sm-5 col-xs-6 m-b-15">
                  <onm-category-selector class="block select2-border" default-value-text="{t}Select a category{/t}…" ng-model="category.id" placeholder="{t}Select a category{/t}…" required></onm-category-selector>
                </div>
                <div class="col-lg-2 col-lg-offset-0 col-md-3 col-md-offset-0 col-sm-2 col-sm-offset-0 col-xs-4 col-xs-offset-4">
                  <button class="btn btn-block btn-danger ng-cloak" ng-click="removeFromMap('categories', $index)" type="button">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 col-lg-offset-4 col-md-10 col-md-offset-1 col-sm-6 col-sm-offset-3">
              <button class="btn btn-block btn-default" ng-click="addToMap('categories')" type="button">
                <i class="fa fa-plus"></i>
                {t}Add{/t}
              </button>
            </div>
          </div>
        </div>
        <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': flags.expanded.filter }" ng-click="flags.expanded.filter = !flags.expanded.filter">
          <i class="fa fa-filter m-r-10"></i>{t}Filter{/t}
          <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': flags.expanded.filter }"></i>
          <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-bold text-uppercase" ng-show="!flags.expanded.filter && item.filters && item.filters.length > 0">
            [% item.filters.length %] {t}filters{/t}
          </span>
        </div>
        <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': flags.expanded.filter }">
          <div class="form-group">
            <div class="help m-l-3 m-t-5">
              <i class="fa fa-info-circle m-r-5 text-info"></i>
              {t}Filter contents that matches one or more list of words{/t}
            </div>
            <div class="row m-t-15" ng-repeat="filter in item.filters track by $index">
              <div class="col-sm-10 col-xs-9 m-b-15 text-center" ng-if="$index">
                {t}or{/t}
              </div>
              <div class="col-lg-10 col-md-9 col-sm-10 col-xs-8">
                <input class="form-control" name="filters-[% $index %]" ng-model="item.filters[$index]" placeholder="{t}Comma-separated list of words to match{/t}" required type="text">
              </div>
              <div class="col-lg-2 col-md-3 col-sm-2 col-xs-4">
                <button class="btn btn-block btn-danger ng-cloak" ng-click="removeFilter($index)" type="button">
                  <i class="fa fa-trash-o"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
              <button class="btn btn-block btn-default" ng-click="addFilter()" type="button">
                <i class="fa fa-plus"></i>
                {t}Add{/t}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="form-group">
        {include file="ui/component/input/text.tpl" iClass="no-margin" iTitle="{t}Source name{/t}" iField="name" iRequired=true iValidation=true}
      </div>
      <div class="form-group">
        <label class="form-controls">
          {t}Type{/t}
        </label>
        <div class="controls">
          <div class="radio">
            <input class="form-control" id="external-agency" ng-model="item.type" ng-value="'0'" type="radio"/>
            <label for="external-agency">
              {t}External agency{/t}
            </label>
          </div>
          <div class="radio">
            <input class="form-control" id="opennemas-agency" ng-model="item.type" ng-value="'1'" type="radio"/>
            <label for="opennemas-agency">
              {t}Opennemas News Agency{/t}
            </label>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-9">
          <div class="form-group">
            <label class="form-label" for="url">
              {t}Url{/t}
            </label>
            <div class="controls">
              <input class="form-control" name="url" ng-model="item.url" ng-if="item.type == 0" placeholder="ftp://server.com/path" required type="text">
              {include file="ui/component/icon/status.tpl" iClass="form-status-absolute" iField="url" iRequired=true iValidation=true}
              <div class="input-group no-animate ng-cloak p-r-50" ng-if="item.type == 1">
                <span class="input-group-addon">
                  https://
                </span>
                <input class="form-control no-animate" name="instance" ng-disabled="item.type == 0" ng-model="data.extra.instance" required type="text">
                <span class="input-group-addon">
                  .opennemas.com/ws/agency
                </span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 form-group no-margin">
              <label class="form-label" for="username">
                {t}Username{/t}
              </label>
              <div class="controls">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  <input class="form-control" id="username" name="username" ng-model="item.username" type="text">
                </div>
              </div>
            </div>
            <div class="col-sm-6 form-group no-margin">
              <label class="form-label" for="password">
                {t}Password{/t}
              </label>
              <div class="controls">
                <div class="input-group">
                  <span class="input-group-btn">
                    <button class="btn btn-default" ng-click="flags.block.password = !flags.block.password" type="button">
                      <i class="fa" ng-class="{ 'fa-lock': flags.block.password, 'fa-unlock-alt': !flags.block.password }"></i>
                    </button>
                  </span>
                  <input class="form-control" id="password" name="password" ng-model="item.password" type="[% flags.block.password ? 'password' : 'text' %]">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-l-30 p-r-30 p-t-35">
            <div class="text-center">
              <i class="fa fa-3x fa-question" ng-show="!status"></i>
              <i class="fa fa-3x fa-check text-success" ng-show="status === 'success'"></i>
              <i class="fa fa-3x fa-times text-danger" ng-show="status === 'failure'"></i>
              <p class="m-t-15 text-center">
                <strong>
                  {t}Status{/t}
                </strong>
              </p>
              <button class="btn btn-block btn-default btn-loading m-t-5" ng-click="check()" ng-disabled="!item.url || flags.http.checking" type="button">
                <i class="fa fa-sitemap m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.checking }"></i>
                {t}Connect{/t}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}
