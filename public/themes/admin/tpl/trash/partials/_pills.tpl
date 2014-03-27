<select class="select2" id="content_type_name" ng-model="shvs.search.content_type_name" data-label="{t}Content Type{/t}">
    <option value="-1">{t}-- All --{/t}</option>
    {is_module_activated name="ARTICLE_MANAGER"}
    {acl isAllowed="ARTICLE_TRASH"}
    <option value="article">{t}Articles{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="OPINION_MANAGER"}
    {acl isAllowed="OPINION_TRASH"}
        <option value="opinion">{t}Opinions{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="ADS_MANAGER"}
    {acl isAllowed="ADVERTISEMENT_TRASH"}
        <option value="advertisement">{t}Advertisements{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="KIOSKO_MANAGER"}
    {acl isAllowed="KIOSKO_TRASH"}
        <option value="kiosko">{t}Covers{/t}</option>
    {/acl}
    {/is_module_activated}

    {is_module_activated name="ALBUM_MANAGER"}
    {acl isAllowed="ALBUM_TRASH"}
        <option value="album">{t}Albums{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="IMAGE_MANAGER"}
    {acl isAllowed="IMAGE_TRASH"}
        <option value="photo">{t}Images{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="VIDEO_MANAGER"}
    {acl isAllowed="VIDEO_TRASH"}
        <option value="video">{t}Videos{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="FILE_MANAGER"}
    {acl isAllowed="FILE_DELETE"}
        <option value="attachment">{t}Files{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="POLL_MANAGER"}
    {acl isAllowed="POLL_DELETE"}
        <option value="poll">{t}Polls{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="SPECIAL_MANAGER"}
    {acl isAllowed="SPECIAL_DELETE"}
        <option value="special">{t}Specials{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="STATIC_PAGES_MANAGER"}
    {acl isAllowed="STATIC_DELETE"}
        <option value="static_page">{t}Static Pages{/t}</option>
    {/acl}{/is_module_activated}

    {is_module_activated name="WIDGET_MANAGER"}
    {acl isAllowed="WIDGET_DELETE"}
        <option value="widget">{t}Widgets{/t}</option>
    {/acl}{/is_module_activated}
</select>
