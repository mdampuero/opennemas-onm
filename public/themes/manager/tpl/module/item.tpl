<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_modules_list') %]">
              <i class="fa fa-plug"></i>
              {t}Modules{/t}
            </a>
          </h4>
        </li>
        <li class="quicklinks seperate">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h5>
            <span ng-if="!module.id">{t}New module{/t}</span>
            <span ng-if="module.id">{t}Edit module{/t}</span>
          </h5>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_modules_list') %]">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-primary" ng-click="save();" ng-disabled="saving" ng-if="!module.id">
              <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
            <button class="btn btn-primary" ng-click="update();" ng-disabled="saving" ng-if="module.id">
              <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <form name="moduleForm" novalidate>
    <div class="row">
      <div class="col-md-4">
        <div class="grid simple">
          <div class="grid-body module-form">
            <div class="form-group">
              <label for="id">{t}Id{/t}</label>
              <div class="controls">
                <input class="form-control" id="uuid" ng-model="module.uuid" placeholder="es.openhost.module.example" type="text">
              </div>
            </div>
            <div class="form-group">
              <label for="version">{t}Version{/t}</label>
              <div class="controls">
                <input class="form-control" id="version" ng-model="module.version" placeholder="1.0" type="text">
              </div>
            </div>
            <div class="form-group">
              <label for="author">{t}Author{/t}</label>
              <div class="controls">
                <input class="form-control" id="author" ng-model="module.author.name" placeholder="Openhost, S.L." type="text">
              </div>
            </div>
            <div class="form-group">
              <label for="author_url">{t}Author URL{/t}</label>
              <div class="controls">
                <input class="form-control" id="author_url" ng-model="module.author.url" placeholder="http://www.openhost.es" type="text">
              </div>
            </div>
            <div class="form-group">
              <label for="image">{t}Image{/t}<label>
              <div class="controls">
                <input id="id" ng-model="module.author.url" type="file">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="grid simple">
          <div class="grid-body no-padding">
            <ul class="fake-tabs b-t-0">
              <li ng-repeat="(key, value) in languages" ng-class="{ 'active': language === key }" ng-click="changeLanguage(key)">[% value%]</li>
            </ul>
            <div class="row p-l-15 p-r-15 p-t-15">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label">
                    {t}Name{/t}
                    <span ng-show="moduleForm.name.$invalid">*</span>
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && moduleForm.title[language].$invalid }">
                    <input class="form-control" id="name" name="name" ng-model="module.name[language]" required type="text">
                  </div>
                  <span class="error" ng-show="formValidated && moduleForm.name.$invalid">
                    <label for="name" class="error">{t}This field is required{/t}</label>
                  </span>
                </div>
                <div class="form-group">
                  <label class="form-label">
                    {t}Short description{/t}
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && moduleForm.short_description[language].$invalid }">
                    <textarea class="form-control" onm-editor onm-editor-preset="simple" id="short_description" name="short_description" ng-model="module.short_description[language]" rows="5"></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">
                    {t}Description{/t}
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && moduleForm.description[language].$invalid }">
                    <textarea class="form-control" onm-editor onm-editor-preset="simple" id="description" name="description" ng-model="module.description[language]" rows="5"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
