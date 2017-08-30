{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script>
      $(document).ready(function($) {
        var btn   = $('.onm-button');

        $('.fileinput').fileinput({
          name: 'logo_path',
          uploadtype:'image'
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form action="{if $category->pk_content_category}{url name=admin_category_update id=$category->pk_content_category}{else}{url name=admin_category_create}{/if}" method="POST" name="formulario" id="formulario" enctype="multipart/form-data">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-bookmark"></i>
                {t}Categories{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks hidden-xs">
              <h5>{if $category->pk_content_category}{t}Editing category{/t}{else}{t}Creating category{/t}{/if}</h5>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks hidden-xs">
              <select id="language" name="language" ng-model="languages">
                <option value="">Gallego</option>
              </select>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_categories}" class="btn btn-link" title="{t}Config categories module{/t}">
                  <span class="fa fa-reply"></span>
                </a>
              </li>
              <li class="quicklinks"><span class="h-seperate"></span></li>
              <li class="quicklinks">
                <button type="submit" class="btn btn-primary" data-text="{t}Saving{/t}..." id="save-button">
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
                <label for="title" class="form-label">
                  {t}Title{/t}
                </label>
                <div class="controls">
                  <input type="text" id="title" name="title" value="{$category->title|default:""}" required class="form-control"/>
                </div>
              </div>
              {if isset($category) && !empty($category->name)}
              <div class="form-group">
                <label for="name" class="form-label">{t}Slug{/t}</label>
                <div class="controls">
                  <input type="text" id="name" name="name" readonly value="{$category->name|clearslash|default:""}"  required class="form-control"/>
                </div>
              </div>
              {/if}
              <div class="form-group">
                <label for="subcategory" class="form-label">
                  {t}Subsection of{/t}
                </label>
                <div class="controls">
                  <select name="subcategory" required>
                    <option value="0" {if !isset($category) || (!empty($category->fk_content_category) || $category->fk_content_category eq '0')}selected{/if}> -- </option>
                    {section name=as loop=$allcategorys}
                    <option value="{$allcategorys[as]->pk_content_category}" {if isset($category) && ($category->fk_content_category eq $allcategorys[as]->pk_content_category)}selected{/if}>{$allcategorys[as]->title}</option>
                    {/section}
                  </select>
                </div>
              </div>
              {if !empty($subcategorys)}
              <div class="form-group">
                <label class="form-label">
                  {t}Subsections{/t}
                </label>
                <div class="controls">
                  <table class="table table-hover no-margin" style="width:100%">
                    <thead>
                      <tr>
                        <th>{t}Title{/t}</th>
                        <th>{t}Internal name{/t}</th>
                        <th>{t}Type{/t}</th>
                        <th>{t}In menu{/t}</th>
                        <th class="right">{t}Actions{/t}</th>
                      </tr>
                    </thead>
                    {section name=s loop=$subcategorys}
                    <tr>
                      <td class="left">
                        {$subcategorys[s]->title}
                      </td>
                      <td class="left">
                        {$subcategorys[s]->name}
                      </td>
                      <td class="left">
                        {if $subcategorys[s]->internal_category eq 7}
                          <i class="fa fa-stack-overflow" uib-tooltip="{t}Albums{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 9}
                          <i class="fa fa-film" uib-tooltip="{t}Videos{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 11}
                          <i class="fa fa-pie-chart" uib-tooltip="{t}Polls{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 10}
                          <i class="fa fa-star" uib-tooltip="{t}Specials{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 14}
                          <i class="fa fa-newspaper-o" uib-tooltip="{t}News Stand{/t}"></i>
                        {elseif $subcategorys[s]->internal_category eq 15}
                          <i class="fa fa-book" uib-tooltip="{t}Books{/t}"></i>
                        {/if}
                      </td>
                      <td class="left">
                        {if $subcategorys[s]->inmenu==1} {t}Yes{/t} {else}{t}No{/t}{/if}
                      </td>
                      <td class="right">
                        <div class="btn-group">
                          <a class="btn btn-mini" href="{url name=admin_category_show id=$subcategorys[s]->pk_content_category}" title="Modificar">
                            <i class="fa fa-pencil"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                    {/section}
                  </table>
                </div>
              </div>
              {/if}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <div class="controls">
                  <div class="checkbox">
                    <input type="checkbox" id="inmenu" name="inmenu" value="1" {if $category->inmenu eq 1} checked="checked"{/if}>
                    <label for="inmenu" class="form-label">
                      {t}Available{/t}
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="controls">
                  <div class="checkbox">
                    <input type="checkbox" id="params[inrss]" name="params[inrss]" value="1"
                    {if isset($category->params['inrss']) && $category->params['inrss'] == true}checked="checked"{/if}>
                    <label for="params[inrss]" class="form-label">{t}Show in RSS{/t}</label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="internal_category" class="form-label">
                  {t}Category available for{/t}
                </label>
                <div class="controls">
                  <select name="internal_category" id="internal_category"  required>
                    <option value="1"
                    {if  (empty($category->internal_category) || $category->internal_category eq 1)} selected="selected"{/if}>{t}All contents{/t}</option>
                    {is_module_activated name="ALBUM_MANAGER"}
                    <option value="7"
                    {if isset($category) && ($category->internal_category eq 7)} selected="selected"{/if}>{t}Albums{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="VIDEO_MANAGER"}
                    <option value="9"
                    {if isset($category) && ($category->internal_category eq 9)} selected="selected"{/if}>{t}Video{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="POLL_MANAGER"}
                    <option value="11"
                    {if isset($category) && ($category->internal_category eq 11)} selected="selected"{/if}>{t}Poll{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="KIOSKO_MANAGER"}
                    <option value="14"
                    {if isset($category) && ($category->internal_category eq 14)} selected="selected"{/if}>{t}ePaper{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="SPECIAL_MANAGER"}
                    <option value="10"
                    {if isset($category) && ($category->internal_category eq 10)} selected="selected"{/if}>{t}Special{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="BOOK_MANAGER"}
                    <option value="15"
                    {if isset($category) && ($category->internal_category eq 15)} selected="selected"{/if}>{t}Book{/t}</option>
                    {/is_module_activated}
                    {acl isAllowed="MASTER"}
                    <option value="0"
                    {if isset($category) && ($category->internal_category eq 0)} selected="selected"{/if}>{t}Internal{/t}</option>
                    {/acl}
                  </select>
                </div>
              </div>
              <div class="form-group">
                {capture "websiteColor"}
                {setting name="site_color"}
                {/capture}
                <label for="color" class="form-label">
                  {t}Color{/t}
                </label>
                <div class="controls">
                  <div class="input-group">
                    <span class="input-group-addon" ng-if="color.indexOf('#') > -1" ng-style="{ 'background-color': color }">
                      &nbsp;&nbsp;&nbsp;&nbsp;
                    </span>
                    <span class="input-group-addon" ng-if="color.indexOf('#') <= -1" ng-style="{ 'background-color': '#' + color }">
                      &nbsp;&nbsp;&nbsp;&nbsp;
                    </span>
                    <input class="form-control" colorpicker="hex" id="color" name="color" ng-init="color='{$category->color|default:$smarty.capture.websiteColor|trim}'" ng-model="color" type="text">
                    <div class="input-group-btn">
                      <button class="btn btn-default" ng-click="color='{$category->color|default:$smarty.capture.websiteColor|trim}'" type="button">{t}Reset{/t}</button>
                    </div>
                  </div>
                </div>
              </div>
              {if isset($configurations) && !empty($configurations['allowLogo'])}
              <div class="form-group">
                <label for="logo_path" class="form-label">{t}Category logo{/t}</label>
                <div class="controls">
                  <div class="fileinput {if $category->logo_path}fileinput-exists{else}fileinput-new{/if}" data-trigger="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 140px; height: 140px;">
                    </div>
                    <div class="fileinput-exists fileinput-preview thumbnail" style="width: 140px; height: 140px;">
                      {if $category->logo_path}
                        <img src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/sections/{$category->logo_path}" style="max-width:200px;" >
                      {/if}
                    </div>
                    <div>
                      <span class="btn btn-file">
                        <span class="fileinput-new">{t}Add new photo{/t}</span>
                        <span class="fileinput-exists">{t}Change{/t}</span>
                        <input type="file"/>
                        <input type="hidden" name="logo_path" class="file-input" value="1">
                      </span>
                      <a href="#" class="btn btn-danger fileinput-exists delete" data-dismiss="fileinput">
                        <i class="fa fa-trash-o"></i>
                        {t}Remove{/t}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              {/if}
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
