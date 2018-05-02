{extends file="base/admin.tpl"}

{block name="content"}
<div ng-controller="CommentsConfigCtrl" ng-init="config = {json_encode($configs)|clear_json}"></div>
  <form action="{url name=admin_comments_config}" method="POST" id="formulario">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=admin_comments}" title="{t}Go back to list{/t}">
                  <i class="fa fa-comment"></i>
                  {t}Comments{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <div class="p-l-10 p-r-10 p-t-10">
                <i class="fa fa-angle-right"></i>
              </div>
            </li>
            <li class="quicklinks hidden-xs">
              <h5><strong>{t}Settings{/t}</strong></h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-success" type="submit">
                  <i class="fa fa-save"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      {include file="comment/partials/_config.tpl"}

      <div class="grid simple" ng-class="{ disabled: configs.disable_comments == '1' }">
        <div class="grid-body">
          <h4>
            <i class="fa fa-eye m-r-5"></i>
            {t}Visualization{/t}
          </h4>
          <div class="form-group p-l-20">
            <label class="form-label" for="config[number_elements]">
              {t}Number of comments to show{/t}
            </label>
            <div class="controls">
              <input id="name" name="configs[number_elements]" min=3 max=100 type="number"  value="{$configs['number_elements']|default:10}">
              <div class="help">{t}Number of comments to show by page{/t}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid simple">
        <div class="grid-body">
          <h4>{t}Comment moderation{/t}</h4>

            <div class="form-group">
              <div class="checkbox">
                <input id="moderation" name="configs[moderation_manual]" type="checkbox" value="1" {if $configs['moderation_manual'] !== true}checked="checked"{/if} >
                <label class="form-label" for="moderation">
                  {t}Manually moderation of comments{/t}
                </label>
                <div class="help p-l-25">
                  {t}An administrator must always approve a comment in order to make it publicly available.{/t}
                </div>
              </div>
            </div>

            <div class="row" ng-if="!configs.moderation_manual">
              <div class="col-sm-6">
                <h5>{t}Blacklist{/t}</h5>

                <div class="form-group">
                  <div class="checkbox">
                    <input id="moderation_autoaccept" name="configs[moderation_autoaccept]" type="checkbox" value="1" {if $configs['moderation_autoaccept'] == true}checked="checked"{/if} >
                    <label class="form-label" for="moderation_autoaccept">
                      {t escape=off}<strong>Auto-accept comments</strong> if they pass the blacklist rules{/t}
                    </label>
                    <div class="help p-l-25">
                      {t}When a comment is submitted and it passes the blacklist checks, then accept it without intervenction.{/t}
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="checkbox">
                    <input id="moderation_autoreject" name="configs[moderation_autoreject]" type="checkbox" value="1" {if $configs['moderation_autoreject'] == true}checked="checked"{/if} >
                    <label class="form-label" for="moderation_autoreject">
                      {t escape=off}<strong>Auto-reject comments</strong> if they don't pass the blacklist rules{/t}
                    </label>
                    <div class="help p-l-25">
                      {t}When a comment is submitted and it doesn't pass the blacklist checks, then reject it without intervenction.{/t}
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <h5>{t}Words and rules prohibited{/t}</h5>
                <div class="form-group">
                  <textarea name="configs[moderation_blacklist]" id="blacklist_rules" class="form-control" rows=10>{$configs['moderation_blacklist']}</textarea>
                  <div class="help">{t}List of words or regular expressions that are prohibited on comments, one per line.{/t}</div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </form>
{/block}

{block name="modals"}
  {include file="comment/modals/_modalChange.tpl"}
{/block}
