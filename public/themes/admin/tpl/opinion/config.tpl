{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=backend_opinions_config}" method="POST" name="formulario" id="formulario">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-quote-right"></i>
              {t}Opinions{/t}
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
              <a class="btn btn-link" href="{url name=backend_opinions_list}" title="{t}Go back to list{/t}">
                <i class="fa fa-reply"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <button class="btn btn-primary" type="submit" data-text="{t}Saving{/t}...">
                <span class="fa fa-save"></span>
                <span class="text">{t}Save{/t}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="grid simple">
      <div class="grid-body ng-cloak">
        <div class="row">
          {acl isAllowed="MASTER"}
            <div class="col-md-6" ng-init="extraFields = {json_encode($extra_fields)|escape:"html"}">
              <autoform-editor ng-model="extraFields"/>
            </div>
            <input id="extra-fields" name="extra-fields" type="hidden" value="[% extraFields %]">
          {/acl}
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
