{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{if $special->id}{url name=admin_special_update id=$special->id}{else}{url name=admin_special_create}{/if}" id="formulario" method="post" name="form" ng-controller="SpecialCtrl" ng-init="special = {json_encode($special)|clear_json}">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-star m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=admin_specials}">
                  {t}Specials{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>{if !isset($special->id)}{t}Create{/t}{else}{t}Edit{/t}{/if}</h4>
            </li>
          </ul>
          <div class="pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="submit($event)" type="submit">
                  <i class="fa fa-save m-r-5"></i>
                  {t}Save{/t}
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
                  <input type="text" id="title" ng-blur="generate()" ng-model="title" name="title" required class="form-control"
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
                <div class="checkbox">
                  <input type="checkbox" name="in_home" id="in_home" value="1" {if $special->in_home eq 1} checked="checked"{/if}>
                  <label for="in_home" class="form-label">
                    {t}Home{/t}
                  </label>
                </div>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <input type="checkbox" name="favorite" id="favorite" value="1" {if $special->favorite eq 1} checked="checked"{/if}>
                  <label for="favorite" class="form-label">
                    {t}Favorite{/t}
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label for="category" class="form-label">{t}Category{/t}</label>
                <div class="controls">
                  <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" locale="config.locale.selected" ng-model="special.category_id" placeholder="{t}Select a category{/t}…" required></onm-category-selector>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">{t}Tags{/t}</label>
                <div class="controls">
                  {include file="ui/component/tags-input/tags.tpl" ngModel="special.tags"}
                </div>
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
                  <div class="col-md-12" {if isset($photo1)}ng-init="photo1 = {json_encode($photo1)|clear_json}"{/if}>
                    <div class="form-group ng-cloak">
                      <div class="thumbnail-placeholder">
                        <div class="img-thumbnail" ng-if="!photo1">
                          <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                            <i class="fa fa-picture-o fa-2x"></i>
                            <h5>no que esta{t}Pick an image{/t}</h5>
                          </div>
                        </div>
                        <div class="dynamic-image-placeholder" ng-if="photo1">
                          <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo1" transform="thumbnail,220,220">
                          <div class="thumbnail-actions">
                            <div class="thumbnail-action remove-action" ng-click="removeImage('photo1')">
                              <i class="fa fa-trash-o fa-2x"></i> {$photo1->path}
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
