{extends file="common/extension/list.table.tpl"}
{block name="customColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_type_name'" type="checkbox">
    <label for="checkbox-published">
      {t}Content type{/t}
    </label>
  </div>
{/block}

{block name="customColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_type_name')" width="150">
    <span class="m-l-5">
      {t}Content type{/t}
    </span>
  </th>
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_type_name')">
    [% getContentTypeName(item.content_type_name) %]
  </td>
{/block}

{block name="itemActions"}
    <a class="btn btn-white btn-small" href="[%
                      item.content_type_name === 'obituary'
                          ? routing.generate('backend_obituaries_show', { id: item.pk_content })
                          : [ 'album', 'attachment', 'opinion', 'photo', 'poll', 'static_page', 'video', 'widget', 'article', 'letter', 'company' ].indexOf(item.content_type_name) != -1
                              ? routing.generate('backend_' + item.content_type_name + '_show', { id: item.pk_content })
                              : routing.generate('admin_' + item.content_type_name + '_show', { id: item.pk_content })
                  %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
    <i class="fa fa-pencil text-success_"></i>
  </a>
{/block}

