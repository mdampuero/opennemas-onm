{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-film"></i>
              {t}Videos{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks dropdown hidden-xs">
            <div data-toggle="dropdown">
              {if $category == 'widget'}
                {t}Widget home{/t}
              {elseif $category == 'all'}
                {t}Listing{/t}
              {else}
                {$datos_cat[0]->title}
              {/if}
              <span class="caret"></span>
            </div>
            <ul class="dropdown-menu">
              <li>
                <a href="{url name=admin_videos_widget}" {if $category=='widget'}class="active"{/if}>
                  {t}Widget home{/t}
                </a>
              </li>
              <li>
                <a href="{url name=admin_videos}" {if $category != 'widget'}class="active"{/if}>
                  {t}Listing{/t}
                </a>
              </li>
            </ul>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="VIDEO_SETTINGS"}
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_videos_config}" class="admin_add" title="{t}Config video module{/t}">
                <span class="fa fa-cog fa-lg"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {/acl}
            {acl isAllowed="VIDEO_WIDGET"}
            {if $category eq 'widget'}
            <li class="quicklinks">
              <button class="btn btn-white" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}">
                {t}Save positions{/t}
              </button>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {/if}
            {/acl}
            {acl isAllowed="VIDEO_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_videos_create}" accesskey="N" tabindex="1" id="create-button">
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
    {include file="video/partials/_video_list_home.tpl"}
  {else}
    {include file="video/partials/_video_list.tpl"}
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
