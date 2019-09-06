{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="TagCtrl" ng-init="getItem({$id})">
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
            <li class="quicklinks m-l-5 m-r-5">
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
          <div class="ng-cloak pull-right" ng-if="!flags.http.loading">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="form.$invalid" type="button">
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
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && item === null">
        <div class="text-center p-b-15 p-t-15">
          <a href="[% routing.generate('backend_tags_list') %]">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t}Unable to find the item{/t}</h3>
            <h4>{t}Click here to return to the list{/t}</h4>
          </a>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!flags.http.loading && item">
        <div class="row">
          <div class="col-md-8">
            <div class="grid simple">
              <div class="grid-body">
                <div class="row">
                  <div class="col-md-6 form-group">
                    {include file="ui/component/input/text.tpl" iField="name" iFlag="validating" iNgActions="ng-blur=\"generate()\" ng-change=\"isValid()\"" iRequired="true" iTitle="{t}Name{/t}" iValidation=true}
                  </div>
                  <div class="col-md-6">
                    {include file="ui/component/input/slug.tpl" iField="slug" iFlag="slug" iRequired="true" iTitle="{t}Slug{/t}" iValidation=true}
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6 form-group" ng-if="config.locale.multilanguage">
                    <label class="form-label" for="locale">
                      {t}Language{/t}
                    </label>
                    <div class="controls">
                      <select class="form-control" name="locale" ng-model="item.locale">
                        <option value="">{t}Any{/t}</option>
                        <option value="[% id %]" ng-repeat="(id, name) in config.locale.available">[% name %]</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group no-margin">
                  <label class="form-label" for="description">
                    {t}Description{/t}
                  </label>
                  <div class="controls">
                    <textarea onm-editor onm-editor-preset="simple" ng-model="item.description" name="description" cols="30" rows="10"></textarea>
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
