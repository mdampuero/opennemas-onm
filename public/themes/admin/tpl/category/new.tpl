{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script>
      $(document).ready(function($) {
        var btn   = $('.onm-button');

        $('.fileinput').fileinput({
          name: 'logo_path',
          uploadtype:'image'
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <script>
      var categoryData = {$categoryData};
  </script>
  <form ng-app="BackendApp" ng-controller="CategoryCtrl" ng-init="init()" enctype="multipart/form-data">
    <div class="page-navbar actions-navbar ng-cloak" ng-show="!loading">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-bookmark"></i>
                {t}Categories{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks hidden-xs">
              <h5> [% (category.pk_content_category)?"{t}Editing category{/t}":"{t}Creating category{/t}" %]</h5>
            </li>
          {is_module_activated name="es.openhost.module.multilanguage"}
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks hidden-xs">
              <translator ng-model="lang" translator-options="languageData"/>
            </li>
          {/is_module_activated}
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_categories}" class="btn btn-link" title="{t}Config categories module{/t}">
                  <span class="fa fa-reply"></span>
                </a>
              </li>
              <li class="quicklinks"><span class="h-seperate"></span></li>
              <li class="quicklinks">
                <button class="btn btn-loading btn-primary" ng-click="save()" type="button">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving}"></i>
                  <span class="text">{t}Save{/t}</span>
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
    <div class="content ng-cloak" ng-show="!loading">
      <div class="row">
        <div class="col-md-8">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <label for="title" class="form-label">
                  {t}Title{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="title" name="title" ng-model="category.title[lang]" type="text" ng-blur="loadSlug()" required>
                </div>
              </div>
              <div class="form-group">
                <label for="name" class="form-label">{t}Slug{/t}</label>
                <div class="controls">
                  <input class="form-control" id="name" name="name" ng-model="category.name[lang]" type="text" readonly>
                </div>
              </div>
              <div class="form-group">
                <label for="subcategory" class="form-label">
                  {t}Subsection of{/t}
                </label>
                <div class="controls">
                  <select name="subcategory"
                      ng-model="category.subcategory"
                      ng-options="auxCategory.code as auxCategory.value for auxCategory in subsectionCategories"
                  >
                    <option value=""></option>
                  </select>
                </div>
              </div>
              <div class="form-group" ng-if="subcategories && subcategories.length">
                <label class="form-label">
                  {t}Subsections{/t}
                </label>
                <div class="controls">
                  <table class="table table-hover no-margin" style="width:100%">
                    <thead>
                      <tr>
                        <th>{t}Title{/t}</th>
                        <th>{t}Internal name{/t}</th>
                        <th>{t}Type{/t}</th>
                        <th>{t}In menu{/t}</th>
                        <th class="right">{t}Actions{/t}</th>
                      </tr>
                    </thead>
                    <tr ng-repeat="subcategory in subcategories">
                      <td class="left">
                        [% subcategory.title %]
                      </td>
                      <td class="left">
                        [% subcategory.name %]
                      </td>
                      <td class="left">
                          <i class="fa [% internalCategoriesImgs[subcategory.internal_category] %]" uib-tooltip="[% internalCategories.internalCategories[subcategory.internal_category].name %]"></i>
                      </td>
                      <td class="left">
                        [% (subcategory.inmenu)?"{t}Yes{/t}":"{t}No{/t}" %]
                      </td>
                      <td class="right">
                        <div class="btn-group">
                          <a class="btn btn-mini" href="[% createShowCategoryUrl(subcategory.id) %]"
                              title="Modificar">
                            <i class="fa fa-pencil"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <div class="controls">
                  <div class="checkbox">
                    <input type="checkbox"
                       id="inmenu"
                       ng-model="category.inmenu"
                       ng-true-value="1"
                       ng-false-value="'0'">
                    <label for="inmenu" class="form-label">
                      {t}Available{/t}
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="controls">
                  <div class="checkbox">
                    <input type="checkbox"
                       ng-model="category.params.inrss"
                       id="inrss"
                       name="inrss"
                       ng-true-value="1">
                    <label for="inrss" class="form-label">{t}Show in RSS{/t}</label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="internal_category" class="form-label">
                  {t}Category available for{/t}
                </label>
                <div class="controls">
                  <select name="category.internal_category"
                      id="internal_category"
                      ng-model="category.internal_category"
                      ng-options="internaAux.code as internaAux.value for internaAux in allowedCategories" >
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="color" class="form-label">
                  {t}Color{/t}
                </label>
                <div class="controls">
                  <div class="input-group">
                    <span class="input-group-addon" ng-if="category.color.indexOf('#') > -1" ng-style="{ 'background-color': category.color }">
                      &nbsp;&nbsp;&nbsp;&nbsp;
                    </span>
                    <span class="input-group-addon" ng-if="category.color.indexOf('#') <= -1" ng-style="{ 'background-color': '#' + category.color }">
                      &nbsp;&nbsp;&nbsp;&nbsp;
                    </span>
                    <input class="form-control" colorpicker="hex" id="color" name="color" ng-model="category.color" type="text">
                    <div class="input-group-btn">
                      <button class="btn btn-default" ng-click="category.color = oldColor" ng-disable="category.color == oldColor" type="button">{t}Reset{/t}</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group" ng-if="configurations.allowLogo">
                <label for="logo_path" class="form-label">{t}Category logo{/t}</label>
                <div class="controls">
                  <div class="fileinput [%(category.logo_path)?'fileinput-exists':'fileinput-new'%]" data-provides="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 140px; height: 140px;">
                    </div>
                    <div class="fileinput-exists fileinput-preview thumbnail" style="width: 140px; height: 140px;">
                        <img src="[% categoryUrl %]" style="max-width:200px;" >
                    </div>
                    <div>
                      <span class="btn btn-file">
                        <span class="fileinput-new">{t}Add new photo{/t}</span>
                        <span class="fileinput-exists">{t}Change{/t}</span>
                        <input type="file" file-model="category.logo_path" name="category.logo_path" class="file-input" value="1">
                      </span>
                      <a href="#" class="btn btn-danger fileinput-exists delete" data-dismiss="fileinput">
                        <i class="fa fa-trash-o"></i>
                        {t}Remove{/t}
                      </a>
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
