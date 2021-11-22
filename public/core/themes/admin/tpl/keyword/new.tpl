{extends file="base/admin.tpl"}

{block name="content"}
<form id="formulario" name="formulario" action="{if $keyword->id}{url name=api_v1_backend_keyword_update_item id=$keyword->id}{else}{url name=api_v1_backend_keyword_create_item}{/if}" method="POST">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-tags"></i>
              {t}Keywords{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{if isset($keyword->id)}{t}Editing keyword{/t}{else}{t}Creating keyword{/t}{/if}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=backend_keywords_list}" class="admin_add" value="{t}Go back{/t}" title="{t}Go back{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            {acl isAllowed="KEYWORD_CREATE"}
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <button class="btn btn-primary" type="submit" data-text="{t}Saving{/t}..." id="save-button">
                <span class="fa fa-save"></span>
                <span class="text">{t}Save{/t}</span>
              </button>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="grid simple">
      <div class="grid-body">
        <div class="col-md-7">
          <div class="form-group">
            <label class="form-label" for="keyword">{t}Name{/t}</label>
            <div class="controls">
              <input type="text" id="keyword" name="keyword" value="{$keyword->keyword|default:""}"
              class="form-control" size="30" maxlength="60" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="tipo">{t}Type{/t}</label>
            <div class="controls">
              <select name="tipo" id="tipo" required>
                {html_options options=$tipos selected=$keyword->tipo|default:""}
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="value">{t}Value{/t}</label>
            <div class="controls">
              <input type="text" id="value" name="value" value="{$keyword->value|default:""}" class="form-control" required>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
