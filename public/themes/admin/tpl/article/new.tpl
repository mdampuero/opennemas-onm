{extends file="base/admin.tpl"}

{block name="content"}
  <form name="articleForm" ng-controller="ArticleCtrl" ng-init="init('{$locale}', '{$id}')" novalidate>
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-file-text-o page-navbar-icon"></i>
                <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/220778-primeros-pasos-en-opennemas-c%C3%B3mo-crear-un-art%C3%ADcu" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                  <i class="fa fa-question"></i>
                </a>
                {t}Articles{/t}
              </h4>
            </li>
            <li class="quicklinks visible-xs">
              <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/220778-primeros-pasos-en-opennemas-c%C3%B3mo-crear-un-art%C3%ADcu" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question fa-lg"></i>
              </a>
            </li>
            <li class="quicklinks seperate hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>
                {if !isset($id)}{t}Creating article{/t}{else}{t}Editing article{/t}{/if}
              </h5>
            </li>
            <li class="quicklinks seperate hidden-xs ng-cloak" ng-if="config.multilanguage">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks ng-cloak" ng-if="config.multilanguage">
              <translator keys="data.extra.keys" ng-model="config.locale" options="data.extra.options"></translator>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks hidden-xs ng-cloak" ng-if="draftSaved">
                <h5>
                  <i class="p-r-15">
                    <i class="fa fa-check"></i>
                    {t}Draft saved at {/t}[% draftSaved %]
                  </i>
                </h5>
              </li>
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_articles}" title="{t}Go back{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks hidden-xs">
                <button class="btn btn-white" id="button_preview" ng-click="preview('admin_article_preview', 'admin_article_get_preview')" type="button">
                  <i class="fa fa-desktop" ng-class="{ 'fa-circle-o-notch fa-spin': flags.preview }" ></i>
                  <span class="hidden-xs">{t}Preview{/t}</span>
                </button>
              </li>
              <li class="quicklinks hidden-xs">
                <span class="h-seperate"></span>
              </li>
              {if isset($id)}
                {acl isAllowed="ARTICLE_UPDATE"}
                  <li class="quicklinks">
                    <button class="btn btn-loading btn-primary" ng-click="save()" ng-disabled="flags.saving" type="button">
                      <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': flags.saving }"></i>
                      <span class="text">{t}Update{/t}</span>
                    </button>
                  </li>
                {/acl}
              {else}
                {acl isAllowed="ARTICLE_CREATE"}
                  <li class="quicklinks">
                    <button class="btn btn-loading btn-primary" ng-click="save()" ng-disabled="flags.saving" type="button">
                      <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': flags.saving }"></i>
                      <span class="text">{t}Save{/t}</span>
                    </button>
                  </li>
                {/acl}
              {/if}
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content ng-cloak no-animate" ng-if="flags.loading">
      <div class="spinner-wrapper">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
    </div>
    <div class="content ng-cloak" ng-if="!error && !flags.loading && article">
      <div class="row">
        <div class="col-md-8">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group" ng-class="{ 'has-error': showRequired && articleForm.title.$invalid }">
                <label class="form-label" for="title">
                  {t}Title{/t} *
                </label>
                <div class="controls">
                  <div class="input-group">
                    <input class="form-control" id="title" name="title" ng-model="article.title" ng-trim="false" placeholder="[% data.article.title[data.extra.options.default] %]" required tooltip-enable="config.locale != data.extra.options.default" tooltip-trigger="focus" type="text" uib-tooltip="{t}Original{/t}: [% data.article.title[data.extra.options.default] %]">
                    <span class="input-group-addon">
                      <span class="ng-cloak" ng-class="{ 'text-warning': article.title.length >= 50 && article.title.length < 80, 'text-danger': article.title.length >= 80 }">
                        [% article.title ? article.title.length : 0 %]
                      </span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group" ng-class="{ 'has-error': showRequired && articleForm.title_int.$invalid }">
                <label class="form-label" for="title-int">
                  {t}Inner title{/t} *
                </label>
                <div class="controls">
                  <div class="input-group">
                    <input class="form-control" id="title-int" maxlength="256" name="title_int" ng-model="article.title_int" ng-trim="false" placeholder="[% data.article.title_int[data.extra.options.default] %]" required tooltip-enable="config.locale != data.extra.options.default" tooltip-trigger="focus" type="text" uib-tooltip="{t}Original{/t}: [% data.article.title_int[data.extra.options.default] %]">
                    <span class="input-group-addon">
                      <span class="ng-cloak" ng-class="{ 'text-warning': article.title_int.length >= 50 && article.title_int.length < 100, 'text-danger': article.title_int.length >= 100 }">
                        [% article.title_int ? article.title_int.length : 0 %]
                      </span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-sm-4">
                  <label class="form-label" for="agency">
                    {t}Signature{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="agency" name="agency" ng-model="article.agency" ng-init="!article.id ? article.agency = '{setting name=site_agency}' : ''" type="text">
                  </div>
                </div>
                {is_module_activated name="ADVANCED_ARTICLE_MANAGER"}
                <div class="form-group col-sm-4">
                  <label class="form-label" for="agency_bulletin">
                    {t}Signature{/t} #2
                  </label>
                  <div class="controls">
                    <input class="form-control" id="agency_bulletin" ng-model="article.params.agencyBulletin" ng-init="!article.id ? article.params.agencyBulletin = '{setting name=site_agency}' : ''" type="text">
                  </div>
                </div>
                {/is_module_activated}
              </div>
              <div class="form-group">
                <label class="form-label" for="pretitle">
                  {t}Pretitle{/t}
                </label>
                <div class="controls">
                  <div class="input-group" id="pretitle">
                    <input class="form-control" name="pretitle" ng-model="article.pretitle" ng-trim="false" placeholder="[% data.article.pretitle[data.extra.options.default] %]" tooltip-enable="config.locale != data.extra.options.default" tooltip-trigger="focus" type="text" uib-tooltip="{t}Original{/t}: [% data.article.pretitle[data.extra.options.default] %]">
                    <span class="input-group-addon">
                      <span class="ng-cloak" ng-class="{ 'text-warning': article.pretitle.length >= 50 && article.pretitle.length < 100, 'text-danger': article.pretitle.length >= 100 }">
                        [% article.pretitle ? article.pretitle.length : 0 %]
                      </span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label clearfix" for="summary">
                  <div class="pull-left">
                    {t}Summary{/t}
                  </div>
                </label>
                {acl isAllowed='PHOTO_ADMIN'}
                <div class="pull-right">
                  <div class="btn btn-default btn-mini" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="5" media-picker-target="editor.summary">
                    {t}Insert image{/t}
                  </div>
                </div>
                {/acl}
                <div class="controls">
                  <textarea class="form-control" onm-editor onm-editor-preset="simple" id="summary" name="summary" ng-model="article.summary" rows="5"></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label clearfix" for="body">
                  <div class="pull-left">{t}Body{/t}</div>
                </label>
                {acl isAllowed='PHOTO_ADMIN'}
                <div class="pull-right">
                  <div class="btn btn-default btn-mini" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="5" media-picker-target="editor.body">
                    {t}Insert image{/t}
                  </div>
                </div>
                {/acl}
                <div class="controls">
                  <textarea name="body" id="body" ng-model="article.body" onm-editor onm-editor-preset="standard"  class="form-control" rows="15"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-12">
              <div class="grid simple">
                <div class="grid-body">
                  {acl isAllowed="ARTICLE_AVAILABLE"}
                    <div class="form-group">
                      <div class="checkbox">
                        <input id="content_status" name="content_status" ng-model="article.content_status" ng-false-value="0" ng-true-value="1" type="checkbox">
                        <label for="content_status">
                          {t}Published{/t}
                        </label>
                      </div>
                    </div>
                  {/acl}
                  {is_module_activated name="COMMENT_MANAGER"}
                    <div class="form-group">
                      <div class="checkbox">
                        <input id="with_comment" name="with_comment" ng-model="article.with_comment" ng-false-value="0" ng-true-value="1" type="checkbox">
                        <label class="form-label" for="with_comment">
                          {t}Allow comments{/t}
                        </label>
                      </div>
                    </div>
                  {/is_module_activated}
                  {acl isAllowed="ARTICLE_HOME"}
                    <div class="form-group">
                      <div class="checkbox">
                        <input id="frontpage" name="frontpage" ng-model="article.frontpage" ng-false-value="0" ng-true-value="1" type="checkbox">
                        <label class="form-label" for="frontpage">
                          {t}Suggested for frontpage{/t}
                        </label>
                      </div>
                    </div>
                  {/acl}
                  <div class="form-group">
                    <label class="form-label" for="fk_author">
                      {t}Author{/t}
                    </label>
                    <div class="controls">
                      {acl isAllowed="CONTENT_OTHER_UPDATE"}
                        <div class="form-group">
                          <div class="controls">
                            <ui-select class="form-control" name="author" theme="select2" ng-model="article.fk_author">
                              <ui-select-match>
                                [% $select.selected.name %]
                              </ui-select-match>
                              <ui-select-choices repeat="item.id as item in data.extra.users | filter: { name: $select.search }">
                                <div ng-bind-html="item.name | highlight: $select.search"></div>
                              </ui-select-choices>
                            </ui-select>
                          </div>
                        </div>
                      {aclelse}
                          <input type="hidden" name="fk_author" ng-model="article.fk_author" ng-init="article.fk_author ? article.fk_author: article.fk_author = {$app.user->id}">
                      {/acl}
                    </div>
                  </div>
                  <div class="form-group" ng-class="{ 'has-error': showRequired && !article.pk_fk_content_category }">
                    <label class="form-label" for="category">
                      {t}Category{/t} *
                    </label>
                    <div class="controls">
                      <ui-select class="form-control" name="pk_fk_content_category" theme="select2" ng-model="article.pk_fk_content_category">
                        <ui-select-match>
                          [% $select.selected.title %]
                        </ui-select-match>
                        <ui-select-choices group-by="groupCategories" repeat="item.pk_content_category as item in categories | filter: { title: $select.search }">
                          <div ng-bind-html="item.title | highlight: $select.search"></div>
                        </ui-select-choices>
                      </ui-select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="metadata">
                      {t}Tags{/t}
                    </label>
                    <div class="controls">
                      <tags-input ng-model="article.metadata" min-length="2" placeholder="{t}Write a tag and press Enter...{/t}"></tags-input>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="slug">
                      {t}Slug{/t}
                    </label>
                    <span class="m-t-2 pull-right" ng-if="article.pk_article && backup.content_status != '0' && !articleForm.pk_fk_content_category.$dirty && !articleForm.content_status.$dirty">
                      <a href="{$smarty.const.INSTANCE_MAIN_DOMAIN}/[% data.extra.options.default === config.locale ? '' : config.locale + '/' %]articulo/[% (categories | filter: { pk_content_category: article.pk_fk_content_category })[0].name %]/[% article.slug %]/[% article.created | moment : 'YYYYMMDDHHmmss' %][% article.pk_content.toString().length < 6 ? ('000000' + article.pk_content).substr(-6) : article.pk_content %].html" target="_blank">
                        <i class="fa fa-external-link"></i>
                        {t}Link{/t}
                      </a>
                    </span>
                    <div class="controls">
                      <input class="form-control" id="slug" name="slug" ng-model="article.slug" type="text" ng-disabled="article.content_status != '0'">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="bodyLink">
                      {t}External link{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="bodyLink" ng-model="article.params.bodyLink" type="text">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="grid simple">
                    <div class="grid-title">
                      <h4>
                        <i class="fa fa-clock-o m-r-10"></i>{t}Schedule{/t}
                      </h4>
                    </div>
                    <div class="grid-body">
                      <div class="form-group">
                        <label class="form-label" for="starttime">
                          {t}Publication start date{/t}
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <input class="form-control" datetime-picker datetime-picker-timezone="{$timezone}" datetime-picker-min="article.created" datetime-picker-max="article.endtime" datetime-picker-use-current="true" id="starttime" name="starttime" ng-model="article.starttime" type="datetime">
                            <span class="input-group-addon add-on">
                              <span class="fa fa-calendar"></span>
                            </span>
                          </div>
                          <span class="help-block">
                            {t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}
                          </span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="endtime">
                          {t}Publication end date{/t}
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <input class="form-control" datetime-picker datetime-picker-timezone="{$timezone}" datetime-picker-min="article.starttime" id="endtime" name="endtime" ng-model="article.endtime" type="datetime">
                            <span class="input-group-addon add-on">
                              <span class="fa fa-calendar"></span>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {is_module_activated name="CONTENT_SUBSCRIPTIONS"}
            <div class="row">
              <div class="col-md-12">
                <div class="grid simple">
                  <div class="grid-title">
                    <h4>
                      <a href="[% routing.generate('backend_subscriptions_list') %]">
                        <i class="fa fa-check-square-o m-r-10"></i>{t}Subscriptions{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="grid-body">
                    <div ng-show="!data.extra.subscriptions || data.extra.subscriptions.length === 0">
                      <i class="fa fa-warning m-r-5 text-warning"></i>
                      {t escape=off}There are no enabled <a href="[% routing.generate('backend_subscriptions_list') %]">subscriptions</a>{/t}
                    </div>
                    <div class="form-group no-margin" ng-show="data.extra.subscriptions && data.extra.subscriptions.length > 0">
                      <div class="checkbox m-b-5" ng-repeat="subscription in data.extra.subscriptions">
                        <input checklist-model="article.subscriptions" checklist-value="subscription.pk_user_group" id="checkbox-[% $index %]" type="checkbox">
                        <label for="checkbox-[% $index %]">[% subscription.name %]</label>
                      </div>
                      <div class="help m-l-3" ng-if="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}The content will be fully available only for subscribers in the selected subscriptions{/t}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          {/is_module_activated}
        </div>
      </div>
      {include  file="article/partials/_images.tpl"}
      <div id="related-contents">
        {include file ="article/partials/_related_list.tpl"}
      </div>
      <div class="row" ng-if="fieldsByModule !== undefined && fieldsByModule">
        <div class="col-md-12">
          <div class="grid simple">
            <div class="grid-title">
              <h4>
                <i class="fa fa-magic"></i>
                {t}Additional data{/t}
              </h4>
            </div>
            <div class="grid-body">
              <autoform ng-model="article" fields-by-module="fieldsByModule"/>
            </div>
          </div>
        </div>
      </div>
      {is_module_activated name="CRONICAS_MODULES"}
        {include file ="article/partials/_article_advanced_customize.tpl"}
      {/is_module_activated}
      <input type="hidden" id="action" name="action" value="{$action}" />
    </div><!-- /wrapper-content contentform -->
    <script type="text/ng-template" id="modal-preview">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()" type="button">&times;</button>
        <h4 class="modal-title">
          {t}Preview{/t}
        </h4>
      </div>
      <div class="modal-body clearfix no-padding">
        <iframe ng-src="[% template.src %]" frameborder="0"></iframe>
      </div>
    </script>
    <script type="text/ng-template" id="modal-draft">
      {include file="article/modal/_draft.tpl"}
    </script>
    <script type="text/ng-template" id="modal-translate">
      {include file="common/modals/_translate.tpl"}
    </script>
  </form>
{/block}
