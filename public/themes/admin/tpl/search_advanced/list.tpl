{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_search}" method="GET" ng-app="BackendApp" ng-controller="ContentListCtrl" ng-controller="ContentListCtrl" ng-init="init('content', { content_type_name: -1 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-search"></i>
              {t}Global search{/t}
            </h4>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" type="text" name="name" ng-model="criteria.title_like" placeholder="{t}Filter by title{/t}" />
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <select name="content_types[]" id="content_types" ng-model="criteria.content_type_name" class="select2" data-label="{t}Type{/t}"> <!-- multiple -->
              {html_options options=$content_types selected=$content_types_selected}
            </select>
          </li>
          <li class="quicklinks hidden-xs">
            <select class="select2 input-medium" name="status" ng-model="criteria.elements_per_page" data-label="{t}View{/t}">
              <option value="10a">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </li>
        </ul>
        <ul class="nav quick-section pull-right simple-pagination ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <span class="info">
              [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            </span>
          </li>
          <li class="quicklinks form-inline pagination-links">
            <div class="btn-group">
              <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
                <i class="fa fa-chevron-left"></i>
              </button>
              <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                <i class="fa fa-chevron-right"></i>
              </button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="content">

    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="loading-spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="table-wrapper ng-cloak">
          <table class="table table-hover no-margin" ng-if="!loading">
            <thead>
              <th class="title">{t}Title{/t}</th>
              <th class="left hidden-xs">{t}Created{/t}</th>
              <th class="right" style="width:10px;"></th>
            </thead>
            <tbody>
              <tr ng-if="contents.length == 0">
                <td class="empty" colspan="3">
                  <div class="search-results">
                    <p>
                      <img src="{$params.IMAGE_DIR}/search/search-images.png">
                    </p>
                    {t escape="off"}Please fill the form for searching contents{/t}
                  </div>
                </td>
              </tr>
              <tr ng-repeat="content in contents" ng-if="contents.length > 0">
                <td style="padding:10px;">
                  <strong>[% content.content_type_l10n_name %]</strong>  - [% content.title %]
                </td>
                <td class="left hidden-xs">
                  [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </td>
                <!-- <td class="center">
                  <span class="btn btn-white">
                    <img src="{$params.IMAGE_DIR}trash.png" height="16px" alt="En Papelera" title="En Papelera" ng-if="content.in_litter == 1"/>
                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicada" title="Publicada" ng-if="content.in_litter != 1&& content.content_status == 1"/>
                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicada" title="Publicada" ng-if="content.in_litter != 1 && content.content_status == 0"/>
                  </span>
                </td> -->
                <td class="right">
                  <div class="btn-group right">
                    <a class="btn btn-white" href="[% edit(content.id, 'admin_' + content.content_type_name + '_show') %]" title="Editar">
                      <i class="fa fa-pencil"></i>
                    </a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="grid-footer clearfix ng-cloak" ng-if="!loading">
          <div class="pagination-info pull-left" ng-if="contents.length > 0">
              {t}Showing{/t} [% ((pagination.page - 1) > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% pagination.page * pagination.epp %] {t}of{/t} [% pagination.total %]
          </div>
          <div class="pull-right pagination-wrapper" ng-if="contents.length > 0">
              <pagination class="no-margin" max-size="3" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
          </div>
      </div>
    </div>
  </div>

</form>
{/block}
