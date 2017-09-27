{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_news_agency_import source_id=$source_id id=$id}" method="POST">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}News Agency{/t}
                    </h4>
                </li>
                <li class="quicklinks hidden-xs">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks hidden-xs">
                    <h5>{t}Importing element{/t}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li>
                        <a href="{url name=admin_news_agency}" class="btn btn-link" title="{t}Go back to list{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li>
                        <span class="h-seperate"></span>
                    </li>
                    <li>
                        <button class="btn btn-primary" type="submit">
                            <span class="fa fa-cloud-download"></span> <span class="hidden-xs">{t}Import{/t}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="content">


    <div class="grid simple">
        <div class="grid-title">
          <h5>{t}You are about to import one article, please select a category where you want to import this content.{/t}</h5>
        </div>
        <div class="grid-body">

            <div class="row">
                <div class="col-md-7">
                    <h4>{$article->title}</h4>
                    {if $article->summary}
                        {$article->summary}
                    {else}
                        {$article->body|clearslash|truncate:400:"..."}
                    {/if}
                </div>
                <div class="col-md-1 center">
                    <span class="fa fa-chevron-down fa-4x visible-xs visible-sm"></span>
                    <span class="fa fa-chevron-right fa-4x hidden-xs hidden-sm" style="padding-top:30px"></span>
                </div>
                <div class="col-md-4" style="padding-top:20px">
                    <div class="form-group">
                        <label for="category" class="form-label">
                                {t}In which category you want to import this element?{/t}
                        </label>
                        <div class="controls">
                          <select id="category" name="category" required>
                            <option value="" >{t}- Select a category -{/t}</option>
                            {section name=as loop=$allcategorys}
                            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                            <option value="{$allcategorys[as]->pk_content_category}" data-name="{$allcategorys[as]->title}"
                              {if $allcategorys[as]->inmenu eq 0} class="unavailable" disabled{/if}
                              {if (($category == $allcategorys[as]->pk_content_category) && !is_object($article)) || $article->category eq $allcategorys[as]->pk_content_category}selected{/if}>
                              {$allcategorys[as]->title}</option>
                              {/acl}
                              {section name=su loop=$subcat[as]}
                              {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                              {if $subcat[as][su]->internal_category eq 1}
                              <option value="{$subcat[as][su]->pk_content_category}" data-name="{$subcat[as][su]->title}"
                                {if $subcat[as][su]->inmenu eq 0} class="unavailable" disabled {/if}
                                {if $category eq $subcat[as][su]->pk_content_category || $article->category eq $subcat[as][su]->pk_content_category}selected{/if} >
                                &nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                              {/if}
                              {/acl}
                              {/section}
                            {/section}
                            <option value="20" data-name="{t}Unknown{/t}" class="unavailable" {if ($category eq '20')}selected{/if}>{t}Unknown{/t}</option>
                          </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- / -->
</div>
</form>
{/block}
