{extends file="base/admin.tpl"}
{block name="content"}
  <form  method="POST" name="formulario" id="formulario" ng-controller="TagConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-home fa-lg"></i>
                {t}Tags{/t}
              </h4>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <h5>{t}Settings{/t}</h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_articles}" title="{t}Go back to list{/t}">
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
    <div class="content ng-cloak no-animate" ng-if="loading">
      <div class="spinner-wrapper">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
    </div>
    <div class="content panel"  ng-show="!loading">
      <div class="grid simple">
        <div class="grid-body">
          <div class="row">
            <div class="col-xs-12">
              <h5><i class="fa fa-fire m-r-5"></i> {t}Words and rules prohibited{/t}</h5>
              <div class="form-group">
                <textarea name="blacklist_comment" id="blacklist_comment" class="form-control" ng-model="blacklist_tag" rows=10></textarea>
                <div class="help">{t}List of words or regular expressions that are prohibited on comments, one per line.{/t}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
