{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EFE importer{/t} :: {t 1=$article->title|truncate:40:"..."}Importing article "%1"{/t}</h2></div>
    </div>
</div>
<div class="wrapper-content">
    <form action="{url name=admin_news_agency_import id=$id}" method="POST">

    {render_messages}
    <div class="panel">
        <h2>{t}You are about to import one article with the next data:{/t}</h2>

        <dl>
            <dt>{t}Title{/t}</dt>
            <dd>{$article->title}</dd>
            {if $article->summary}
                <dt>{t}Summary{/t}</dt>
                <dd>{$article->summary}</dd>
            {/if}
            <dt>{t}In which category you want to import this element?{/t}</dt>
            <dd>
                <select name="category">
                    {html_options options=$categories}
                </select>
            </dd>
        </dl>

    </div><!-- / -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">{t}Import{/t}</button>
        <a class="btn" onclick="history.go(-1)">{t}Go back{/t}</a>
    </div>
    </form>
</div>
{/block}
