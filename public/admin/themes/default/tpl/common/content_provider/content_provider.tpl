{* its try to be a generic content_types provider for related_contents, bulletin & specials manager. *}
 <div id="content-provider" class="related-content-provider clearfix" >
    <ul>
        {is_module_activated name="ARTICLE_MANAGER"}
        <li>
            <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/article.php?action=related-provider-category&amp;category={$category}">{t}Articles{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="OPINION_MANAGER"}
        <li>
            <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/opinion/opinion.php?action=related-provider&amp;category={$category}">{t}Opinions{/t}</a>
        </li>
        {/is_module_activated}
        {is_module_activated name="ALBUM_MANAGER"}
        <li>
            <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/album/album.php?action=related-provider&amp;category={$category}">{t}Albums{/t}</a>
        </li>
        {/is_module_activated}
            {is_module_activated name="POLL_MANAGER"}
        <li>
            <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/poll/poll.php?action=related-provider&amp;category={$category}">{t}Polls{/t}</a>
        </li>
        {/is_module_activated}
        {is_module_activated name="VIDEO_MANAGER"}
        <li>
            <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/video/video.php?action=related-provider&amp;category={$category}">{t}Videos{/t}</a>
        </li>
        {/is_module_activated}
        {is_module_activated name="FILE_MANAGER"}
        <li>
            <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/files/files.php?action=related-provider&amp;category={$category}">{t}Files{/t}</a>
        </li>
        {/is_module_activated}
        {is_module_activated name="ADVANCED_SEARCH"}
        <li>
            <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/search_advanced/search_advanced.php?action=content-provider-related&amp;">{t}Search{/t}</a>
        </li>
        {/is_module_activated}

    </ul>
</div><!-- /content-provider -->
