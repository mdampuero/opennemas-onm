{extends file="common/extension/list.table.tpl"}

{block name="columns"}{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" width="400">
    {t}Name{/t}
  </th>
  <th class="v-align-middle" width="200">
    {t}Slug{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="data.extra.locale.multilanguage" width="200">
    {t}Locale{/t}
  </th>
  <th class="text-center v-align-middle" class="text-center" width="120">
    {t}Contents{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle">
    <div class="table-text">
      [% item.name %]
    </div>
    <div class="listing-inline-actions btn-group">
      <a class="btn btn-white btn-small" href="[% routing.generate('backend_tag_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
        <i class="fa fa-pencil text-success_"></i>
      </a>
      <button class="btn btn-white btn-small" ng-click="delete(item.id)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
        <i class="fa fa-trash-o text-danger"></i>
      </button>
    </div>
  </td>
  <td class="v-align-middle">
    <div class="table-text">
      [% item.slug %]
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="data.extra.locale.multilanguage">
    <div class="table-text" ng-if="item.locale">
      [% data.extra.locale.available[item.locale] %]
    </div>
    <small class="text-italic" ng-if="!item.locale">
      &lt;{t}Any{/t}&gt;
    </small>
  </td>
  <td class="text-center">
    <span class="badge badge-default text-bold" ng-class="{ 'badge-danger': !data.extra.stats[getItemId(item)] || data.extra.stats[getItemId(item)] == 0 }">
      [% data.extra.stats[getItemId(item)] ? data.extra.stats[getItemId(item)] : 0 %]
    </span>
  </td>
{/block}
