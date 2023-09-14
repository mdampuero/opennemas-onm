{extends file="common/extension/list.table.tpl"}

{block name="columns"}{/block}

{block name="commonColumnsHeader"}
  <th class="text-center v-align-middle" width="80">
    <i class="fa fa-picture-o"></i>
  </th>
  <th class="v-align-middle" width="400">
    {t}Name{/t}
  </th>
  <th class="v-align-middle" width="400">
    {t}Email{/t}
  </th>
  <th class="text-center v-align-middle" width="80">
    {t}Blog{/t}
  </th>
  <th class="v-align-middle" width="400">
    {t}Biography{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="text-center v-align-middle">
    <dynamic-image class="img-thumbnail img-thumbnail-circle" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[item.avatar_img_id].path" transform="thumbnail,50,50" ng-if="item.avatar_img_id && item.avatar_img_id != 0"></dynamic-image>
  </td>
  <td class="v-align-middle">
    <div class="table-text">
      [% item.name %]
    </div>
    {block name="itemActions"}
      <div class="listing-inline-actions m-t-10 btn-group">
        {acl isAllowed="AUTHOR_UPDATE"}
          <a class="btn btn-white btn-small" href="[% routing.generate('backend_author_show', { id:  item.id }) %]" title="{t}Edit{/t}" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
            <i class="fa fa-pencil"></i>
          </a>
        {/acl}
        {acl isAllowed="AUTHOR_DELETE"}
          <button class="btn btn-white btn-small" ng-click="delete(item.id)" ng-if="backup.master || item.id != backup.id" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        {/acl}
      </div>
    {/block}
  </td>
  <td class="v-align-middle">
    <div class="table-text">
      [% item.email%]
    </div>
  </td>
  <td class="text-center v-align-middle">
    <i class="fa" ng-class="{ 'fa-check text-success': item.is_blog == 1, 'fa-times text-danger': !item.is_blog || item.is_blog == 0 }"></i>
  </td>
  <td class="v-align-middle">
    <div class="table-text" ng-if="item.bio">
      <span ng-if="item.is_blog == 1">
        <strong>Blog:</strong>
      </span>
      [% item.bio %]
    </div>
    <span class="text-italic" ng-if="!item.bio">
      {t}No biography{/t}
    </span>
  </td>
{/block}
