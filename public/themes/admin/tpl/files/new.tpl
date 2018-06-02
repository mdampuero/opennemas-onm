{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script type="text/javascript">
      $(document).ready(function($) {
        $('.fileinput').fileinput({ name: 'path' });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form action="{if !is_null($attaches)}{url name=admin_files_update id=$attaches->id}{else}{url name=admin_files_create}{/if}" enctype="multipart/form-data" method="POST" name="formulario" id="formulario" ng-controller="FileCtrl" ng-init="init({json_encode($attaches)|clear_json}, {json_encode($locale)|clear_json}, {json_encode($tags)|clear_json})" />
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-file-o"></i>
                {t}Files{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>
                {if $attaches}
                  {t}Editing file{/t}
                {else}
                  {t}Creating file{/t}
                {/if}
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_files}" title="{t}Go back{/t}">
                  <span class="fa fa-reply"></span>
                </a>
              </li>
              <li class="quicklinks"><span class="h-seperate"></span></li>
              {acl isAllowed="ATTACHMENT_CREATE"}
                <li class="quicklinks">
                  <button class="btn btn-primary" type="submit" data-text="{t}Saving{/t}..." id="save-button">
                    <span class="fa fa-save"></span>
                    <span class="text">{t}Save{/t}</span>
                  </button>
                </li>
              {/acl}
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
                <label for="" class="form-label">{t}Title{/t}</label>
                <div class="controls">
                  <input type="text" id="title" name="title" ng-model="title" value="{$attaches->title|clearslash}" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="form-label">{t}Description{/t}</label>
                <div class="controls">
                  <textarea id="description" name="description" class="form-control" ng-model="description" required class="required" onm-editor onm-editor-preset="simple">{$attaches->description|clearslash}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <label for="category" class="form-label">{t}Category{/t}</label>
                <div class="controls">
                  {include file="common/selector_categories.tpl" name="category" item=$attaches}
                </div>
              </div>
              <div class="form-group">
                <label for="metadata" class="form-label">{t}Tags{/t}</label>
                <div class="controls">
                  <onm-tag ng-model="tag_ids" locale="locale" tags-list="tags" check-new-tags="checkNewTags" get-suggested-tags="getSuggestedTags" load-auto-suggested-tags="loadAutoSuggestedTags" suggested-tags="suggestedTags" placeholder="{t}Write a tag and press Enter...{/t}"/>
                </div>
              </div>
              <div class="form-group">
                <label for="path" class="form-label">{t}File{/t}</label>
                <div class="controls">
                  {if !is_null($attaches)}
                    <a class="btn btn-white thumbnail center" target="_blank" href="{$smarty.const.INSTANCE_MAIN_DOMAIN}{$smarty.const.INSTANCE_MEDIA}{$smarty.const.FILE_DIR}{$attaches->path}">
                      <span class="fa fa-download fa-2x"></span> <br>{t}Download{/t}
                    </a>
                    <input type="hidden" id="path" name="path" value="{$attaches->path|clearslash}" class="form-control" required readonly="readonly">
                  {else}
                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                      <div class="form-control" data-trigger="fileinput" style="height: 37px;">
                        <i class="fa fa-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                      </div>
                      <span class="input-group-btn">
                        <span class="btn btn-default btn-file">
                          <span class="fileinput-new">Select file</span>
                          <span class="fileinput-exists">Change</span>
                          <input type="file" id="path" name="path" />
                        </span>
                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">{t}Remove{/t}</a>
                      </span>
                    </div>
                    <div class="help">
                      {t 1=$smarty.const.MAX_UPLOAD_FILE/1024/1024}Max allowed file size: %1 Mb{/t}
                      <!-- <span class="fa fa-info-circle" uib-tooltip="{t}File types allowed{/t}: 7z avi bmp bz2 css csv doc docx eot flac flv gif gz ico jpeg jpg js mka mkv mov mp3 mp4 mpeg mpg odt odp ods odw otf ogg ogm opus pdf png ppt pptx rar rtf svg svgz swf tar tbz tgz ttf txt txz wav webm webp woff woff2 xls xlsx xml xz zip"> -->
                    </span></div>
                  {/if}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {if !is_null($attaches->id)}
        <input type="hidden" id="id" name="id"  value="{$attaches->id|default:""}" />
        <input type="hidden" id="fich" name="fich" value="{$attaches->pk_attachment}" />
      {/if}
      <input type="hidden" name="page" id="page" value="{$page|default:"1"}" />
  </form>
{/block}
