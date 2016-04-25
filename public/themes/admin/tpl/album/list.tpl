{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-stack-overflow page-navbar-icon"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/745938-opennemas-c%C3%B3mo-crear-%C3%A1lbumes-galer%C3%ADas-de-imagene" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
              {t}Albums{/t}
            </h4>
          </li>
          <li class="quicklinks visible-xs">
            <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/745938-opennemas-c%C3%B3mo-crear-%C3%A1lbumes-galer%C3%ADas-de-imagene" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
              <i class="fa fa-question fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks dropdown hidden-xs">
            <div data-toggle="dropdown">
              {if $category == 'widget'}
                {t}Widget home{/t}
              {else}
                {t}Listing{/t}
              {/if}
              <span class="caret"></span>
            </div>
            <ul class="dropdown-menu">
              <li>
                <a href="{url name=admin_albums_widget}">
                  {t}Widget home{/t}
                </a>
              </li>
              <li>
                <a href="{url name=admin_albums}">
                  {t}Listing{/t}
                </a>
              </li>
            </ul>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="ALBUM_SETTINGS"}
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_albums_config}" title="{t}Config album module{/t}">
                <span class="fa fa-cog fa-lg"></span>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            {/acl}
            {acl isAllowed="ALBUM_WIDGET"}
            {if $category eq 'widget'}
            <li class="quicklinks">
              <button class="btn btn-white" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}" id="save_button">
                <i class="fa fa-save"></i>
                {t}Save positions{/t}
              </button>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            {/if}
            {/acl}
            {acl isAllowed="ALBUM_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_album_create}" title="{t}New album{/t}" id="create_button">
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
    {include file="album/partials/_album_list_home.tpl"}
  {else}
    {include file="album/partials/_album_list.tpl"}
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
</form>
{/block}
