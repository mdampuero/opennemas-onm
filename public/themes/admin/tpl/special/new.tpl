{extends file="base/admin.tpl"}
{block name="content"}
  <form action="{if $special->id}{url name=admin_special_update id=$special->id}{else}{url name=admin_special_create}{/if}" method="post" ng-controller="SpecialCtrl" id="formulario" ng-init="init({json_encode($special)|clear_json}, {json_encode($locale)|clear_json}, {json_encode($tags)|clear_json})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-star"></i>
                {t}Specials{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks hidden-xs">
              <h5>{if !isset($special->id)}{t}Creating special{/t}{else}{t}Editing special{/t}{/if}</h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li>
                <a class="btn btn-link" href="{url name=admin_specials}">
                  <span class="fa fa-reply"></span>
                </a>
              </li>
              <li class="quicklinks"><span class="h-seperate"></span></li>
              <li class="quicklinks">
                {if !is_null($special->id)}
                  {acl isAllowed="SPECIAL_UPDATE"}
                    <button class="btn btn-primary" data-text="{t}Updating{/t}..." type="submit" id="update-button">
                      <i class="fa fa-save"></i>
                      <span class="text">{t}Update{/t}</span>
                    </button>
                  {/acl}
                {else}
                  {acl isAllowed="SPECIAL_CREATE"}
                    <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit" id="save-button">
                      <i class="fa fa-save"></i>
                      <span class="text">{t}Save{/t}</span>
                    </button>
                  {/acl}
                {/if}
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
                  <input type="text" id="title" ng-model="title" name="title" required class="form-control"
                  value="{$special->title|clearslash|escape:"html"}"/>
                </div>
              </div>
              <div class="form-group">
                <label for="pretitle" class="form-label">{t}Pretitle{/t}</label>
                <div class="controls">
                  <input type="text" id="pretitle" name="pretitle" class="form-control" value="{$special->pretitle|clearslash|escape:"html"}" />
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="form-label">{t}Description{/t}</label>
                <div class="controls">
                  <textarea name="description" id="description" ng-model="description" onm-editor onm-editor-preset="simple">{$special->description|clearslash}</textarea>
                </div>
              </div>
            </div>
          </div>
          {include file="special/partials/_contents_containers.tpl"}
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <div class="checkbox">
                  <input type="checkbox" name="content_status" id="content_status" value="1" {if $special->content_status eq 1} checked="checked"{/if}>
                  <label for="content_status" class="form-label">
                    {t}Published{/t}
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label for="category" class="form-label">{t}Category{/t}</label>
                <div class="controls">
                  {include file="common/selector_categories.tpl" name="category" item=$special}
                </div>
              </div>
              <div class="form-group">
                <label for="metadata" class="form-label">{t}Tags{/t}</label>
                <onm-tag ng-model="tag_ids" locale="locale" tags-list="tags" check-new-tags="checkNewTags" get-suggested-tags="getSuggestedTags" load-auto-suggested-tags="loadAutoSuggestedTags" suggested-tags="suggestedTags" placeholder="{t}Write a tag and press Enter...{/t}"/>
              </div>
              <div class="form-group">
                <label for="slug" class="form-label">{t}Slug{/t}</label>
                <div class="controls">
                  <input  type="text" id="slug" name="slug" class="form-control"
                  value="{$special->slug|clearslash|escape:"html"}" />
                </div>
              </div>
            </div>
          </div>
          {acl isAllowed='PHOTO_ADMIN'}
            {is_module_activated name="IMAGE_MANAGER"}
              <div class="grid simple">
                <div class="grid-title">
                  <h4>{t}Image for Special{/t}</h4>
                </div>
                <div class="grid-body">
                  <div class="col-md-12" {if isset($photo1) && $photo1->name}ng-init="photo1 = {json_encode($photo1)|clear_json}"{/if}>
                    <div class="form-group ng-cloak">
                      <div class="thumbnail-placeholder">
                        <div class="img-thumbnail" ng-if="!photo1">
                          <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                            <i class="fa fa-picture-o fa-2x"></i>
                            <h5>{t}Pick an image{/t}</h5>
                          </div>
                        </div>
                        <div class="dynamic-image-placeholder" ng-if="photo1">
                          <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo1" transform="thumbnail,220,220">
                          <div class="thumbnail-actions">
                            <div class="thumbnail-action remove-action" ng-click="removeImage('photo1')">
                              <i class="fa fa-trash-o fa-2x"></i>
                            </div>
                            <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                              <i class="fa fa-camera fa-2x"></i>
                            </div>
                          </div>
                          <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1" media-picker-type="photo"></div>
                        </dynamic-image>
                      </div>
                    </div>
                  </div>
                  <input type="hidden" name="img1" ng-value="img1"/>
                </div>
              </div>
            {/is_module_activated}
          {/acl}
        </div>
      </div>
    </div>
  </form>
{/block}
