{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="UrlCtrl" ng-init="getItem({$id})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-globe m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_urls_list') %]">
                  {t}URLs{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>
                {if empty($id)}{t}Create{/t}{else}{t}Edit{/t}{/if}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="form.$invalid">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && item === null">
        <div class="text-center p-b-15 p-t-15">
          <a href="[% routing.generate('backend_user_groups_list') %]">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t}Unable to find the item{/t}</h3>
            <h4>{t}Click here to return to the list{/t}</h4>
          </a>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!flags.http.loading && item">
        <div class="row">
          <div class="col-md-4 col-md-push-8">
            <div class="grid simple">
              <div class="grid-body no-padding">
                <div class="grid-collapse-title">
                  <div class="checkbox">
                    <input class="form-control" id="enabled" name="enabled" ng-model="item.enabled" ng-true-value="1" ng-false-value="0" type="checkbox">
                    <label for="enabled" class="form-label">
                      {t}Enabled{/t}
                    </label>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.redirection }" ng-click="expanded.redirection = !expanded.redirection">
                  <i class="fa fa-retweet m-r-10"></i>{t}Redirection{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.redirection }"></i>
                  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.redirection">
                    <span ng-show="item.redirection">{t}Enabled{/t}</span>
                    <span ng-show="!item.redirection">{t}Disabled{/t}</span>
                  </span>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.redirection }">
                  <div class="form-group no-margin">
                    <div class="checkbox">
                      <input class="form-control" id="redirection" name="redirection" ng-false-value="0" ng-model="item.redirection" ng-true-value="1" type="checkbox">
                      <label for="redirection" class="form-label">
                        {t}Redirection{/t}
                      </label>
                    </div>
                    <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}If enabled, this rule will cause a redirection to a valid opennemas URL{/t}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 col-md-pull-4">
            <div class="grid simple">
              <div class="grid-title">
                <div class="form-group no-margin">
                  <h4>
                    <i class="fa fa-cog"></i>
                    {t}Mode{/t}
                  </h4>
                </div>
              </div>
              <div class="grid-body">
                <div class="row">
                  <div class="col-sm-12 form-group no-margin">
                    <div class="radio">
                      <input id="type-content-content" ng-model="item.type" ng-value="0" type="radio">
                      <label class="form-label" for="type-content-content">
                        <i class="fa fa-file-text-o"></i>
                        <strong>{t}Content{/t}</strong>
                        -
                        <i class="fa fa-file-text-o"></i>
                        <strong>{t}Content{/t}</strong>
                        ({t}Migrations only{/t})
                      </label>
                    </div>
                    <div class="help m-l-3" ng-if="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}Redirects a content id to another content id{/t}
                    </div>
                    <div class="help m-b-15 m-l-3" ng-if="isHelpEnabled()">
                      <i class="fa fa-warning m-r-5 text-warning"></i>
                      {t}This should be used during migration only{/t}
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6 form-group no-margin">
                    <div class="radio">
                      <input id="type-slug-content" ng-model="item.type" ng-value="1" type="radio">
                      <label for="type-slug-content">
                        <i class="fa fa-code"></i>
                        <strong>{t}Slug{/t}</strong>
                        -
                        <i class="fa fa-file-text-o"></i>
                        <strong>{t}Content{/t}</strong>
                      </label>
                    </div>
                    <div class="help m-b-15 m-l-3" ng-if="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}Redirects a slug to a content id{/t}
                    </div>
                  </div>
                  <div class="col-sm-6 form-group no-margin">
                    <div class="radio">
                      <input id="type-slug-slug" ng-model="item.type" ng-value="2" type="radio">
                      <label for="type-slug-slug">
                        <i class="fa fa-code"></i>
                        <strong>{t}Slug{/t}</strong>
                        -
                        <i class="fa fa-code"></i>
                        <strong>{t}Slug{/t}/{t}URL{/t}</strong>
                      </label>
                    </div>
                    <div class="help m-b-15 m-l-3" ng-if="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}Redirects a slug to another slug or external URL{/t}
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6 form-group">
                    <div class="radio">
                      <input id="type-regex-content" ng-model="item.type" ng-value="3" type="radio">
                      <label for="type-regex-content">
                        <i class="fa fa-asterisk"></i>
                        <strong>{t}Regex{/t}</strong>
                        -
                        <i class="fa fa-file-text-o"></i>
                        <strong>{t}Content{/t}</strong>
                      </label>
                    </div>
                    <div class="help m-l-3" ng-if="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}Redirects slugs to captured content ids basing on a regular expression{/t}
                    </div>
                  </div>
                  <div class="col-sm-6 form-group">
                    <div class="radio">
                      <input id="type-regex-slug" ng-model="item.type" ng-value="4" type="radio">
                      <label for="type-regex-slug">
                        <i class="fa fa-asterisk"></i>
                        <strong>{t}Regex{/t}</strong>
                        -
                        <i class="fa fa-code"></i>
                        <strong>{t}Slug{/t}/{t}URL{/t}</strong>
                      </label>
                    </div>
                    <div class="help m-l-3" ng-if="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}Redirects slugs to another slugs basing on a regular expression{/t}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="grid simple">
              <div class="grid-title">
                <h4>
                  <i class="fa fa-plane"></i>
                  {t}Source{/t} - {t}Target{/t}
                </h4>
              </div>
              <div class="grid-body">
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label for="name" class="form-label">
                        <span ng-if="item.type === 0">{t}Content{/t}</span>
                        <span ng-if="item.type === 1 || item.type === 2">{t}Slug{/t}</span>
                        <span ng-if="item.type >= 3">{t}Pattern{/t}</span>
                      </label>
                      <div class="controls">
                        <input class="form-control" id="name" name="name" ng-model="item.source" placeholder="[% item.type == 0 ? '1234' : (item.type == 1 || item.type == 2 ? 'qux/thud/norf': '^[a-z]+/([0-9]+)$') %]" required type="text">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-8">
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group no-margin">
                          <label for="name" class="form-label">
                            <span ng-if="item.type === 0 || item.type === 1 || item.type === 3">{t}Content{/t}</span>
                            <span ng-if="item.type === 2 || item.type === 4">{t}Slug{/t}/{t}URL{/t}</span>
                          </label>
                          <span class="help m-l-5" ng-if="item.type === 2 || item.type === 4">
                            <i class="fa fa-warning text-warning"></i>
                            {t}An empty slug equals to frontpage{/t}
                          </span>
                          <div class="controls">
                            <div class="input-group">
                              <span class="input-group-btn">
                                <button class="btn btn-default" content-picker content-picker-selection="true" content-picker-max-size="1" content-picker-target="data.extra.content" content-picker-type="attachment,album,article,letter,opinion,poll,photo,video" content-picker-view="list-item" uib-tooltip="{t}Select a content{/t}" type="button">
                                  <i class="fa fa-search"></i>
                                </button>
                              </span>
                              <input class="form-control" id="name" name="name" ng-disabled="data.extra.content" ng-model="item.target" ng-required="item.type !== 2 && item.type !== 4" placeholder="[% item.type == 0 || item.type == 1 ? '4685' : (item.type === 2 ? 'http://www.qux.org/thud/norf' : (item.type === 3 ? '$1' : 'http://www.qux.org/$1')) %]" type="text">
                              <span class="input-group-btn">
                                <button class="btn btn-danger" ng-click="data.extra.content = null" ng-if="data.extra.content" type="button">
                                  <i class="fa fa-trash-o"></i>
                                </button>
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6" ng-if="item.type === 0 || item.type === 1 || item.type === 3">
                        <div class="form-group no-margin">
                          <label for="name" class="form-label">{t}Content type{/t}</label>
                          <div class="controls">
                            <div class="content-placeholder">
                              <input class="form-control" id="name" name="name" ng-disabled="data.extra.content" ng-model="item.content_type" required type="text">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12" ng-if="data.extra.content">
                        <strong>{t}Title{/t}:</strong>
                        <i>[% data.extra.content.title %]</i>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 text-center">
                    <a href="#" ng-click="examples = !examples">
                      <strong>{t}Examples{/t}</strong>
                    </a>
                  </div>
                </div>
                <div class="m-t-30" ng-if="examples">
                  <div class="row">
                    <div class="col-sm-4">
                      <div class="form-group no-margin">
                        <label for="name" class="form-label">
                          <span ng-if="item.type === 0">{t}Content{/t}</span>
                          <span ng-if="item.type === 1 || item.type === 2">{t}Slug{/t}</span>
                          <span ng-if="item.type === 3 || item.type === 4">{t}Regex{/t}</span>
                        </label>
                        <div class="controls">
                          <input class="form-control" ng-if="item.type === 0" readonly type="text" value="1324">
                          <input class="form-control" ng-if="item.type === 1 || item.type === 2"  readonly type="text" value="glorp">
                          <input class="form-control" ng-if="item.type === 3 || item.type === 4"  readonly type="text" value="^[a-z]+/[0-9]+">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group no-margin">
                        <label for="name" class="form-label">
                          <span ng-if="item.type === 0 || item.type === 1 || item.type === 3">{t}Content{/t}</span>
                          <span ng-if="item.type === 2 || item.type === 4">{t}Slug{/t}/{t}URL{/t}</span>
                        </label>
                        <div class="controls">
                          <input class="form-control" ng-if="item.type === 0 || item.type === 1 || item.type === 3"  readonly type="text" value="32455">
                          <input class="form-control" ng-if="item.type === 2 || item.type === 4"  readonly type="text" value="flob">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4" ng-if="item.type !== 2 && item.type !== 4">
                      <div class="form-group no-margin">
                        <label for="name" class="form-label">{t}Content type{/t}</label>
                        <div class="controls">
                          <input class="form-control" readonly type="text" value="article">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="m-t-15 row">
                    <div class="col-sm-4">
                      <div class="form-group no-margin">
                        <label for="name" class="form-label">
                          <span ng-if="item.type === 0">{t}Content{/t}</span>
                          <span ng-if="item.type === 1 || item.type === 2">{t}Slug{/t}</span>
                          <span ng-if="item.type === 3 || item.type === 4">{t}Regex{/t}</span>
                        </label>
                        <div class="controls">
                          <input class="form-control" ng-if="item.type === 0"  readonly type="text" value="534">
                          <input class="form-control" ng-if="item.type === 1 || item.type === 2"  readonly type="text" value="wibble/grault">
                          <input class="form-control" ng-if="item.type === 3 || item.type === 4"  readonly type="text" value="^([a-z]+)/([0-9]+)">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group no-margin">
                        <label for="name" class="form-label">
                          <span ng-if="item.type === 0 || item.type === 1 || item.type === 3">{t}Content{/t}</span>
                          <span ng-if="item.type === 2 || item.type === 4">{t}Slug{/t}/{t}URL{/t}</span>
                        </label>
                        <div class="controls">
                          <input class="form-control" ng-if="item.type === 0 || item.type === 1"  readonly type="text" value="5345">
                          <input class="form-control" ng-if="item.type === 2"  readonly type="text" value="">
                          <input class="form-control" ng-if="item.type === 3"  readonly type="text" value="$1-$2">
                          <input class="form-control" ng-if="item.type === 4"  readonly type="text" value="http://www.example.org/$2/$1">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4" ng-if="item.type !== 2 && item.type !== 4">
                      <div class="form-group no-margin">
                        <label for="name" class="form-label">{t}Content type{/t}</label>
                        <div class="controls">
                          <input class="form-control" readonly type="text" value="photo">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="m-t-15 row">
                    <div class="col-sm-4">
                      <div class="form-group no-margin">
                        <label for="name" class="form-label">
                          <span ng-if="item.type === 0">{t}Content{/t}</span>
                          <span ng-if="item.type === 1 || item.type === 2">{t}Slug{/t}</span>
                          <span ng-if="item.type === 3 || item.type === 4">{t}Regex{/t}</span>
                        </label>
                        <div class="controls">
                          <input class="form-control" ng-if="item.type === 0"  readonly type="text" value="23">
                          <input class="form-control" ng-if="item.type === 1 || item.type === 2"  readonly type="text" value="grault/norf">
                          <input class="form-control" ng-if="item.type === 3 || item.type === 4"  readonly type="text" value="^glorp-(.*)$">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group no-margin">
                        <label for="name" class="form-label">
                          <span ng-if="item.type === 0 || item.type === 1 || item.type === 3">{t}Content{/t}</span>
                          <span ng-if="item.type === 2 || item.type === 4">{t}Slug{/t}/{t}URL{/t}</span>
                        </label>
                        <div class="controls">
                          <input class="form-control" ng-if="item.type === 0 || item.type === 1"  readonly type="text" value="45">
                          <input class="form-control" ng-if="item.type === 2"  readonly type="text" value="http://www.example.org">
                          <input class="form-control" ng-if="item.type === 3"  readonly type="text" value="$1">
                          <input class="form-control" ng-if="item.type === 4"  readonly type="text" value="http://www.example.org/$0">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4" ng-if="item.type !== 2 && item.type !== 4">
                      <div class="form-group no-margin">
                        <label for="name" class="form-label">{t}Content type{/t}</label>
                        <div class="controls">
                          <input class="form-control" readonly type="text" value="user">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
