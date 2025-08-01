{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="VideoConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_videos_list') %]">
                  <i class="fa fa-film"></i>
                  {t}Videos{/t}
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
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=backend_video_list}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-loading btn-success ng-cloak text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
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
      <div class="grid simple">
        <div class="grid-body ng-cloak">
          <div class="row">
            {acl isAllowed="MASTER"}
              <div class="col-md-6">
                <h4 class="no-margin">Extra fields</h4>
                <autoform-editor ng-model="extraFields"/>
              </div>
            {/acl}
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
