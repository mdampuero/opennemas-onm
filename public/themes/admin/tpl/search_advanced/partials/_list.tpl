<div class="spinner-wrapper" ng-if="loading">
    <div class="spinner"></div>
    <div class="spinner-text">{t}Loading{/t}...</div>
</div>
<table class="table table-hover no-margin" ng-if="!loading">
    <thead>
        <th class="title">{t}Title{/t}</th>
        <th class="center" style="width:10px;"></th>
        <th class="right" style="width:10px;">{t}Actions{/t}</th>
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
                <strong>[ [% content.content_type_l10n_name %] ] [% content.title %]</strong>
                <br>
                <img src="{$params.IMAGE_DIR}/tag_red.png" alt="" > [% content.metadata %]
                <br>
                <strong>{t}Category{/t}:</strong> [% content.category_name %]
                <br>
                <strong>{t}Created{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
            </td>
            <td class="center">
                <img src="{$params.IMAGE_DIR}trash.png" height="16px" alt="En Papelera" title="En Papelera" ng-if="content.in_litter == 1"/>
                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicada" title="Publicada" ng-if="content.in_litter != 1&& content.content_status == 1"/>
                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicada" title="Publicada" ng-if="content.in_litter != 1 && content.content_status == 0"/>
            </td>
            <td class="right">
                <div class="btn-group right">
                    <a class="btn" href="[% edit(content.id, 'admin_' + content.content_type_name + '_show') %]" title="Editar">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <td colspan="3" class="center">
            <div class="pagination-info pull-left" ng-if="contents.length > 0">
                {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total|number %]
            </div>
            <div class="pull-right" ng-if="contents.length > 0">
                <pagination class="no-margin" max-size="5" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_contents_list')" ng-model="pagination.page" total-items="pagination.total" num-pages="pages"></pagination>
            </div>
            <span ng-if="contents.length == 0">&nbsp;</span>
        </td>
    </tfoot>
</table>
