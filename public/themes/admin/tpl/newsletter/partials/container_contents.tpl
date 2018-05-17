 <div id="content-provider" class="related-content-provider clearfix" >
    <ul class="nav nav-tabs" role="tablist" id="myTab">
        {is_module_activated name="ARTICLE_MANAGER"}
        <li role="presentation" class="active">
            <a href="{url name=admin_articles_content_provider_in_frontpage category=$category}" >{t}Articles in Frontpage{/t}</a>
        </li>
        <li role="presentation">
            <a href="{url name=admin_articles_content_provider_related category=$category}">{t}Articles{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="OPINION_MANAGER"}
        <li role="presentation">
            <a href="{url name=admin_opinions_content_provider_related category=$category}">{t}Opinions{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="ALBUM_MANAGER"}
        <li role="presentation">
            <a href="{url name=admin_albums_content_provider_related category=$category}">{t}Albums{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="VIDEO_MANAGER"}
        <li>
            <a href="{url name=admin_videos_content_provider_related category=$category}">{t}Videos{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="POLL_MANAGER"}
        <li role="presentation">
            <a href="{url name=admin_polls_content_provider_related category=$category}">{t}Polls{/t}</a>
        </li>
        {/is_module_activated}

        {is_module_activated name="FILE_MANAGER"}
        <li role="presentation">
            <a href="{url name=admin_files_content_provider_related category=$category}">{t}Files{/t}</a>
        </li>
        {/is_module_activated}
    </ul>
</div><!-- /content-provider -->
