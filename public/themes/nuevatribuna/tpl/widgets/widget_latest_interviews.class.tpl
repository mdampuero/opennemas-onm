<div class="widget-latest-interviews widget-latest-interviews-wrapper clearfix">
    <div class="widget-header">
        <img src="{$params.IMAGE_DIR}/sections/entrevistas.gif" alt="Entrevistas Nueva Tribuna" />
    </div>
    <div class="widget-content">
        <ul>
            {section name=a loop=$latestInterviews max=$maxInterviews}
            <li {if $smarty.section.a.last}class="last"{/if}>
                <h5><strong><a href="{$smarty.const.SITE_URL}{$latestInterviews[a]->uri|clearslash}">{$latestInterviews[a]->subtitle}</a></strong></h5>
                <a href="{$smarty.const.SITE_URL}{$latestInterviews[a]->uri|clearslash}" class="comment-link">{$latestInterviews[a]->title|clearslash}</a>
            </li>
            {sectionelse}
            <li>
                En estos momentos no tenemos entrevistas.
            </li>
            {/section}
        </ul>
    </div>
</div>