{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    .adminlist td {
    padding-top:4px;
    padding-bottom:4px;
    }
    </style>
{/block}
{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EFE importer{/t} :: {t 1=$article->title|truncate:40:"..."}Importing article "%1"{/t}</h2></div>
    </div>
</div>
<div class="wrapper-content">
    <form action="{$smarty.server.PHP_SELF}" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>

    {render_messages}
    <div style="padding:30px; display:block;" class="panel">
        <h2>You are about to import one article with the next data:</h2>

        <dl>
            <dt>{t}Title{/t}</dt>
            <dd>{$article->title}</dd>
            <dt>{t}Summary{/t}</dt>
            <dd>{$article->texts[0]->summary}</dd>
            <dt>{t}In which category you want to import this element?{/t}</dt>
            <dd>
                <select name="category">
                    {html_options options=$categories}
                </select>
            </dd>
        </dl>

        
    </div><!-- / -->
    

    <div class="action-bar clearfix">
        <div class="right">
            <button type="submmit"   class="onm-button green">{t}Import{/t}</button>
        </div>
    </div>
    <input type="hidden" name="id" value="{$id}" placeholder="">
    <input type="hidden" id="action" name="action" value="import" />
    </form>
</div>
{/block}
