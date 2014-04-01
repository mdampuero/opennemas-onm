<div class="spinner-wrapper" ng-if="loading">
    <div class="spinner"></div>
    <div class="spinner-text">{t}Loading{/t}...</div>
</div>
<table class="table table-hover table-condensed" ng-if="!loading">
    <thead>
        <th class="title">{t}Title{/t}</th>
        <th class="center" style="width:10px;"></th>
        <th class="right" style="width:10px;">{t}Actions{/t}</th>
    </thead>
    <tbody>
        <tr ng-if="shvs.contents.length == 0">
            <td class="empty" colspan="3">
                <div class="search-results">
                    <p>
                        <img src="{$params.IMAGE_DIR}/search/search-images.png">
                    </p>
                    {t escape="off"}Please fill the form for searching contents{/t}
                </div>
            </td>
        </tr>
        <tr ng-repeat="content in shvs.contents" ng-if="shvs.contents.length > 0">
            <td style="padding:10px;">
                <strong>[ [% content.content_type_l10n_name %] ] [% content.title %]</strong>
                <br>
                <img src="{$params.IMAGE_DIR}/tag_red.png" alt="" > [% content.metadata %]
                <br>
                <strong>{t}Category{/t}:</strong> [% content.category_name %]
                <br>
                <strong>{t}Created{/t}:</strong> [% content.created %]
            </td>
            <td class="center">
                <img src="{$params.IMAGE_DIR}trash.png" height="16px" alt="En Papelera" title="En Papelera" ng-if="content.in_litter == 1"/>
                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicada" title="Publicada" ng-if="content.in_litter != 1&& content.content_status == 1"/>
                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicada" title="Publicada" ng-if="content.in_litter != 1 && content.content_status == 0"/>
            </td>
            <td class="right">
                <div class="btn-group right">
                    <button class="btn" ng-click="edit(content.id, 'admin_' + content.content_type_name + '_show')" title="Editar" type="button">
                        <i class="icon-pencil"></i>
                    </button>
                </div>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <td colspan="3" class="center">
            <div class="pull-left">
                {t}Showing{/t} [% (shvs.page - 1) * shvs.elements_per_page %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total|number %]
            </div>
            <div class="pull-right">
                <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
            </div>
        </td>
    </tfoot>
</table>
