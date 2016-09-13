{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts src="@Common/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"}
    <script>
      var draftSavedMsg = '{t}A draft was saved at {/t}';

      jQuery(document).ready(function($){
        $('#title_input, #category').on('change', function() {
          var title = $('#title_input');
          var category = $('#category option:selected');
          var metaTags = $('#metadata');

          // Fill tags from title and category
          if (!metaTags.val()) {
            var tags = title.val();

            if (category.data('name')) {
              tags += " " + category.data('name');
            }
            fill_tags(tags, '#metadata', '{url name=admin_utils_calculate_tags}');
          }
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form name="articleForm" ng-controller="ArticleCtrl" ng-init="{if isset($article->id)}article = {json_encode($article)|clear_json}; {/if}checkDraft()" novalidate>
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
                {if !isset($article->id)}{t}Creating article{/t}{else}{t}Editing article{/t}{/if}
              </h5>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="draftSaved">
              <h5>
                <small class="p-l-10">
                  [% draftSaved %]
                </small>
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
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
                  <i class="fa fa-desktop" ng-class="{ 'fa-circle-o-notch fa-spin': loading }" ></i>
                  <span class="hidden-xs">{t}Preview{/t}</span>
                </button>
              </li>
              <li class="quicklinks hidden-xs">
                <span class="h-seperate"></span>
              </li>
              {if isset($article->id)}
              {acl isAllowed="ARTICLE_UPDATE"}
              <li class="quicklinks">
                <button class="btn btn-loading btn-primary" ng-click="update()" ng-disabled="saving || articleForm.$invalid" type="button" id="update-button">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i>
                  <span class="text">{t}Update{/t}</span>
                </button>
              </li>
              {/acl}
              {else}
              {acl isAllowed="ARTICLE_CREATE"}
              <li class="quicklinks">
                <button class="btn btn-loading btn-primary" ng-click="save()" ng-disabled="saving || articleForm.$invalid" type="button" id="save-button">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i>
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
    <div class="content">
     <div class="row">
        <div class="col-md-8">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <label class="form-label" for="title">
                  {t}Title{/t}
                </label>
                <div class="controls">
                  <div class="input-group" id="title">
                    <input class="form-control" id="title_input" name="title" ng-model="article.title" ng-trim="false" required="required" type="text" value="{$article->title|clearslash|escape:"html"}"/>
                    <span class="input-group-addon">
                      <span class="ng-cloak" ng-class="{ 'text-warning': title.length >= 50 && title.length < 80, 'text-danger': title.length >= 80 }">
                        [% title.length %]
                      </span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="title_int_input">
                  {t}Inner title{/t}
                </label>
                <div class="controls">
                  <div class="input-group" id="title_int">
                    <input class="form-control" id="title_int_input" maxlength="256" type="text" name="title_int" ng-model="article.title_int" ng-trim="false" value="{$article->title_int|clearslash|escape:"html"|default:$article->title}" required="required" />
                    <span class="input-group-addon">
                      <span class="ng-cloak" ng-class="{ 'text-warning': title_int.length >= 50 && title_int.length < 100, 'text-danger': title_int.length >= 100 }">
                        [% title_int.length %]
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
                    <input class="form-control" id="agency" name="agency" ng-model="article.agency" type="text"
                    {if is_object($article)}
                    value="{$article->agency|clearslash|escape:"html"}"
                    {else}
                    value="{setting name=site_agency}"
                    {/if} />
                  </div>
                </div>
                {is_module_activated name="ADVANCED_ARTICLE_MANAGER"}
                <div class="form-group col-sm-4">
                  <label class="form-label" for="agency_bulletin">
                    {t}Signature{/t} #2
                  </label>
                  <div class="controls">
                    <input class="form-control" id="agency_bulletin" name="params[agencyBulletin]" ng-model="article.params.agencyBulletin" type="text"
                    {if is_object($article)}
                    value="{$article->params['agencyBulletin']|clearslash|escape:"html"}"
                    {else}
                    value="{setting name=site_agency}"
                    {/if} />
                  </div>
                </div>
                {/is_module_activated}
              </div>
              <div class="form-group">
                <label class="form-label" for="subtitle">
                  {t}Pretitle{/t}
                </label>
                <div class="controls">
                  <div class="input-group" id="subtitle">
                    <input class="form-control" name="subtitle" ng-model="article.subtitle" ng-trim="false" type="text" value="{$article->subtitle|clearslash|escape:"html"}"/>
                    <span class="input-group-addon">
                      <span class="ng-cloak" ng-class="{ 'text-warning': subtitle.length >= 50 && subtitle.length < 100, 'text-danger': subtitle.length >= 100 }">
                        [% subtitle.length %]
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
                  <textarea class="form-control" onm-editor onm-editor-preset="simple" id="summary" name="summary" ng-model="article.summary" rows="5">{$article->summary|clearslash|escape:"html"|default:"&nbsp;"}</textarea>
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
                  <textarea name="body" id="body" ng-model="article.body" onm-editor onm-editor-preset="standard"  class="form-control" rows="15">{$article->body|clearslash|default:"&nbsp;"}</textarea>
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
                        <input id="content_status" name="content_status" ng-model="article.content_status" {if (isset($article) && $article->content_status eq 1)}checked{/if}  value="1" type="checkbox"/>
                        <label for="content_status">
                          {t}Published{/t}
                        </label>
                      </div>
                    </div>
                  {/acl}
                  {is_module_activated name="COMMENT_MANAGER"}
                    <div class="form-group">
                      <div class="checkbox">
                        <input {if (!isset($article) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($article) && $article->with_comment eq 1)}checked{/if} id="with_comment" name="with_comment" ng-model="article.with_comments" type="checkbox" value="1"/>
                        <label class="form-label" for="with_comment">
                          {t}Allow comments{/t}
                        </label>
                      </div>
                    </div>
                  {/is_module_activated}
                  {acl isAllowed="ARTICLE_HOME"}
                    <div class="form-group">
                      <div class="checkbox">
                        <input {if (isset($article) && $article->frontpage eq '1')} checked {/if}  id="frontpage" name="frontpage" type="checkbox" value="1"/>
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
                            <select id="fk_author" name="fk_author" ng-model="article.fk_author">
                              {html_options options=$authors selected=$article->fk_author}
                            </select>
                          </div>
                        </div>
                      {aclelse}
                        {if !isset($article->fk_author) || empty($article->fk_author)}
                          {$smarty.session._sf2_attributes.user->name}
                          <input type="hidden" name="fk_author" ng-model="article.fk_author" value="{$smarty.session._sf2_attributes.user->id}">
                        {else}
                          {$authors[$article->fk_author]}
                          <input type="hidden" name="fk_author" ng-model="article.fk_author" value="{$article->fk_author}">
                        {/if}
                      {/acl}
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="category">
                      {t}Category{/t}
                    </label>
                    <div class="controls">
                      <select id="category" name="category" ng-model="article.category" required="required">
                        <option value="" >{t}- Select a category -{/t}</option>
                        {section name=as loop=$allcategorys}
                        {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                        <option value="{$allcategorys[as]->pk_content_category}" data-name="{$allcategorys[as]->title}"
                          {if $allcategorys[as]->inmenu eq 0} class="unavailable" disabled{/if}
                          {if (($category == $allcategorys[as]->pk_content_category) && !is_object($article)) || $article->category eq $allcategorys[as]->pk_content_category}selected{/if}>
                          {$allcategorys[as]->title}</option>
                          {/acl}
                          {section name=su loop=$subcat[as]}
                          {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                          {if $subcat[as][su]->internal_category eq 1}
                          <option value="{$subcat[as][su]->pk_content_category}" data-name="{$subcat[as][su]->title}"
                            {if $subcat[as][su]->inmenu eq 0} class="unavailable" disabled{/if}
                            {if $category eq $subcat[as][su]->pk_content_category || $article->category eq $subcat[as][su]->pk_content_category}selected{/if} >
                            &nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                          {/if}
                          {/acl}
                          {/section}
                        {/section}
                        <option value="20" data-name="{t}Unknown{/t}" class="unavailable" {if ($category eq '20')}selected{/if}>{t}Unknown{/t}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="metadata">
                      {t}Tags{/t}
                    </label>
                    <div class="controls">
                      <input class="tagsinput" data-role="tagsinput" id="metadata" name="metadata" ng-model="article.metadata" placeholder="{t}Write a tag and press Enter...{/t}" required="required" type="text" value="{$article->metadata|clearslash|escape:"html"}"/>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="slug">
                      {t}Slug{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="slug" name="slug" ng-model="article.slug" type="text" value="{$article->slug|clearslash}" {if isset($article->id) && $article->content_status != 0}disabled{/if}>
                      {if $article && $article->content_status eq 1}
                      {assign var=uri value="\" "|explode:$article->uri}
                      <span class="help-block">
                        <a href="{$smarty.const.INSTANCE_MAIN_DOMAIN}/{$uri.0|clearslash}" target="_blank">
                          <i class="fa fa-external-link"></i> {t}Link{/t}
                        </a>
                      </span>
                      {/if}
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="bodyLink">
                      {t}External link{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="bodyLink" name="params[bodyLink]" ng-model="article.params.bodyLink" type="text" value="{$article->params['bodyLink']}">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="grid simple">
                    <div class="grid-title">
                      <h4>{t}Schedule{/t}</h4>
                    </div>
                    <div class="grid-body">
                      <div class="form-group">
                        <label class="form-label" for="starttime">
                          {t}Publication start date{/t}
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <input class="form-control" id="starttime" name="starttime" type="datetime" value="{if $article->starttime neq '0000-00-00 00:00:00'}{$article->starttime}{/if}">
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
                            <input class="form-control" id="endtime" name="endtime" type="datetime" value="{if $article->endtime neq '0000-00-00 00:00:00'}{$article->endtime}{/if}">
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
                  <h4>{t}Subscription{/t}</h4>
                </div>
                <div class="grid-body">
                  <div class="checkbox">
                    <input {if $article->params["only_registered"] == "1"}checked=checked{/if} id="only_registered" name="params[only_registered]" type="checkbox" value="1">
                    <label for="only_registered">
                      {t}Only available for registered users{/t}
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {/is_module_activated}
          {is_module_activated name="PAYWALL"}
          <div class="row">
            <div class="col-md-12">
              <div class="grid simple">
                <div class="grid-title">
                  <h4>{t}Paywall{/t}</h4>
                </div>
                <div class="grid-body">
                  <div class="checkbox">
                    <input {if $article->params["only_subscribers"] == "1"}checked=checked{/if} id="only_subscribers" name="params[only_subscribers]" ng-model="article.params.only_subscribers" type="checkbox" value="1">
                    <label for="only_subscribers">
                      {t}Only available for subscribers{/t}
                    </label>
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
        {include file ="article/related/_related_list.tpl"}
        <input type="hidden" name="params[withGallery]" ng-model="article.params.withGallery" ng-value="withGallery"/>
        <input type="hidden" name="params[withGalleryInt]" ng-model="article.params.withGalleryInt" ng-value="withGalleryInt"/>
        <input type="hidden" name="params[withGalleryHome]" ng-model="article.params.withGalleryHome" ng-value="withGalleryHome"/>
      </div>

      {is_module_activated name="CRONICAS_MODULES"}
        {include file ="article/partials/_article_advanced_customize.tpl"}
      {/is_module_activated}

      <input type="hidden" id="action" name="action" value="{$action}" />
      <input type="hidden" name="id" id="id" value="{$article->id|default:""}" />
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
  </form>
{/block}
