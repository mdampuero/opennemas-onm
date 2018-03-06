{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script>
      jQuery(document).ready(function ($){
        $('#starttime, #endtime').datetimepicker({
          format: 'YYYY-MM-DD HH:mm:ss',
          useCurrent: false,
          minDate: '{$opinion->created|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}'
        });

        $("#starttime").on("dp.change",function (e) {
          $('#endtime').data("DateTimePicker").minDate(e.date);
        });
        $("#endtime").on("dp.change",function (e) {
          $('#starttime').data("DateTimePicker").maxDate(e.date);
        });

        $('#title').on('change', function(e, ui) {
          var metaTags = $('#metadata');
          var title = $('#title input');

          // Fill tags from title and category
          if (!metaTags.val()) {
            var tags = title.val();
            fill_tags(tags, '#metadata', '{url name=admin_utils_calculate_tags}');
          }
        });

        $('#type_opinion').on('change', function() {
          var selected = $(this).find('option:selected').val();
          if (selected != 0) {
            $('#author').hide();
            $('#fk_author').val(selected);
          } else {
            $('#author').show();
          }
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form action="{if $opinion->id}{url name=admin_opinion_update id=$opinion->id}{else}{url name=admin_opinion_create}{/if}" method="POST" id="formulario" ng-controller="OpinionCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-quote-right"></i>
                {t}Opinions{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>
                {if $opinion->id}
                  {t}Editing opinion{/t}
                {else}
                  {t}Creating opinion{/t}
                {/if}
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <div class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_opinions}" title="{t}Go back{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks hidden-xs">
                <button class="btn btn-white" id="preview-button" ng-click="preview('admin_opinion_preview', 'admin_opinion_get_preview')" type="button" id="preview_button">
                  <i class="fa fa-desktop" ng-class="{ 'fa-circle-o-notch fa-spin': loading }" ></i>
                  {t}Preview{/t}
                </button>
              </li>
              <li class="quicklinks hidden-xs">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit" id="save-button">
                  <i class="fa fa-save"></i>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </div>
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
                  <div class="input-group" id="title" >
                    <input class="form-control" name="title" ng-model="title" ng-trim="false" required type="text" value="{$opinion->title|clearslash|escape:"html"}"/>
                    <span class="input-group-addon">
                      <span class="ng-cloak" ng-class="{ 'text-warning': title.length >= 50 && title.length < 100, 'text-danger': title.length >= 100 }">
                        [% title.length %]
                      </span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="summary">
                  {t}Summary{/t}
                </label>
                <div class="controls">
                  <textarea class="form-control" onm-editor onm-editor-preset="simple" id="summary" name="summary" ng-model="summary">{$opinion->summary|clearslash|escape:"html"|default:"&nbsp;"}</textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="body">
                  <span class="pull-left">{t}Body{/t}</span>
                </label>
                {acl isAllowed='PHOTO_ADMIN'}
                <div class="pull-right">
                  <div class="btn btn-default btn-mini" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="5" media-picker-target="editor.body">
                    {t}Insert image{/t}
                  </div>
                </div>
                {/acl}
                <div class="controls">
                  <textarea name="body" id="body" ng-model="body" class="form-control" onm-editor onm-editor-preset="standard">{$opinion->body|clearslash|default:"&nbsp;"}</textarea>
                </div>
              </div>
              <input type="hidden" id="fk_user_last_editor" name="fk_user_last_editor" value="{$publisher|default:""}"/>
              <input type="hidden" id="category" name="category" title="opinion" value="opinion" />
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-12">
              <div class="grid simple">
                <div class="grid-title">
                  <h4>{t}Attributes{/t}</h4>
                </div>
                <div class="grid-body">
                  <div class="form-group">
                    <div class="checkbox">
                      <input id="content_status" name="content_status" type="checkbox" {if $opinion->content_status eq 1}checked="checked"{/if}/>
                      <label for="content_status">
                        {t}Published{/t}
                      </label>
                    </div>
                  </div>
                  {is_module_activated name="COMMENT_MANAGER"}
                    <div class="form-group">
                      <div class="checkbox">
                        <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($opinion) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($opinion) && $opinion->with_comment eq 1)}checked{/if}  />
                        <label for="with_comment">
                          {t}Allow comments{/t}
                        </label>
                      </div>
                    </div>
                  {/is_module_activated}
                  <div class="form-group">
                    <div class="checkbox">
                      <input id="in_home" name="in_home" type="checkbox" {if $opinion->in_home eq 1}checked="checked"{/if}>
                      <label for="in_home">
                        {t}In homepage{/t}
                      </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="type_opinion">
                      {t}Type{/t}
                    </label>
                    <div class="controls">
                      <select id="type_opinion" name="type_opinion" required>
                        <option value="">{t}-- Select opinion type --{/t}</option>
                        <option value="0" {if $opinion->type_opinion eq 0} selected {/if}>{t}Opinion from author{/t}</option>
                        <option value="1" {if $opinion->type_opinion eq 1} selected {/if}>{t}Opinion from editorial{/t}</option>
                        <option value="2" {if $opinion->type_opinion eq 2} selected {/if}>{t}Director's letter{/t}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group" id="author" {if $opinion->type_opinion neq 0}style="display:none"{/if}>
                    <label class="form-label" for="fk_author">
                      {t}Author{/t}
                    </label>
                    <div class="controls">
                      {acl isAllowed="CONTENT_OTHER_UPDATE"}
                        <select id="fk_author" name="fk_author" required>
                          <option value="" {if is_null($author->id)}selected{/if}>{t} - Select one author - {/t}</option>
                          {foreach from=$all_authors item=author}
                          <option value="{$author->id}" {if $opinion->fk_author eq $author->id}selected{/if}>{$author->name} {if $author->meta['is_blog'] eq 1} (Blogger) {/if}</option>
                          {/foreach}
                        </select>
                      {aclelse}
                        <select id="fk_author" name="fk_author">
                          <option value="{$app.user->id}" selected >{$app.user->name}</option>
                        </select>
                      {/acl}
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label">
                      {t}Tags{/t}
                    </label>
                    <div class="controls">
                      <input class="tagsinput" data-role="tagsinput" id="metadata" name="metadata" placeholder="{t}Write a tag and press Enter...{/t}" required type="text" value="{$opinion->metadata|clearslash|escape:"html"}"/>
                    </div>
                  </div>
                  {if is_object($opinion)}
                    <div class="form-group">
                      <span>
                        <a href="{$smarty.const.INSTANCE_MAIN_DOMAIN}/{$opinion->uri}" target="_blank">
                          {t}Link{/t} <i class="fa fa-external-link"></i>
                        </a>
                      </span>
                    </div>
                  {/if}
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
                        <input class="form-control" id="starttime" name="starttime" type="datetime" value="{if $opinion->starttime neq '0000-00-00 00:00:00'}{$opinion->starttime}{/if}" type="datetime" >
                        <span class="input-group-addon add-on">
                          <span class="fa fa-calendar"></span>
                        </span>
                      </div>
                      <div class="help-block">{t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="endtime">
                      {t}Publication end date{/t}
                    </label>
                    <div class="controls">
                      <div class="input-group">
                        <input class="form-control" id="endtime" name="endtime" type="datetime" value="{if $opinion->endtime neq '0000-00-00 00:00:00'}{$opinion->endtime}{/if}" type="datetime">
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
          {is_module_activated name="CONTENT_SUBSCRIPTIONS"}
            <div class="row">
              <div class="col-md-12">
                <div class="grid simple">
                  <div class="grid-title">
                    <h4>{t}Subscription{/t}</h4>
                  </div>
                  <div class="grid-body">
                    <div class="checkbox">
                      <input {if is_array($opinion->params) && array_key_exists('only_registered', $opinion->params) && $opinion->params["only_registered"] == "1"}checked=checked{/if} id="only_registered" name="params[only_registered]" type="checkbox" value="1">
                      <label for="only_registered">
                        {t}Only available for registered users{/t}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          {/is_module_activated}
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          {include  file="opinion/partials/_images.tpl" article=$opinion withoutVideo='true'}
        </div>
      </div>
      <div class="row ng-cloak" ng-init="fieldsByModule = {json_encode($extra_fields)|escape:"html"}">
        <div class="col-md-12" ng-if="fieldsByModule !== undefined && fieldsByModule">
          <div class="grid simple">
            <div class="grid-title">
              <h4>
                <i class="fa fa-magic"></i>
                {t}Additional data{/t}
              </h4>
            </div>
            <div class="grid-body"{if !empty($opinion)} ng-init="opinion = {json_encode(get_object_vars($opinion))|escape:"html"}"{/if}>
              <autoform ng-model="opinion" fields-by-module="fieldsByModule"/>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
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
  </form>
{/block}
