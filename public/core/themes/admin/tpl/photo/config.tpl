{extends file="base/admin.tpl"}
{block name="content"}
  <form ng-controller="PhotoConfigCtrl" ng-init="init({json_encode($extra_fields)|clear_json})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-picture-o m-r-10"></i>
                {t}Photos{/t}
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
                <a class="btn btn-link" href="{url name=backend_photos_list}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" type="button" ng-click="save()">
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
        <div class="grid-title">
          <h4>
            <i class="fa fa-toggle-on"></i>
            {t}Default settings{/t}
          </h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
            <div class="checkbox">
              <input id="optimize_images" name="optimize-images" type="checkbox" ng-model="config.optimize_images">
              <label class="form-label" for="optimize_images">
                <span class="checkbox-title">{t}Optimize images{/t}</span>
                <div class="help">
                  {t}If set, images will be automaticaly optimized when uploaded and imported{/t}
                </div>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
