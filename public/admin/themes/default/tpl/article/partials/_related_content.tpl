<div style="width:50%;float:left;" >
    {include file="common/content_provider/content_provider.tpl"}
</div>
<div style="width:50%;float:left;" >
    <div id="frontpage_related" class="column-receiver">
        <h5>{t}Related in frontpage{/t}</h5>
        <hr>
        <ul class="content-receiver" >
            {section name=d loop=$contentsRight}
                <li class="" data-type="{$contentsRight[d]->content_type}" data-id="{$contentsRight[d]->pk_content}">
                    {$contentsRight[d]->created|date_format:"%d-%m-%Y"}:{$contentsRight[d]->title|clearslash}
                </li>
            {/section}
        </ul>
    </div>

    <div id="inner_related" class="column-receiver">
            <h5>{t}Related in inner{/t}</h5>
            <hr>
            <ul class="content-receiver" >
            {section name=d loop=$contentsLeft}
                <li class="" data-type="{$contentsLeft[d]->content_type}" data-id="{$contentsLeft[d]->pk_content}">
                    {$contentsLeft[d]->created|date_format:"%d-%m-%Y"}:{$contentsLeft[d]->title|clearslash}
                </li>
            {/section}
            </ul>
    </div>
    {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
    <div id="home_related" class="column-receiver">
        <h5>{t}Related in home{/t}</h5>
        <hr>
        <ul class="content-receiver" >
            {section name=d loop=$contentsRight}
                <li class="" data-type="{$contentsRight[d]->content_type}" data-id="{$contentsRight[d]->pk_content}">
                    {$contentsRight[d]->created|date_format:"%d-%m-%Y"}:{$contentsRight[d]->title|clearslash}
                </li>
            {/section}
        </ul>
    </div>
    {/is_module_activated}
</div>