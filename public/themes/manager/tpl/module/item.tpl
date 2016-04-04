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
          <li class="hidden-xs quicklinks">
            <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_modules_list') %]">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="hidden-xs quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-loading btn-success text text-uppercase" ng-click="save();" ng-disabled="moduleForm.$invalid || !uuidValid || saving">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i>
              {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="extra">
  <form name="moduleForm">
    <div class="row">
      <div class="col-md-4">
        <div class="grid simple">
          <div class="grid-body module-form">
            <div class="form-group" ng-class="{ 'has-error': moduleForm.uuid.$dirty && (moduleForm.uuid.$invalid || !uuidValid), 'has-success': moduleForm.uuid.$dirty && moduleForm.uuid.$valid && uuidValid }">
              <div class="clearfix">
                <label class="form-label pull-left" for="uuid">
                  {t}UUID{/t}
                  <span class="text-danger">*</span>
                </label>
              </div>
              <div class="controls">
                <div class="input-with-icon right">
                  <i class="fa fa-check text-success" ng-if="moduleForm.uuid.$dirty && moduleForm.uuid.$valid && uuidValid"></i>
                  <i class="fa fa-times text-danger" ng-if="moduleForm.uuid.$dirty && (moduleForm.uuid.$invalid || !uuidValid)" uib-tooltip="{t}This UUID is invalid{/t}"></i>
                  <input autocomplete="off" class="form-control no-animate" id="uuid" name="uuid" ng-model="module.uuid" required typeahead="uuid for uuid in extra.uuids | filter: $viewValue" typeahead-min-length="3" placeholder="es.openhost.module.example" type="text">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label class="form-label" for="type">{t}Type{/t}</label>
                  <div class="controls">
                    <select id="type" name="type" ng-model="module.type">
                      <option value="">{t}Select a type...{/t}</option>
                      <option value="module">{t}Module{/t}</option>
                      <option value="theme">{t}Theme{/t}</option>
                      <option value="theme-addon">{t}Theme addon{/t}</option>
                      <option value="Widget">{t}Widget{/t}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row" ng-show="module.type == 'module'">
              <div class="col-sm-12">
                <div class="form-group">
                  <label class="form-label" for="category">{t}Category{/t}</label>
                  <div class="controls">
                    <select id="category" name="category" ng-model="module.metas.category">
                      <option value="">{t}Select a category...{/t}</option>
                      <option value="module">{t}Module{/t}</option>
                      <option value="pack">{t}Pack{/t}</option>
                      <option value="partner">{t}Partner{/t}</option>
                      <option value="service">{t}Service{/t}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="author">{t}Author{/t}</label>
              <div class="controls">
                <input class="form-control" id="author" ng-model="module.author" placeholder="Openhost, S.L." type="text">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="author_url">{t}URL{/t}</label>
              <div class="controls">
                <input class="form-control" id="author_url" ng-model="module.url" placeholder="http://www.openhost.es" type="text">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="price">
                {t}Price{/t}
                <span class="text-danger">*</span>
              </label>
              <div class="controls">
                <div class="m-b-15 row" ng-repeat="price in module.metas.price">
                  <div class="form-group" ng-class="{ 'has-error': moduleForm['price_' + $index].$dirty && moduleForm['price_' + $index], 'has-success': moduleForm['price_' + $index].$dirty && moduleForm['price_' + $index].$valid }">
                    <div class="col-xs-3">
                      <div class="input-with-icon left">
                        <i class="fa fa-times text-danger" ng-if="moduleForm['price_' + $index].$dirty && (moduleForm['price_' + $index].$invalid)" uib-tooltip="{t}This UUID is invalid{/t}"></i>
                        <input class="form-control text-right" id="price-[% $index %]" name="price_[% $index %]" ng-model="price.value" required="required" type="number">
                      </div>
                    </div>
                    <div class="col-xs-7">
                      <select class="form-control" id="price-type-[% $index %]" name="price-type-[% $index %]" ng-model="price.type">
                        <option value="monthly">{t}Monthly{/t} (€/{t}month{/t})</option>
                        <option value="yearly">{t}Yearly{/t} (€/{t}year{/t})</option>
                        <option value="single">{t}Single{/t} (€)</option>
                        <option value="item">{t}Item{/t} (€/{t}item{/t})</option>
                      </select>
                    </div>
                    <div class="col-xs-2">
                      <button class="btn btn-block btn-success" ng-click="addPrice()" ng-if="$index === 0" type="button">
                        <i class="fa fa-plus"></i>
                      </button>
                      <button class="btn btn-block btn-danger" ng-click="removePrice($index)" ng-if="$index > 0" type="button">
                        <i class="fa fa-lg fa-trash-o"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="clearfix">
                <label class="form-label" for="uuid">{t}Modules included{/t}</label>
              </div>
              <div class="controls">
                <tags-input ng-model="module.metas.modules_included">
                  <auto-complete source="autocomplete($query)" min-length="0" load-on-focus="true" load-on-empty="true"></auto-complete>
                </tags-input>
              </div>
            </div>
            <div class="form-group">
              <div class="clearfix">
                <label class="form-label" for="uuid">{t}Modules in conflict{/t}</label>
              </div>
              <div class="controls">
                <tags-input ng-model="module.metas.modules_in_conflict">
                  <auto-complete source="autocomplete($query)" min-length="0" load-on-focus="true" load-on-empty="true"></auto-complete>
                </tags-input>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="image">{t}Image{/t}</label>
              <div class="controls">
                <input class="hidden" id="image" name="image" file-model="module.images[0]" type="file"/>
                <div class="thumbnail-wrapper">
                  <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay }"></div>
                  <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay }">
                    <p>{t}Are you sure?{/t}</p>
                    <div class="confirm-actions">
                      <button class="btn btn-link" ng-click="toggleOverlay()" type="button">
                        <i class="fa fa-times fa-lg"></i>
                        {t}No{/t}
                      </button>
                      <button class="btn btn-link" ng-click="removeFile();toggleOverlay()" type="button">
                        <i class="fa fa-check fa-lg"></i>
                        {t}Yes{/t}
                      </button>
                    </div>
                  </div>
                  <label for="image" ng-if="!module.images || module.images.length === 0 || !module.images[0]">
                    <div class="thumbnail-placeholder">
                      <div class="img-thumbnail">
                        <div class="thumbnail-empty">
                          <i class="fa fa-picture-o fa-3x block"></i>
                          <h5>{t}Pick an image{/t}</h5>
                        </div>
                      </div>
                    </div>
                  </label>
                  <div class="img-thumbnail text-center img-thumbnail-center no-animate" ng-if="module.images.length > 0">
                    <div class="text-center" ng-if="module.images.length > 0" ng-preview="module.images[0]">
                      <div class="thumbnail-actions ng-cloak" ng-if="module.images.length > 0">
                        <div class="thumbnail-action remove-action" ng-click="toggleOverlay()">
                          <i class="fa fa-trash-o fa-2x"></i>
                        </div>
                        <label class="thumbnail-action" for="image">
                          <i class="fa fa-camera fa-2x"></i>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="grid simple">
          <div class="grid-body no-padding">
            <ul class="fake-tabs b-t-0">
              <li ng-repeat="(key, value) in extra.languages" ng-class="{ 'active': language === key }" ng-click="changeLanguage(key)">
                [% value %]
                <span class="orb" ng-class="{ 'orb-danger': countStringsLeft(key) > 0, 'orb-success': countStringsLeft(key) === 0 }">
                  <i class="fa fa-check" ng-if="countStringsLeft(key) === 0"></i>
                  <span ng-if="countStringsLeft(key) > 0">[% countStringsLeft(key) %]</span>
                </span>
              </li>
            </ul>
            <div class="row p-l-15 p-r-15 p-t-15">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label">
                    {t}Name{/t}
                    <span class="text-danger">*</span>
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
                    {t}Description{/t}
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && moduleForm.description[language].$invalid }">
                    <textarea class="form-control" onm-editor onm-editor-preset="simple" id="description" name="description" ng-model="module.description[language]" rows="5"></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">
                    {t}About{/t}
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && moduleForm.about[language].$invalid }">
                    <textarea class="form-control" onm-editor onm-editor-preset="simple" id="about" name="about" ng-model="module.about[language]" rows="5"></textarea>
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
