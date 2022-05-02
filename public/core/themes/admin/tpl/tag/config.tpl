{extends file="base/admin.tpl"}
{block name="content"}
  <form name="form" ng-controller="TagConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-tags m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_tags_list') %]">
                  {t}Tags{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>{t}Configuration{/t}</h4>
            </li>
          </ul>
          <div class="ng-cloak pull-right" ng-if="!flags.http.loading">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="save($event)" ng-disabled="flags.http.loading || flags.http.saving" type="button">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
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
      <div class="grid simple ng-cloak" ng-show="!flags.http.loading">
        <div class="grid-body">
          <div class="row">
            <div class="form-group col-lg-4 col-md-6">
              <label class="form-label" for="max-tags">{t}Maximum number of tags per content{/t}</label>
              <div class="controls">
                <input class="form-control" id="max-tags" ng-model="settings.tags_maxItems" type="number">
              </div>
            </div>
            <div class="form-group col-lg-4 col-md-6">
              <label class="form-label" for="max-tags">{t}Number of items in the list of suggestions{/t}</label>
              <div class="controls">
                <input class="form-control" id="max-tags" ng-model="settings.tags_maxResults" type="number">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-12">
              <h5><i class="fa fa-fire m-r-5"></i> {t}Words and rules prohibited{/t}</h5>
              <div class="form-group">
                <textarea name="blacklist_tag" id="blacklist_tag" class="form-control" ng-model="settings.blacklist_tag" rows=10></textarea>
                <div class="help">{t}List of words or regular expressions that are prohibited on tags, one per line.{/t}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
