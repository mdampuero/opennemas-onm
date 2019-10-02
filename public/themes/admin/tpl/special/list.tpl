{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-controller="ContentListCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-star m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                {t}Specials{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed="SPECIAL_SETTINGS"}
                <li class="quicklinks">
                  <a class="btn btn-link" href="{url name=admin_specials_config}" class="admin_add" title="{t}Config special module{/t}">
                    <span class="fa fa-cog fa-lg"></span>
                  </a>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
              {/acl}
              {acl isAllowed="SPECIAL_CREATE"}
                <li class="quicklinks">
                  <a class="btn btn-loading btn-success text-uppercase" href="{url name=admin_special_create}">
                    <span class="fa fa-plus m-r-5"></span>
                    {t}Create{/t}
                  </a>
                </li>
              {/acl}
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section pull-left">
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right" type="button">
                <i class="fa fa-arrow-left fa-lg"></i>
              </button>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <h4>
                [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
              </h4>
            </li>
          </ul>
          <ul class="nav quick-section pull-right">
            {acl isAllowed="SPECIAL_AVAILABLE"}
            <li class="quicklinks">
              <button class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" uib-tooltip="{t}Publish{/t}" tooltip-placement="bottom">
                <i class="fa fa-check fa-lg"></i>
              </button>
            </li>
            <li class="quicklinks">
              <button class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" uib-tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
                <i class="fa fa-times fa-lg"></i>
              </button>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks hidden-xs">
              <button class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')" uib-tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
                <i class="fa fa-home fa-lg"></i>
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <button class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')" uib-tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
                <i class="fa fa-home fa-lg"></i>
                <i class="fa fa-times fa-sub text-danger"></i>
              </button>
            </li>
            {/acl}
            {acl isAllowed="SPECIAL_DELETE"}
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <button class="btn btn-link" href="#" id="batch-delete" ng-click="sendToTrashSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
                <i class="fa fa-trash-o fa-lg"></i>
              </button>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
    {include file="special/partials/_special_list.tpl"}
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
