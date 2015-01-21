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
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
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
                            <span class="fa fa-cloud-download"></span> {t}Import{/t}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="content">

    {render_messages}

    <div class="grid simple">

        <div class="grid-body">

            <div class="alert alert-block alert-info fade in">
              <button type="button" class="close" data-dismiss="alert"></button>
              <p>{t}You are about to import one article, please select a category where to import the article{/t}</p>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <h4>{$article->title}</h4>
                    {if $article->summary}
                        {$article->summary}
                    {else}
                        {$article->body|clearslash|truncate:400:"..."}
                    {/if}
                </div>
                <div class="col-md-1" style="padding-top:50px">
                    <span class="fa fa-chevron-right fa-4x"></span>
                </div>
                <div class="col-md-4" style="padding-top:20px">
                    <div class="form-group">
                        <label for="category" class="form-label">
                                {t}In which category you want to import this element?{/t}
                        </label>
                        <div class="controls">
                            <select name="category">
                                {html_options options=$categories}
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
