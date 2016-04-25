{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-file-o"></i>
              {t}Files{/t}
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
                <a href="{url name=admin_files_widget}" {if $category=='widget'}class="active"{/if}>
                  {t}Widget Home{/t}
                </a>
              </li>
              <li>
                <a href="{url name=admin_files}" {if $category !=='widget'}class="active"{/if}>
                  {t}Listing{/t}
                </a>
              </li>
            </ul>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_files_statistics}">
                <span class="fa fa-bar-chart"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {if $category eq 'widget'}
            {acl isAllowed="ATTACHMENT_FRONTS"}
            <li class="quicklinks">
              <button class="btn btn-white" id="save-widget-positions" title="{t}Save positions{/t}" ng-click="savePositions('backend_ws_contents_save_positions')">
                <span class="fa fa-save"></span>
                {t}Save positions{/t}
              </button>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            {/acl}
            {/if}
            {acl isAllowed="ATTACHMENT_CREATE"}
            <li>
              <a class="btn btn-primary" href="{url name=admin_files_create}" title="{t}New file{/t}" id="create_button">
                <span class="fa fa-plus"></span>
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
    {include file="files/partials/_file_list_home.tpl"}
  {else}
    {include file="files/partials/_file_list.tpl"}
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
