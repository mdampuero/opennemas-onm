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
            </ul>
        </div>
    </div>
</div>
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <ul class="old-button">
            <li>
                <button type="submit">
                    <img src="{$params.IMAGE_DIR}archive_no.png" alt="{t}Import{/t}" ><br />{t}Import{/t}
                </button>
            </li>
            <li>
                <a href="{url name=admin_news_agency}" class="admin_add" title="{t}Go back to list{/t}">
                <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

    <div class="alert alert-info">{t}You are about to import one article, please select a category where to import the article{/t}</div>
    <div class="form-horizontal panel">


        <div class="control-group">
            <label for="title" class="control-label">{t}Title{/t}</label>
            <div class="controls">
                <h4>{$article->title}</h4>
            </div>
        </div>
        <div class="control-group">
            <label for="summary" class="control-label">{t}Content{/t}</label>
            <div class="controls">
                {if $article->summary}
                    {$article->summary}
                {else}
                    {$article->body|clearslash|truncate:600:"..."}
                {/if}
            </div>
        </div>
        <div class="control-group">
            <label for="category" class="control-label">{t}Category{/t}</label>
            <div class="controls">
                <select name="category">
                    {html_options options=$categories}
                </select>
                <div class="help-block">
                    {t}In which category you want to import this element?{/t}
                </div>
            </div>
        </div>
    </div><!-- / -->
    </form>
</div>
{/block}
