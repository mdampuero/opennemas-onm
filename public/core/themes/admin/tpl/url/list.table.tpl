{extends file="common/extension/list.table.tpl"}

{block name="columns"}{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" width="400">
    {t}Source{/t}
  </th>
  <th class="v-align-middle" width="400">
    {t}Target{/t}
  </th>
  <th class="text-center v-align-middle" width="200">
    {t}Type{/t}
  </th>
  <th class="text-center v-align-middle" width="150">
    <i class="fa fa-retweet" uib-tooltip="{t}Redirection{/t}" tooltip-placement="left"></i>
    <span ng-if="isHelpEnabled()">{t}Redirection{/t}</span>
  </th>
  <th class="text-center v-align-middle" width="150">
    <i class="fa fa-check" uib-tooltip="{t}Enabled{/t}" tooltip-placement="left"></i>
    <span ng-if="isHelpEnabled()">{t}Enabled{/t}</span>
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle">
    <div class="table-text">
      [% item.source %]
    </div>
    {block name="itemActions"}
      <div class="listing-inline-actions m-t-10">
        <a class="btn btn-default btn-small" href="[% routing.generate('backend_url_show', { id: item.id }) %]">
          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
        </a>
        <button class="btn btn-danger btn-small" ng-click="delete(item.id)" type="button">
          <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
        </button>
        <a class="btn btn-white btn-small" href="/[% item.source %]" ng-if="item.type == 1 || item.type == 2" target="_blank">
          <i class="fa fa-external-link m-r-5"></i>{t}Test{/t}
        </a>
      </div>
    {/block}
  </td>
  <td class="v-align-middle">
    <div class="table-text">
      <a href="[% routing.generate('admin_' + item.content_type + '_show', { id: item.target }) %]" ng-if="(item.type == 0 || item.type == 1 || item.type == 3) && ['advertisement', 'article', 'book', 'comment', 'keyword', 'letter', 'menu', 'photo', 'widget'].indexOf(item.content_type) !== -1">
        [% item.target %] ([% item.content_type %])
      </a>
      <a href="/[% item.target %]" ng-if="item.type == 2 || item.type == 4">
        [% item.target %]
      </a>
      <a href="[% routing.generate('backend_' + item.content_type + '_show', { id: item.target }) %]" ng-if="(item.type == 0 || item.type == 1 || item.type == 3) && ['advertisement', 'article', 'book', 'comment', 'keyword', 'letter', 'menu', 'photo', 'widget'].indexOf(item.content_type) === -1">
        [% item.target %] ([% item.content_type %])
      </a>
    </div>
  </td>
  <td class="text-center v-align-middle">
    <small>
      <i class="fa" ng-class="{ 'fa-file-text-o': item.type == 0, 'fa-code': item.type == 1 || item.type == 2, 'fa-asterisk': item.type > 2 }"></i>
      <strong ng-if="isHelpEnabled() && item.type == 0">{t}Content{/t}</strong>
      <strong ng-if="isHelpEnabled() && item.type == 1 || item.type == 2">URI</strong>
      <strong ng-if="isHelpEnabled() && item.type > 2">{t}Regex{/t}</strong>
      {t}to{/t}
      <i class="fa" ng-class="{ 'fa-file-text-o': item.type == 0 || item.type == 1 || item.type == 3, 'fa-code': item.type == 2 || item.type == 4 }"></i>
      <strong ng-if="isHelpEnabled() && (item.type == 0 || item.type == 1 || item.type == 3)">{t}Content{/t}</strong>
      <strong ng-if="isHelpEnabled() && (item.type == 2 || item.type == 4)">URI</strong>
    </small>
  </td>
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="patch(item, 'redirection', item.redirection != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.redirectionLoading, 'fa-exchange-alt text-error' : !item.redirectionLoading && item.redirection == 0, 'fa-retweet text-success': !item.redirectionLoading && item.redirection == 1 }"></i>
      <span class="badge text-uppercase text-bold" ng-class="{ 'badge-success': !item.redirection, 'badge-warning text-black': item.redirection }">
        [% item.redirection ? '301' : '200' %]
      </span>
    </button>
  </td>
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="patch(item, 'enabled', item.enabled != 1 ? 1 : 0)" type="button" uib-tooltip="[% !item.enabled ? '{t}Disabled{/t}' : '{t}Enabled{/t}' %]">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.enabledLoading, 'fa-check text-success' : !item.enabledLoading && item.enabled == 1, 'fa-times text-error': !item.enabledLoading && item.enabled == 0 }"></i>
    </button>
  </td>
{/block}
