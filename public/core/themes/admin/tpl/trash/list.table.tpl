{extends file="common/extension/list.table.tpl"}

{block name="commonColumns" prepend}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-media" checklist-model="app.columns.selected" checklist-value="'media'" type="checkbox">
    <label for="checkbox-media">
      {t}Media{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader" prepend}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('media')" width="80">
  </th>
{/block}

{block name="commonColumnsBody" prepend}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('media')">
    <i class="fa fa-lg fa-calendar" uib-tooltip="{t}Event{/t}" ng-if="item.content_type_name === 'event'"></i>
    <i class="fa fa-lg fa-envelope" uib-tooltip="{t}Letter{/t}" ng-if="item.content_type_name === 'letter'"></i>
    <i class="fa fa-lg fa-file" uib-tooltip="{t}Static Page{/t}" ng-if="item.content_type_name === 'static_page'"></i>
    <i class="fa fa-lg fa-file-o" uib-tooltip="{t}Article{/t}" ng-if="item.content_type_name === 'article'"></i>
    <i class="fa fa-lg fa-film" uib-tooltip="{t}Video{/t}" ng-if="item.content_type_name === 'video'"></i>
    <i class="fa fa-lg fa-newspaper-o" uib-tooltip="{t}NewsStand{/t}" ng-if="item.content_type_name === 'kiosko'"></i>
    <i class="fa fa-lg fa-paperclip" uib-tooltip="{t}File{/t}" ng-if="item.content_type_name === 'attachment'"></i>
    <i class="fa fa-lg fa-picture-o" uib-tooltip="{t}Image{/t}" ng-if="item.content_type_name === 'image'"></i>
    <i class="fa fa-lg fa-pie-chart" uib-tooltip="{t}Poll{/t}" ng-if="item.content_type_name === 'poll'"></i>
    <i class="fa fa-lg fa-puzzle-piece" uib-tooltip="{t}Widget{/t}" ng-if="item.content_type_name === 'widget'"></i>
    <i class="fa fa-lg fa-shield fa-flip-vertical" uib-tooltip="{t}Obituary{/t}" ng-if="item.content_type_name === 'obituary'"></i>
    <i class="fa fa-lg fa-quote" uib-tooltip="{t}Opinion{/t}" ng-if="item.content_type_name === 'opinion'"></i>
    <i class="fa fa-lg fa-camera" uib-tooltip="{t}Album{/t}" ng-if="item.content_type_name === 'album'"></i>
  </td>
{/block}

{block name="categoryColumn"}
  <a class="label label-default m-r-5 text-bold" href="[% routing.generate('backend_category_show', { id: category }) %]" ng-repeat="category in item.categories">
    [% (categories | filter: { id: category } : true)[0].title %]
  </a>
{/block}

{block name="itemActions"}
  <a class="btn btn-white btn-small" ng-click="restore(item.pk_content)" type="button" uib-tooltip="{t}Restore{/t}" tooltip-placement="top">
    <i class="fa fa-retweet text-success"></i>
  </a>
  <button class="btn btn-white btn-small" ng-click="delete(item.pk_content)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
    <i class="fa fa-trash-o text-danger"></i>
  </button>
{/block}
