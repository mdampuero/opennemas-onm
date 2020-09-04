{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script>
      var localeAux = '{$smarty.const.CURRENT_LANGUAGE_SHORT|default:"en"}';
      localeAux = moment.locales().includes(localeAux) ?
        localeAux :
        'en';
      $('#date').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        minDate: '{$book->created|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}',
        locale: localeAux
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form action="{if isset($book)}{url name=admin_books_update id=$book->id}{else}{url name=admin_books_create}{/if}" id="formulario" method="POST" name="form" ng-controller="BookCtrl" ng-init="book = {json_encode($book)|clear_json}">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-book m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=admin_books}">
                  {t}Books{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>
                {if !isset($book->id)}{t}Create{/t}{else}{t}Edit{/t}{/if}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="submit($event)" type="submit">
                  <i class="fa fa-save m-r-5"></i>
                  {t}Save{/t}
                </button>
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
                  <input type="text" id="title" name="title" ng-blur="generate()" ng-model="title" value="{$book->title|default:""}" required class="form-control"/>
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="form-label">{t}Description{/t}</label>
                <div class="controls">
                  <textarea onm-editor onm-editor-preset="simple" id="description" ng-model="description" name="description" rows="3" class="form-control">{$book->description|clearslash}</textarea>
                </div>
              </div>
              <div class="form-group ng-cloak" {if isset($book->cover_img) && $book->cover_img}ng-init="book_cover = {json_encode($book->cover_img)|clear_json}"{/if}>
                  <h5>{t}Cover image{/t}</h5>
                  <div class="form-group">
                    <div class="thumbnail-placeholder">
                      <div class="img-thumbnail" ng-if="!book_cover">
                        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="book_cover">
                          <i class="fa fa-picture-o fa-2x"></i>
                          <h5>{t}Pick an image{/t}</h5>
                        </div>
                      </div>
                      <div class="dynamic-image-placeholder" ng-if="book_cover">
                        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="book_cover" transform="thumbnail,220,220">
                          <div class="thumbnail-actions">
                            <div class="thumbnail-action remove-action" ng-click="removeImage('book_cover')">
                              <i class="fa fa-trash-o fa-2x"></i>
                            </div>
                            <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="book_cover">
                              <i class="fa fa-camera fa-2x"></i>
                            </div>
                          </div>
                          <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="book_cover" media-picker-type="photo"></div>
                        </dynamic-image>
                      </div>
                    </div>
                  </div>
                  <input type="hidden" name="book_cover_id" ng-value="book_cover_id">
              </div>
            </div>
            <input type="hidden" name="id" id="id" value="{$book->id|default:""}" />
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <div class="checkbox">
                <input {acl isNotAllowed="BOOK_AVAILABLE"} disabled="disabled" {/acl} type="checkbox" value="1" id="content_status" name="content_status" {if !isset($book) || $book->content_status == 1}checked="checked"{/if}>
                  <label for="content_status">{t}Published{/t}</label>
                </div>
              </div>
              <div class="form-group">
                <div class="checkbox">
                <input {acl isNotAllowed="BOOK_AVAILABLE"} disabled="disabled" {/acl} type="checkbox" value="1" id="in_home" name="in_home" {if !isset($book) || $book->in_home == 1}checked="checked"{/if}>
                  <label for="in_home">{t}Home{/t}</label>
                </div>
              </div>
              <div class="form-group">
                <label for="category" class="form-label">{t}Category{/t}</label>
                <div class="controls">
                  <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" locale="config.locale.selected" ng-model="book.category_id" placeholder="{t}Select a category{/t}…" required></onm-category-selector>
                </div>
              </div>
              <div class="form-group">
                <label for="metadata" class="form-label">{t}Tags{/t}</label>
                <div class="controls">
                  {include file="ui/component/tags-input/tags.tpl" ngModel="book.tags"}
                </div>
              </div>
              <div class="form-group">
                <label for="author" class="form-label">{t}Author{/t}</label>
                <div class="controls">
                  <input type="text" id="author" name="author" value="{$book->author|default:""}" required class="form-control"/>
                </div>
              </div>
              <div class="form-group">
                <label for="starttime" class="form-label">{t}Date{/t}</label>
                <div class="controls">
                  <div class="input-group">
                    <input class="form-control" type="datetime" id="date" name="starttime" value="{if $book->starttime}{$book->starttime}{/if}">
                    <span class="input-group-addon add-on">
                      <span class="fa fa-calendar"></span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="editorial" class="form-label">{t}Editorial{/t}</label>
                <div class="controls">
                  <input type="text" id="editorial" name="editorial" value="{$book->editorial|default:""}" required class="form-control"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
{/block}
