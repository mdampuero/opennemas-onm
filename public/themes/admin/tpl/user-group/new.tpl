{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if isset($user_group->pk_user_group)}{url name="admin_acl_usergroups_update" id=$user_group->pk_user_group}{else}{url name="admin_acl_usergroups_save"}{/if}" method="post" ng-controller="UserGroupCtrl">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-users"></i>
              {t}User groups{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <h5>
              {if isset($user_group->pk_user_group)}{t}Editing group{/t}{else}{t}Creating user group{/t}{/if}
            </h5>
          </li>
        </ul>
      </div>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" href="{url name="admin_acl_usergroups"}" title="{t}Go back{/t}">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit" id="save-button">
              <i class="fa fa-save"></i>
              <span class="text">{t}Save{/t}</span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="grid simple">
      <div class="grid-body">
        <div class="form-group">
          <label for="name" class="form-label">{t}Group name{/t}</label>
          <div class="controls">
            <input type="text" id="name" name="name" value="{$user_group->name}" class="form-control" required
            {if $user_group->name eq $smarty.const.SYS_NAME_GROUP_ADMIN}disabled="disabled"{/if} />
          </div>
        </div>
      </div>
    </div>
    <div class="grid simple" ng-init="{if !empty($user_group)}user_group = {json_encode($user_group->getData())|clear_json}; {/if}extra.modules = {json_encode($modules)|clear_json}">
      <div class="grid-title">
        <h4>{t}Privileges{/t}</h4>
      </div>
      <div class="grid-body" id="privileges">
        <div class="checkbox check-default check-title">
          <input id="checkbox-all" ng-change="selectAll()" ng-checked="areAllSelected()" ng-model="selected.allSelected" type="checkbox">
          <label for="checkbox-all">
            <h5>{t}Toggle all privileges{/t}</h5>
          </label>
        </div>
        <div class="ng-cloak">
          <div ng-repeat="section in sections">
            <h5>{t}[% section.title %]{/t}</h5>
            <div class="row" ng-repeat="columns in section.rows">
              <div class="col-sm-3" ng-repeat="name in columns">
                <div class="col-sm-12 m-b-10">
                  <div class="checkbox check-default check-title">
                    <input id="checkbox-[% name %]" ng-change="selectModule(name)" ng-checked="isModuleSelected(name)" ng-model="selected.all[name]" type="checkbox">
                    <label for="checkbox-[% name %]">
                      <h5>[% name %]</h5>
                    </label>
                  </div>
                </div>
                <div class="col-sm-12 m-b-5" ng-repeat="privilege in extra.modules[name]">
                  <div class="checkbox check-default">
                    <input id="checkbox-[% name + '-' + $index %]" checklist-model="user_group.privileges" checklist-value="privilege.id" type="checkbox">
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
  </div>
  <input type="hidden" name="privileges" ng-value="permissions">
</form>
{/block}
