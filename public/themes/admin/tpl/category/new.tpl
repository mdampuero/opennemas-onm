{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script>
      var categoryData = {$categoryData};
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form ng-app="BackendApp" ng-controller="CategoryCtrl" ng-init="show(categoryData)" class="settings">
    <div class="page-navbar actions-navbar">
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
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks hidden-xs">
              <div class="btn-group">
                <button type="button" class="form-control btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <span class="fa fa-exchange"></span>Galician<span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#"><span class="fa fa-pencil" aria-hidden="true"></span>French</a></li>
                  <li><a href="#"><span class="fa fa-globe" aria-hidden="true"></span>English</a></li>
                  <li><a href="#" class="text-muted"><span class="fa fa-exchange" aria-hidden="true"></span>Galician</a></li>
                </ul>
              </div>
            </li>
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
    <div class="content">
      <div class="row">
        <div class="col-md-8">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <label for="title" class="form-label">
                  {t}Title{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="title" name="title" ng-model="category.title" type="text" required>
                </div>
              </div>
              <div class="form-group" ng-if="category.name">
                <label for="name" class="form-label">{t}Slug{/t}</label>
                <div class="controls">
                  <input class="form-control" id="name" name="name" ng-model="category.name" type="text" readonly>
                </div>
              </div>
              <div class="form-group">
                <label for="subcategory" class="form-label">
                  {t}Subsection of{/t}
                </label>
                <div class="controls">
                  <select name="subcategory" ng-model="category.subcategory">
                    <option value="0">--</option>
                    <option
                        value="[% auxCategory.pk_content_category %]"
                        ng-repeat="auxCategory in categories"
                        ng-selected="[% auxCategory.pk_content_category === category.subcategory %]"
                        ng-if="auxCategory.pk_content_category != category.pk_content_category">
                      [% category.title %]
                    </option>
                  </select>
                </div>
              </div>
              <div class="form-group" ng-if="subcategories">
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

                        {/*if $subcategorys[s]->internal_category eq 7}
                          <i class="fa fa-stack-overflow" uib-tooltip="{t}Albums{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 9}
                          <i class="fa fa-film" uib-tooltip="{t}Videos{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 11}
                          <i class="fa fa-pie-chart" uib-tooltip="{t}Polls{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 10}
                          <i class="fa fa-star" uib-tooltip="{t}Specials{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 14}
                          <i class="fa fa-newspaper-o" uib-tooltip="{t}News Stand{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 15}
                          <i class="fa fa-book" uib-tooltip="{t}Books{/t}"></i>
                        {/if */}
                      </td>
                      <td class="left">
                        [% (subcategory.inmenu)?"{t}Yes{/t}":"{t}No{/t}" %]
                      </td>
                      <td class="right">
                        <div class="btn-group">
                          <a class="btn btn-mini" href="[% createShowCategoryUrl(subcategory.pkContentCategory) %]"
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
                    <input type="checkbox" id="inmenu" name="inmenu" value="1" {if $category->inmenu eq 1} checked="checked"{/if}>
                    <input type="checkbox"
                       ng-model="category.inmenu"
                       id="category.inmenu"
                       name="category.inmenu"
                       ng-true-value="1">
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
                       id="category.params.inrss"
                       name="category.params.inrss"
                       ng-true-value="1">
                    <label for="params[inrss]" class="form-label">{t}Show in RSS{/t}</label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="internal_category" class="form-label">
                  {t}Category available for{/t}
                </label>
                <div class="controls">
                  <select name="category.internal_category" id="internal_category" ng-model="category.internal_category"  required>
                    <option
                        value="[% internalCategory.pk_content_type %]"
                        ng-repeat="internalCategory in internalCategories"
                        ng-selected="[% internalCategory.pk_content_type === category.internal_category %]">
                      [% internalCategory.title %]
                    </option>
                    <option value="1"
                    {if  (empty($category->internal_category) || $category->internal_category eq 1)} selected="selected"{/if}>{t}All contents{/t}</option>
                    {is_module_activated name="ALBUM_MANAGER"}
                    <option value="7"
                    {if isset($category) && ($category->internal_category eq 7)} selected="selected"{/if}>{t}Albums{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="VIDEO_MANAGER"}
                    <option value="9"
                    {if isset($category) && ($category->internal_category eq 9)} selected="selected"{/if}>{t}Video{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="POLL_MANAGER"}
                    <option value="11"
                    {if isset($category) && ($category->internal_category eq 11)} selected="selected"{/if}>{t}Poll{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="KIOSKO_MANAGER"}
                    <option value="14"
                    {if isset($category) && ($category->internal_category eq 14)} selected="selected"{/if}>{t}ePaper{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="SPECIAL_MANAGER"}
                    <option value="10"
                    {if isset($category) && ($category->internal_category eq 10)} selected="selected"{/if}>{t}Special{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="BOOK_MANAGER"}
                    <option value="15"
                    {if isset($category) && ($category->internal_category eq 15)} selected="selected"{/if}>{t}Book{/t}</option>
                    {/is_module_activated}
                    {acl isAllowed="MASTER"}
                    <option value="0"
                    {if isset($category) && ($category->internal_category eq 0)} selected="selected"{/if}>{t}Internal{/t}</option>
                    {/acl}
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
                  <div class="fileinput [%(categoryUrl)?'fileinput-exists':'fileinput-new'%]" data-trigger="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 140px; height: 140px;">
                    </div>
                    <div class="fileinput-exists fileinput-preview thumbnail" style="width: 140px; height: 140px;" ng-if="categoryUrl">
                        <img src="categoryUrl" style="max-width:200px;" >
                    </div>
                    <div>
                      <span class="btn btn-file">
                        <span class="fileinput-new">{t}Add new photo{/t}</span>
                        <span class="fileinput-exists">{t}Change{/t}</span>
                        <input type="file"/>
                        <input type="hidden" name="logo_path" class="file-input" value="1">
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
