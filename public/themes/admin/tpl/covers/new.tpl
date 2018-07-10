{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $('#date').datetimepicker({
          format: 'YYYY-MM-DD',
          minDate: '{$cover->created|default:$smarty.now|date_format:"%Y-%m-%d"}'
        });

        $('.fileinput').fileinput({
          name: 'cover',
          uploadtype:'image'
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
<form action="{if !empty($cover->id)}{url name=admin_kiosko_update id=$cover->id}{else}{url name=admin_kiosko_create}{/if}" method="POST" ng-controller="CoverCtrl" ng-init="init({json_encode($cover)|clear_json}, {json_encode($locale)|clear_json}, {json_encode($tags)|clear_json})"  enctype="multipart/form-data">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-newspaper-o"></i>
              {t}Covers{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{if !isset($cover->id)}{t}Creating ePaper{/t}{else}{t}Editing ePaper{/t}{/if}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_kioskos}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit" id="update-button">
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
                  <input type="text" id="title" name="title" ng-model="title" value="{$cover->title|default:""}" required class="form-control"/>
                </div>
              </div>
              <div class="form-group">
                <label for="date" class="form-label">{t}Date{/t}</label>
                <div class="controls">
                  <div class="input-group">
                    <input class="form-control" type="text" id="date" name="date" value="{$cover->date}" required placeholder="{t}Click here to pick a date{/t}" aria-describedby="basic-addon2">
                    <span class="input-group-addon" id="basic-addon2"><span class="fa fa-calendar"></span></span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="price" class="form-label">{t}Price{/t}</label>
                <span class="help">{t}Split decimals with a dot{/t}.</span>
                <div class="controls">
                  <input type="number" step="any" id="price" name="price" value="{$cover->price|number_format:2:".":","|default:"0"}" required />
                </div>
              </div>
              <div class="form-group">
                <label for="type" class="form-label">{t}Type{/t}</label>
                <div class="controls">
                  <select name="type" id="type" required>
                    <option value="0" {if empty($cover) || $cover->type==0}selected{/if}>{t}Item{/t}</option>
                    <option value="1" {if $cover->type==1}selected{/if}>{t}Subscription{/t}</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="date" class="form-label">{t}File{/t}</label>
                <div class="controls">
                  <div class="fileinput {if $cover->name}fileinput-exists{else}fileinput-new{/if}" data-trigger="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 140px; height: 140px;">
                    </div>
                    <div class="fileinput-exists fileinput-preview thumbnail" style="width: 140px; height: 140px;">
                      {if $cover->path}
                        <img src="{$KIOSKO_IMG_URL}{$cover->path}{$cover->thumb_url|regex_replace:"/.pdf$/":".jpg"}" style="max-width:200px;" >
                      {/if}
                    </div>
                    <div>
                      <span class="btn btn-file">
                        <span class="fileinput-new">{t}Add PDF{/t}</span>
                        <span class="fileinput-exists">{t}Change{/t}</span>
                        <input type="file"/>
                        <input type="hidden" name="cover" class="file-input" value="1">
                      </span>
                      <a href="#" class="btn btn-danger fileinput-exists delete" data-dismiss="fileinput">
                        <i class="fa fa-trash-o"></i>
                        {t}Remove{/t}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <input type="hidden" id="id" name="id" value="{$cover->id}" />
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">

              <div class="form-group">
                <div class="checkbox">
                  <input type="checkbox" value="1" id="content_status" name="content_status" {if $cover->content_status eq 1}checked="checked"{/if}>
                  <label for="content_status">{t}Published{/t}</label>
                </div>
              </div>

              <div class="form-group">
                <div class="checkbox">
                  <input type="checkbox" value="1" id="favorite" name="favorite" {if $cover->favorite eq 1}checked="checked"{/if}>
                  <label for="favorite">{t}Favorite{/t}</label>
                </div>
              </div>
              <div class="form-group">
                <label for="category" class="form-label">{t}Category{/t}</label>
                <div class="controls">
                  <select name="category" id="category" required {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                    {section name=as loop=$allcategorys}
                      {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                        <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category || $cover->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                      {/acl}
                      {section name=su loop=$subcat[as]}
                        {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                          <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category || $cover->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;L&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                        {/acl}
                      {/section}
                    {/section}
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="metadata" class="form-label">{t}Keywords{/t}</label>
                <span class="help">{t}List of words separated by commas{/t}.</span>
                <div class="controls">
                  <onm-tag ng-model="tag_ids" locale="locale" tags-list="tags" check-new-tags="checkNewTags" get-suggested-tags="getSuggestedTags" load-auto-suggested-tags="loadAutoSuggestedTags" suggested-tags="suggestedTags" placeholder="{t}Write a tag and press Enter...{/t}"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
