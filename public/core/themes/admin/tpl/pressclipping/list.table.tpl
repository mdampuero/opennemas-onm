{extends file="common/extension/list.table.tpl"}

{block name="itemActionsWrapper"}
{/block}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('title')">
    <input id="checkbox-title" checklist-model="app.columns.selected" checklist-value="'title'" type="checkbox">
    <label for="checkbox-title">
      {t}Title{/t}
    </label>
  </div>
{/block}

{block name="customColumns"}
    <div class="checkbox column-filters-checkbox">
      <input id="status" checklist-model="app.columns.selected" checklist-value="'status'" type="checkbox">
      <label for="status">
        {t}Status{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="send_date" checklist-model="app.columns.selected" checklist-value="'send_date'" type="checkbox">
      <label for="send_date">
        {t}Send date{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="actions" checklist-model="app.columns.selected" checklist-value="'actions'" type="checkbox">
      <label for="actions">
        {t}Actions{/t}
      </label>
    </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="400">
    {t}Title{/t}
  </th>
{/block}

{block name="customColumnsHeader"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('status')">
      {t}Status{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('send_date')">
      {t}Send date{/t}
    </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle" ng-if="isColumnEnabled('title')">
    <div class="table-text">
      <a class="text-black" href="[% routing.generate('backend_article_show', { id: item.fk_content }) %]"">[% item.title %]</a>
    </div>
  </td>
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('status')">
    <span class="ng-cloak badge badge-default" ng-class="{ 'badge-danger': item.status == 2, 'badge-warning': item.status == 0, 'badge-success' : item.status == 1 }">
      <strong ng-if="item.pressclipping_status !== 'Not sended'">
        {t}Sent{/t}
      </strong>
      <strong ng-if="item.pressclipping_status === 'Not sended'">
        {t}Not sent{/t}
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('send_date')">
    <span class="ng-cloak badge badge-default">
      <strong>
        [% item.pressclipping_sended %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('actions')">
    <strong>
      <a class="btn btn-primary btn-sm" ng-click="removeData(item.fk_content)">
          <i class="fa fa-trash"></i>
      </a>
    </strong>
  </td>
{/block}


