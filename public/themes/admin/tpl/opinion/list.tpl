{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{url name=admin_opinions}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="OpinionListCtrl" ng-init="init('opinion', 'backend_ws_opinions_list')">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-quote-right"></i>
                {if $contentType eq 'blog'}
                  Posts
                {else}
                  {t}Opinions{/t}
                {/if}
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks dropdown hidden-xs">
              <div data-toggle="dropdown">
                {if $home}
                  {t}Opinion frontpage{/t}
                {else}
                  {t}Listing{/t}
                {/if}
                <span class="caret"></span>
              </div>
              <ul class="dropdown-menu">
                {acl isAllowed="OPINION_FRONTPAGE"}
                <li>
                  <a href="{url name=admin_opinions_frontpage}">
                    {t}Opinion frontpage{/t}
                  </a>
                </li>
                {/acl}
                <li>
                  <a href="{url name=admin_opinions}">
                    {t}Listing{/t}
                  </a>
                </li>
              </ul>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed="OPINION_SETTINGS"}
                <li class="quicklinks">
                  <a class="btn btn-link" href="{url name=admin_opinions_config}" title="{t}Config opinion module{/t}">
                    <i class="fa fa-cog fa-lg"></i>
                  </a>
                </li>
                <li class="quicklinks">
                  <span class="h-seperate"></span>
                </li>
              {/acl}
              {acl isAllowed="OPINION_FRONTPAGE"}
                {if $home}
                  <li>
                    <button class="btn btn-white" id="save_positions" ng-click="saveOpinionsFrontpage()" title="{t}Save positions{/t}" type="button">
                      <i class="fa fa-save"></i> <span class="hidden-xs">{t}Save positions{/t}</span>
                    </button>
                  </li>
                  <li class="quicklinks">
                    <span class="h-seperate"></span>
                  </li>
                {/if}
              {/acl}
              {acl isAllowed="OPINION_CREATE"}
                <li class="quicklinks">
                  <a class="btn btn-primary" href="{url name=admin_opinion_create}" title="{t}New opinion{/t}" id="create-button">
                    <i class="fa fa-plus"></i>
                    {t}Create{/t}
                  </a>
                </li>
              {/acl}
            </ul>
          </div>
        </div>
      </div>
    </div>
    {if $home}
      {include file="opinion/partials/_opinion_list_home.tpl"}
    {else}
      {include file="opinion/partials/_opinion_list.tpl"}
    {/if}
    <script type="text/ng-template" id="modal-delete">
      {include file="common/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
      {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-update-selected">
      {include file="common/modals/_modalBatchUpdate.tpl"}
    </script>
  </form>
{/block}
