<div class="widget-latest-opinions widget-latest-opinions-wrapper clearfix">
    <div class="widget-header">
        <img src="{$params.IMAGE_DIR}/sections/opinion.jpg" alt="Opiniones Nueva Tribuna" />
    </div>
    <div class="widget-content">
        <ul>
            {section name=a loop=$latestOpinions max=6}
            <li {if $smarty.section.a.last}class="last"{/if}>
                {if $latestOpinions[a]->name neq ""}
                <h5><a href="{$smarty.const.SITE_URL}{generate_uri content_type="opinion_author_frontpage" title=$latestOpinions[a]->name id=$latestOpinions[a]->fk_author}">{$latestOpinions[a]->name}</a></h5>
                {/if}
                <a href="{$smarty.const.SITE_URL}{generate_uri  content_type="opinion"
                                                                id=$latestOpinions[a]->id
                                                                date=$latestOpinions[a]->created
                                                                title=$latestOpinions[a]->title
                                                                category_name=$latestOpinions[a]->author_name_slug|default:"autor"}" class="comment-link">{$latestOpinions[a]->title|clearslash}</a>
            </li>
            {sectionelse}
            <li>
                En estos momentos no tenemos opiniones.
            </li>
            {/section}
        </ul>
    </div>
</div>