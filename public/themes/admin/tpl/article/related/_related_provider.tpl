 <div id="content-provider" class="related-content-provider tabs clearfix" >
    <ul>
        {is_module_activated name="ADVANCED_SEARCH"}
        <li>
            <a href="{url name=admin_search_content_provider related=1 category=$category}"><i class="icon-search"></i></a>
        </li>
        {/is_module_activated}

        {is_module_activated name="ARTICLE_MANAGER"}
        <li>
            <a href="{url name=admin_articles_content_provider_related category=$category}">{t}Articles{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="OPINION_MANAGER"}
        <li>
            <a href="{url name=admin_opinions_content_provider_related category=$category}">{t}Opinions{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="VIDEO_MANAGER"}
        <li>
            <a href="{url name=admin_videos_content_provider_related category=$category}">{t}Videos{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="ALBUM_MANAGER"}
        <li>
            <a href="{url name=admin_albums_content_provider_related category=$category}">{t}Albums{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="POLL_MANAGER"}
        <li>
            <a href="{url name=admin_polls_content_provider_related category=$category}">{t}Polls{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="SPECIAL_MANAGER"}
        {*<li>
            <a href="{url name=admin_specials_content_provider_related category=$category}">{t}Specials{/t}</a>
        </li> *}
        {/is_module_activated}

        {is_module_activated name="FILE_MANAGER"}
        <li>
            <a href="{url name=admin_files_content_provider_related category=$category}">{t}Files{/t}</a>
        </li>
        {/is_module_activated}
    </ul>
</div><!-- /content-provider -->
