{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script type="text/javascript">
      $('#date').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        minDate: '{$book->created|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}'
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
<form id="formulario" action="{if isset($book)}{url name=admin_books_update id=$book->id}{else}{url name=admin_books_create}{/if}" method="POST" ng-controller="BookCtrl" ng-init="init({json_encode($book)|clear_json}, {json_encode($locale)|clear_json}, {json_encode($tags)|clear_json})">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-book"></i>
              {t}Books{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <h5>
              {if !isset($book->id)}
              {t}Creating Book{/t}
              {else}
              {t}Editing Book{/t}
              {/if}
            </h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_books}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              {if isset($book->id)}
              {acl isAllowed="BOOK_UPDATE"}
              <button class="btn btn-primary" data-text="{t}Updating{/t}..." href="{url name=admin_books_update id=$book->id}" name="continue" value="1" id="update-button">
                <i class="fa fa-save"></i>
                <span class="text">{t}Update{/t}</span>
              </button>
              {/acl}
              {else}
              {acl isAllowed="BOOK_CREATE"}
              <button class="btn btn-primary" data-text="{t}Saving{/t}..." href="{url name=admin_books_create}" name="continue" value="1" id="save-button">
                <i class="fa fa-save"></i>
                <span class="text">{t}Save{/t}</span>
              </button>
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
              <label for="title" class="form-label">{t}Title{/t}</label>
              <div class="controls">
                <input type="text" id="title" name="title" ng-model="title" value="{$book->title|default:""}" required class="form-control"/>
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
              <input {acl isNotAllowed="BOOK_AVAILABLE"} disabled="disabled" {/acl} type="checkbox" value="1" id="content_status" name="content_status" {if !isset($book) || $book->content_status eq 1}checked="checked"{/if}>
                <label for="content_status">{t}Published{/t}</label>
              </div>
            </div>

            <div class="form-group">
              <label for="category" class="form-label">{t}Category{/t}</label>
              <div class="controls">
                {include file="common/selector_categories.tpl" name="category" item=$book}
              </div>
            </div>

            <div class="form-group">
              <label for="metadata" class="form-label">{t}Keywords{/t}</label>
              <span class="help">{t}Separated by comas{/t}</span>
              <div class="controls">
                <onm-tag ng-model="tag_ids" locale="locale" tags-list="tags" check-new-tags="checkNewTags" get-suggested-tags="getSuggestedTags" load-auto-suggested-tags="loadAutoSuggestedTags" suggested-tags="suggestedTags" placeholder="{t}Write a tag and press Enter...{/t}"/>
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
                  <input class="form-control" type="datetime" id="date" name="starttime" value="{if $book->starttime neq '0000-00-00 00:00:00'}{$book->starttime}{/if}">
                  <span class="input-group-addon add-on">
                    <span class="fa fa-calendar"></span>
                  </span>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="position" class="form-label">{t}Position{/t}</label>
              <div class="controls">
                <input type="number" id="position" name="position" value="{$book->position}">
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
