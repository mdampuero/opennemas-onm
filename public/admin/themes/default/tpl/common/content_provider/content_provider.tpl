{* its try to be a generic content_types provider for related_contents, bulletin & specials manager. *}

<div id="content-provider" class="tabs clearfix" title="{t}Available contents{/t}">
    <div class="content-provider-block-wrapper wrapper-content clearfix">
        <ul>
            {*is_module_activated name="ADVANCED_SEARCH"}
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/search_advanced/search_advanced.php?action=content-provider&amp;">{t}Search{/t}</a>
            </li>
            {/is_module_activated*}
            {is_module_activated name="ARTICLE_MANAGER"}
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/article.php?action=content-list-provider&amp;category={$category}&amp;page={$page}">{t}Articles{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="WIDGET_MANAGER"}
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/widget/widget.php?action=content-list-provider&amp;category={$category}&amp;page={$page}">{t}Widgets{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="OPINION_MANAGER"}
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/opinion/opinion.php?action=content-list-provider&amp;category={$category}&amp;page={$page}">{t}Opinions{/t}</a>
            </li>
            {/is_module_activated}

            {is_module_activated name="ALBUM_MANAGER"}
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/album/album.php?action=content-list-provider&amp;category={$category}&amp;page={$page}">{t}Albums{/t}</a>
            </li>
            {/is_module_activated}
            {*is_module_activated name="VIDEO_MANAGER"}
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/video/video.php?action=content-list-provider&amp;category={$category}&amp;page={$page}">{t}Videos{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="POLL_MANAGER"}
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/poll/poll.php?action=content-list-provider&amp;category={$category}">{t}Poll{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="LETTER_MANAGER"}
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/letter/letter.php?action=content-list-provider&amp;category={$category}">{t}Letter to the Editor{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="ADS_MANAGER"}
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/advertisement/advertisement.php?action=content-list-provider&amp;category={$category}">{t}Advertisement{/t}</a>
            </li>
            {/is_module_activated*}
        </ul>
    </div>

</div>