{extends file="common/extension/list.table.tpl"}

{block name="itemActions"}
    <a class="btn btn-white" href="[%
                      item.content_type_name === 'obituary'
                          ? routing.generate('backend_obituaries_show', { id: item.pk_content })
                          : [ 'album', 'attachment', 'opinion', 'photo', 'poll', 'static_page', 'video', 'widget', 'article', 'letter', 'company' ].indexOf(item.content_type_name) != -1
                              ? routing.generate('backend_' + item.content_type_name + '_show', { id: item.pk_content })
                              : routing.generate('admin_' + item.content_type_name + '_show', { id: item.pk_content })
                  %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
    <i class="fa fa-pencil text-success_"></i>
  </a>
{/block}

