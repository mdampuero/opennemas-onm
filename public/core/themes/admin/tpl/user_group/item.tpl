{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}User Groups{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="UserGroupCtrl" ng-init="getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-users m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="[% routing.generate('backend_user_groups_list') %]">
    {t}User Groups{/t}
  </a>
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks">
        <a class="btn btn-link" ng-click="expansibleSettings()" title="{t 1=_('User group')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
          <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
          {t}Save{/t}
        </button>
      </li>
    </ul>
  </div>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <div class="checkbox">
          <input class="form-control" id="enabled" name="enabled" ng-model="item.enabled" ng-true-value="1" ng-false-value="0" type="checkbox">
          <label for="enabled" class="form-label">
            {t}Enabled{/t}
          </label>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.visibility }" ng-click="expanded.visibility = !expanded.visibility">
        <i class="fa fa-eye m-r-10"></i>{t}Visibility{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.visibility }"></i>
        <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.visibility">
          <span ng-show="item.private">{t}Private{/t}</span>
          <span ng-show="!item.private">{t}Public{/t}</span>
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.visibility }">
        <div class="form-group no-margin">
          <div class="checkbox">
            <input class="form-control" id="private" name="private" ng-false-value="0" ng-model="item.private" ng-true-value="1" type="checkbox">
            <label for="private" class="form-label">
              {t}Private{/t}
            </label>
          </div>
          <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
            <i class="fa fa-info-circle m-r-5 text-info"></i>
            {t}If enabled, this user group will not be visible in some circunstances{/t}
          </span>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="commonFields"}
  <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('visibility')">
    <input id="checkbox-visibility" checklist-model="app.fields[contentKey].selected" checklist-value="'visibility'" type="checkbox">
    <label for="checkbox-visibility">
      {t}Visibility{/t}
    </label>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="form-group">
        <label for="name" class="form-label">{t}Name{/t}</label>
        <div class="controls">
          <input class="form-control" id="name" name="name" ng-model="item.name" required type="text">
        </div>
      </div>
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-title">
      <h4>{t}Privileges{/t}</h4>
    </div>
    <div class="grid-body" id="privileges">
      <div class="checkbox check-default check-title">
        <input id="checkbox-all" ng-change="selectAll()" ng-checked="areAllSelected()" ng-model="selected.allSelected" type="checkbox">
        <label for="checkbox-all">
          <h5 class="semi-bold text-uppercase">{t}Toggle all privileges{/t}</h5>
        </label>
      </div>
      <div class="ng-cloak">
        <div ng-repeat="section in sections">
          <h5 class="m-t-30 semi-bold text-uppercase">[% section.title %]</h5>
          <div class="row" ng-repeat="columns in section.rows">
            <div class="col-sm-3" ng-repeat="name in columns">
              <div class="col-sm-12 m-b-10">
                <div class="checkbox check-default check-title">
                  <input id="checkbox-[% name %]" ng-change="selectModule(name)" ng-checked="isModuleSelected(name)" ng-model="selected.all[name]" type="checkbox">
                  <label for="checkbox-[% name %]">
                    <h5 class="semi-bold">[% data.extra.extensions[name] ? data.extra.extensions[name] : name %]</h5>
                  </label>
                </div>
              </div>
              <div class="col-sm-12 m-b-5" ng-repeat="privilege in data.extra.modules[name]">
                <div class="checkbox check-default">
                  <input id="checkbox-[% name + '-' + $index %]" checklist-model="item.privileges" checklist-value="privilege.id" type="checkbox">
                  <label for="checkbox-[% name + '-' + $index %]">
                    [% privilege.description %]
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}
