{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-pie-chart"></i>
              {t}Polls{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks dropdown hidden-xs">
            <div data-toggle="dropdown">
              {if $category == 'widget'}
              {t}Widget Home{/t}
              {else}
              {t}Listing{/t}
              {/if}
              <span class="caret"></span>
            </div>
            <ul class="dropdown-menu">
              <li>
                <a href="{url name=admin_polls_widget}">
                  {t}Widget Home{/t}
                </a>
              </li>
              <li>
                <a href="{url name=admin_polls}">
                  {t}Listing{/t}
                </a>
              </li>
            </ul>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="POLL_SETTINGS"}
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_polls_config}" title="{t}Config album module{/t}">
                <i class="fa fa-gear fa-lg"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            {/acl}
            {acl isAllowed="POLL_WIDGET"}
            {if $category eq 'widget'}
            <li class="quicklinks">
              <a class="btn btn-white" href="#" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}" id="save_positions_button">
                <i class="fa fa-save fa-lg"></i>
                {t}Save positions{/t}
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            {/if}
            {/acl}
            {acl isAllowed="POLL_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_poll_create}" title="{t}New poll{/t}" id="create_button">
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

  {if $category == 'widget'}
    {include file="poll/partials/_poll_list_home.tpl"}
  {else}
    {include file="poll/partials/_poll_list.tpl"}
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

</div>
{/block}
