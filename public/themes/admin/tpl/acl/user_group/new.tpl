{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if isset($user_group->id)}{url name="admin_acl_usergroups_update" id=$user_group->id}{else}{url name="admin_acl_usergroups_create"}{/if}" method="post">
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
              {if isset($user_group->id)}{t}Editing group{/t}{else}{t}Creating user group{/t}{/if}
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
            <input type="text" id="name" name="name" value="{$user_group->name}" class="form-control" required="required"
            {if $user_group->name eq $smarty.const.SYS_NAME_GROUP_ADMIN}disabled="disabled"{/if} />
          </div>
        </div>
      </div>
    </div>

    <div class="grid simple">
      <div class="grid-title">
        <h4>{t}Privileges{/t}</h4>
      </div>
      <div class="grid-body" id="privileges">
        {foreach $modules as $mod => $privileges}
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading" id="accordion_{$privileges@index}" data-toggle="collapse">
                <h4 class="panel-title">
                  <a data-toggle="collapse" data-parent="#accordion_{$privileges@index}" href="#module{$privileges@index}">
                    {$mod} ({$total_activated[{$mod}]|default:0})
                  </a>
                </h4>
              </div>
              <div id="module{$privileges@index}" class="panel-collapse collapse in">
                <div class="panel-body">
                  {foreach $privileges as $privilege}
                  <div class="col-xs-12 col-md-6 col-lg-4">
                    <div class="checkbox check-default">
                      <input id="checkbox_{$privileges@index}{$privilege@index}" type="checkbox" name="privileges[]" value="{$privilege->id}" {if $user_group->containsPrivilege($privilege->id)}checked="checked"{/if}>
                      <label for="checkbox_{$privileges@index}{$privilege@index}">
                        {t}{$privilege->description}{/t}
                      </label>
                    </div>
                  </div>
                  {/foreach}
                </div>
              </div>
            </div>
          </div>
        {/foreach}
      </div>
    </div>
  </div>
  <input type="hidden" name="id" value="{$user_group->id}">
</form>
{/block}
