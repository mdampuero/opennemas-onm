{extends file="common/extension/list.table.tpl"}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="400">
    {t}Title{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('start-date')" width="400">
    {t}Start Date{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('end-date')" width="400">
    {t}End date{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('position')" width="400">
    {t}Position{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('type')" width="80">
    {t}Type{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('published')" width="400">
    {t}Published{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
asdasdasdasd
{/block}

