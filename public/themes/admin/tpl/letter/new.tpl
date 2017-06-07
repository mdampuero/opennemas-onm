{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script>
      jQuery(document).ready(function($) {
        $('#created').datetimepicker({
          format: 'YYYY-MM-DD HH:mm:ss',
          minDate: '{$letter->created|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}'
        });

        $('#title').on('change', function(e, ui) {
          fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form id="formulario" action="{if isset($letter->id)}{url name=admin_letter_update id=$letter->id}{else}{url name=admin_letter_create}{/if}" method="POST" ng-controller="LetterCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-envelope"></i>
                <span class="hidden-xs">{t}Letters to the Editor{/t}</span>
                <span class="visible-xs-inline">{t}Letters{/t}</span>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>
                {if isset($letter->id)}
                  {t}Editing letter{/t}
                {else}
                  {t}Creating letter{/t}
                {/if}
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_letters}" title="{t}Go back{/t}">
                  <span class="fa fa-reply"></span>
                </a>
              </li>
              <li class="quicklinks"><span class="h-seperate"></span></li>
              <li class="quicklinks">
                <button class="btn btn-primary" type="submit" data-text="{t}Saving{/t}..." id="save-button">
                  <span class="fa fa-save"></span>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
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
                <label for="title" class="form-label">{t}Title{/t}</label>
                <div class="controls">
                  <input type="text" id="title" name="title" value="{$letter->title|clearslash|escape:"html"}" required="required" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label for="body" class="form-label">{t}Body{/t}</label>
                <div class="controls">
                  <textarea name="body" id="body" ng-model="body" class="onm-editor form-control" onm-editor onm-editor-preset="standard" rows="10">{$letter->body|clearslash}</textarea>
                </div>
              </div>
              <h4>{t}Author information{/t}</h4>
              <div class="row">
                <div class="form-inline-block">
                  <div class="form-group col-md-6">
                    <label for="author" class="form-label">{t}Nickname{/t}</label>
                    <div class="controls">
                      <input type="text" id="author" name="author" value="{$letter->author|clearslash}" required="required" class="form-control" />
                    </div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="email" class="form-label">{t}Email{/t}</label>
                    <div class="controls">
                      <input type="email" id="email" name="email" value="{$letter->email|clearslash}" required="required" class="form-control" />
                    </div>
                  </div>
                </div>
              </div>
              {if !empty($letter->params)}
                <div class="form-inline-block">
                  {foreach $letter->params as $key => $value}
                  <div class="form-group">
                    <label for="{$key}" class="form-label">{t}{$key|capitalize}{/t}</label>
                    <div class="controls">
                      <input type="text" id="params[{$key}]" name="params[{$key}]" value="{$value|clearslash}"  readonly class="form-control" />
                    </div>
                  </div>
                  {/foreach}
                </div>
              {/if}
              <div class="form-group">
                <label for="created" class="form-label">{t}Created at{/t}</label>
                <div class="controls">
                  <div class="input-group">
                    <input class="form-control" type="text" id="created" name="created" value="{$letter->created}" readonly/>
                    <span class="input-group-addon add-on">
                      <span class="fa fa-calendar"></span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">
              {acl isAllowed="LETTER_AVAILABLE"}
                <div class="form-group">
                  <div class="checkbox">
                    <input id="content_status" name="content_status" {if $letter->content_status eq 1} checked {/if}  value="1" type="checkbox"/>
                    <label for="content_status">
                      {t}Published{/t}
                    </label>
                  </div>
                </div>
              {/acl}
              {is_module_activated name="COMMENT_MANAGER"}
              <div class="form-group">
                <div class="checkbox">
                  <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($letter) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($letter) && $letter->with_comment eq 1)}checked{/if} value="1" />
                  <label for="with_comment">{t}Allow comments{/t}</label>
                </div>
              </div>
              {/is_module_activated}
              <div class="form-group">
                <label for="metadata" class="form-label">{t}Tags{/t}</label>
                <span class="help">{t}List of words separated by words.{/t}</span>
                <div class="controls">
                  <input data-role="tagsinput" id="metadata" name="metadata" placeholder="{t}Write a tag and press Enter...{/t}" required="required" type="hidden" value="{$letter->metadata|clearslash|escape:"html"}"/>
                </div>
              </div>
              <div class="form-group">
                <label for="url" class="form-label">{t}Related url{/t}</label>
                <div class="controls">
                  <input type="text" id="url" name="url" value="{$letter->url}" class="form-control"/>
                </div>
              </div>
            </div>
          </div>
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Image assigned{/t}</h4>
            </div>
            <div class="grid-body">
              <div class="row">
                <div class="col-md-12" {if isset($photo1) && $photo1->name}ng-init="photo1 = {json_encode($photo1)|clear_json}"{/if}>
                  <div class="thumbnail-wrapper">
                    <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.photo1 }"></div>
                    <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.photo1 }">
                      <p>{t}Are you sure?{/t}</p>
                      <div class="confirm-actions">
                        <button class="btn btn-link" ng-click="toggleOverlay('photo1')" type="button">
                          <i class="fa fa-times fa-lg"></i>
                          {t}No{/t}
                        </button>
                        <button class="btn btn-link" ng-click="removeImage('photo1');toggleOverlay('photo1')" type="button">
                          <i class="fa fa-check fa-lg"></i>
                          {t}Yes{/t}
                        </button>
                      </div>
                    </div>
                    <div class="thumbnail-placeholder ng-cloak">
                      <div class="img-thumbnail" ng-if="!photo1">
                        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                          <i class="fa fa-picture-o fa-2x"></i>
                          <h5>{t}Pick an image{/t}</h5>
                        </div>
                      </div>
                      <div class="dynamic-image-placeholder ng-cloak" ng-if="photo1">
                        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo1" transform="thumbnail,220,220">
                        <div class="thumbnail-actions">
                          <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo1')">
                            <i class="fa fa-trash-o fa-2x"></i>
                          </div>
                          <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                            <i class="fa fa-camera fa-2x"></i>
                          </div>
                        </div>
                        <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1" media-picker-type="photo"></div>
                      </dynamic-image>
                      <input type="hidden" name="img1" ng-value="img1"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="id" id="id" value="{$letter->id|default:""}" />
  </form>
{/block}
