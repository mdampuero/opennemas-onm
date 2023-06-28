{extends file="base/admin.tpl"}
{block name="content"}
  <form ng-controller="VideoConfigCtrl" ng-init="init({json_encode($extra_fields)|clear_json})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-film"></i>
                {t}Videos{/t}
              </h4>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
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
                <button class="btn btn-primary" type="button" ng-click="saveConf($event)">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple">
        <div class="grid-body">
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
