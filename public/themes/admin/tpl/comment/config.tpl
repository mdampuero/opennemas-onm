{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{url name=admin_comments_config}" method="POST">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-pie-chart"></i>
                {t}Comments{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>{t}Settings{/t}</h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_comments}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <a class="btn btn-link change" data-controls-modal="modal-comment-change" href="#" title="{t}Change comments module{/t}">
                  <i class="fa fa-refresh"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" type="submit">
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
      <div class="grid simple">
        <div class="grid-body">
          <div class="form-group">
            <label class="form-label" for="config[number_elements]">
              {t}Display{/t}
            </label>
            <span class="help">
              {t}Number of comments to show by page{/t}
            </span>
            <div class="controls">
              <input id="name" name="configs[number_elements]" type="number" value="{$configs['number_elements']|default:10}">
            </div>
          </div>
          <div class="form-group">
            <div class="checkbox">
              <input id="moderation" name="configs[moderation]" type="checkbox" value="1" {if $configs['moderation'] == true}checked="checked"{/if} >
              <label class="form-label" for="moderation">
                {t}Before a comment appears{/t}
              </label>
              <span class="help">
                {t}An administrator must always approve the comment {/t}
              </span>
            </div>
          </div>
        </div>
      </div>
      {include file="comment/partials/_config.tpl"}
    </div>
  </form>
{/block}

{block name="modals"}
  {include file="comment/modals/_modalChange.tpl"}
{/block}
