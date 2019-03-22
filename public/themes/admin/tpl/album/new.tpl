{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script>
      $(document).ready(function($){
        var localeAux = '{$smarty.const.CURRENT_LANGUAGE_SHORT|default:"en"}';
        localeAux = moment.locales().includes(localeAux) ?
          localeAux :
          'en';
        $('#starttime, #endtime').datetimepicker({
          format: 'YYYY-MM-DD HH:mm:ss',
          useCurrent: false,
          minDate: '{$album->created|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}',
          locale: localeAux
        });

        $("#starttime").on("dp.change",function (e) {
          $('#endtime').data("DateTimePicker").minDate(e.date);
        });
        $("#endtime").on("dp.change",function (e) {
          $('#starttime').data("DateTimePicker").maxDate(e.date);
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form action="{if isset($album->id)}{url name=admin_album_update id=$album->id}{else}{url name=admin_album_create}{/if}" id="formulario" method="POST" name="form"  ng-controller="AlbumCtrl" ng-init="album = {json_encode($album)|clear_json}">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-stack-overflow page-navbar-icon"></i>
                <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/745938-opennemas-c%C3%B3mo-crear-%C3%A1lbumes-galer%C3%ADas-de-imagene" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                  <i class="fa fa-question"></i>
                </a>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=admin_albums}">
                  {t}Albums{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks visible-xs">
              <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/745938-opennemas-c%C3%B3mo-crear-%C3%A1lbumes-galer%C3%ADas-de-imagene" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question fa-lg"></i>
              </a>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>
                {if isset($album->id)}{t}Edit{/t}{else}{t}Create{/t}{/if}
              </h4>
            </li>
          </ul>
          <div class="pull-right">
            <ul class="quick-section">
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
                <label class="form-label" for="title">
                  {t}Title{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="title" name="title" ng-blur="generate()" ng-model="title" required type="text" value="{$album->title|clearslash|escape:"html"|default:""}"/>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="description">
                  {t}Description{/t}
                </label>
                <div class="controls">
                  <textarea class="form-control" id="description" name="description" ng-model="description" onm-editor onm-editor-preset="simple">{$album->description|clearslash}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <div class="checkbox">
                  <input type="checkbox" value="1" id="content_status" name="content_status" {if $album->content_status eq 1}checked="checked"{/if}>
                  <label for="content_status">{t}Published{/t}</label>
                </div>
              </div>
              {is_module_activated name="COMMENT_MANAGER"}
              <div class="form-group">
                <div class="checkbox">
                  <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($album) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($album) && $album->with_comment eq 1)}checked{/if} value="1" />
                  <label for="with_comment">{t}Allow comments{/t}</label>
                </div>
              </div>
              {/is_module_activated}
              <div class="form-group">
                <label class="form-label">
                  {t}Category{/t}
                </label>
                <div class="controls">
                    <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" locale="config.locale.selected" ng-model="album.pk_fk_content_category" placeholder="{t}Select a category{/t}…" required></onm-category-selector>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="agency">
                  {t}Agency{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="agency" name="agency" type="text" value="{$album->agency|clearslash|escape:"html"}"/>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">
                  {t}Author{/t}
                </label>
                <div class="controls">
                  {acl isAllowed="CONTENT_OTHER_UPDATE"}
                    <select id="fk_author" name="fk_author">
                      <option value="" {if empty($opinion->fk_author)}selected{/if}>{t}Select an author...{/t}</option>
                      {foreach from=$authors item=author}
                        <option value="{$author->id}" {if $album->fk_author eq $author->id}selected{/if}>{$author->name}</option>
                      {/foreach}
                    </select>
                  {aclelse}
                    {if !isset($album->fk_author) || empty($album->fk_author)}
                      {$app.user->name}
                      <input type="hidden" name="fk_author" value="{$app.user->id}">
                    {else}
                      {$authors[$album->fk_author]->name}
                      <input type="hidden" name="fk_author" value="{$album->fk_author}">
                    {/if}
                  {/acl}
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">
                  {t}Tags{/t}
                </label>
                <div class="controls">
                  {include file="ui/component/tags-input/tags.tpl" ngModel="album.tag_ids"}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8" {if !empty($photos)}ng-init="parsePhotos({json_encode($photos)|clear_json})"{/if}>
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Album images{/t}</h4>
            </div>
            <div class="grid-body no-padding">
              <div ui-sortable="{ axis: 'x,y', placeholder: 'album-thumbnail-sortable' }" ng-model="photos">
                <div class="album-thumbnail-sortable" ng-repeat="photo in photos">
                  <input type="hidden" name="album_photos_id[]" ng-value="photo.id"/>
                  <input type="hidden" name="album_photos_footer[]" ng-value="photo.footer"/>
                  <div class="thumbnail-wrapper">
                    <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay['photo_'+ $index] }"></div>
                    <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay['photo_'+ $index] }">
                      <p>{t}Are you sure?{/t}</p>
                      <div class="confirm-actions">
                        <button class="btn btn-link" ng-click="toggleOverlay('photo_'+ $index)" type="button">
                          <i class="fa fa-times fa-lg"></i>
                          {t}No{/t}
                        </button>
                        <button class="btn btn-link" ng-click="removeItem('photos', $index);toggleOverlay('photo_'+ $index)" type="button">
                          <i class="fa fa-check fa-lg"></i>
                          {t}Yes{/t}
                        </button>
                      </div>
                    </div>
                    <div class="dynamic-image-placeholder">
                      <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo" transform="zoomcrop,500,500">
                        <div class="thumbnail-actions">
                          <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo_'+ $index)">
                            <i class="fa fa-trash-o fa-2x"></i>
                          </div>
                        </div>
                      </dynamic-image>
                    </div>
                    <div class="form-group">
                      <textarea class="album-thumbnail-description form-control" ng-model="photo.footer"></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <div class="album-thumbnail-placeholder">
                <div class="img-thumbnail">
                  <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="150" media-picker-target="photos">
                    <i class="fa fa-plus fa-3x"></i>
                    <h4>{t}Add images{/t}<h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4" {if isset($album->cover_image) && $album->cover_image->id}ng-init="cover = {json_encode($album->cover_image)|clear_json}"{/if}>
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Cover image{/t}</h4>
            </div>
            <div class="grid-body">
              <div class="thumbnail-wrapper">
                <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.cover }"></div>
                <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.cover }">
                  <p>Are you sure?</p>
                  <div class="confirm-actions">
                    <button class="btn btn-link" ng-click="toggleOverlay('cover')" type="button">
                      <i class="fa fa-times fa-lg"></i>
                      {t}No{/t}
                    </button>
                    <button class="btn btn-link" ng-click="removeImage('cover');toggleOverlay('cover')" type="button">
                      <i class="fa fa-check fa-lg"></i>
                      {t}Yes{/t}
                    </button>
                  </div>
                </div>
                <div class="thumbnail-placeholder">
                  <div class="img-thumbnail" ng-if="!cover">
                    <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover">
                      <i class="fa fa-picture-o fa-2x"></i>
                      <h5>Pick an image</h5>
                    </div>
                  </div>
                  <div class="dynamic-image-placeholder" ng-if="cover">
                    <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="cover">
                      <div class="thumbnail-actions">
                        <div class="thumbnail-action remove-action" ng-click="toggleOverlay('cover')">
                          <i class="fa fa-trash-o fa-2x"></i>
                        </div>
                        <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover" media-picker-types="photo">
                          <i class="fa fa-camera fa-2x"></i>
                        </div>
                      </div>
                    </dynamic-image>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
                    <input class="form-control" id="starttime" name="starttime" type="datetime" value="{if $album->starttime neq '0000-00-00 00:00:00'}{$album->starttime}{/if}">
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
                    <input class="form-control" id="endtime" name="endtime" type="datetime" value="{if $album->endtime neq '0000-00-00 00:00:00'}{$album->endtime}{/if}">
                    <span class="input-group-addon add-on">
                      <span class="fa fa-calendar"></span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {is_module_activated name="CONTENT_SUBSCRIPTIONS"}
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Subscription{/t}</h4>
            </div>
            <div class="grid-body">
              <div class="checkbox">
                <input {if (is_array($album->params) && $album->params["only_registered"] == "1")}checked=checked{/if} id="only_registered" name="params[only_registered]" type="checkbox" value="1">
                <label for="only_registered">
                  {t}Only available for registered users{/t}
                </label>
              </div>
            </div>
          </div>
          {/is_module_activated}
        </div>
      </div>
      <input type="hidden" name="album_frontpage_image" id="album_frontpage_image" ng-value="cover.id" />
      <input type="hidden" name="id" id="id" value="{$album->id|default:""}" />
    </div>
    <script type="text/ng-template" id="modal-edit-album-error">
      {include file="album/modals/_edit_album_error.tpl"}
    </script>
  </form>
{/block}
