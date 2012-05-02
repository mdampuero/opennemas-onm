<ul class="pills clearfix">
            {*{section name=as loop=$types_content}
                <li>
                    {assign var=ca value=`$types_content[as]`}
                    <a href="litter.php?action=list&amp;mytype={$ca}" {if $mytype==$ca}class="active"{/if}>{$types_content[as]}</a>
                </li>
            {/section} *}
            {is_module_activated name="ARTICLE_MANAGER"}
            {acl isAllowed="ARTICLE_TRASH"}
                <li><a href="{url name=admin_trash mytype=article}" {if $mytype=='article'}class="active"{/if}>{t}Articles{/t}</a></li>
            {/acl}{/is_module_activated}

            {is_module_activated name="OPINION_MANAGER"}
            {acl isAllowed="OPINION_TRASH"}
                <li><a href="{url name=admin_trash mytype=opinion}" {if $mytype=='opinion'}class="active"{/if}>{t}Opinions{/t}</a></li>
            {/acl}{/is_module_activated}

            {is_module_activated name="ADS_MANAGER"}
            {acl isAllowed="ADVERTISEMENT_TRASH"}
                <li><a href="{url name=admin_trash mytype=advertisement}" {if $mytype=='advertisement'}class="active"{/if}>{t}Ads{/t}</a></li>
            {/acl}{/is_module_activated}

            {is_module_activated name="COMMENT_MANAGER"}
            {acl isAllowed="COMMENT_TRASH"}
                <li><a href="{url name=admin_trash mytype=comment}" {if $mytype=='comment'}class="active"{/if}>{t}Coments{/t}</a></li>
            {/acl}{/is_module_activated}

            {is_module_activated name="ALBUM_MANAGER"}
            {acl isAllowed="ALBUM_TRASH"}
                <li><a href="{url name=admin_trash mytype=album}" {if $mytype=='album'}class="active"{/if}>{t}Albums{/t}</a></li>
            {/acl}{/is_module_activated}

            {is_module_activated name="IMAGE_MANAGER"}
            {acl isAllowed="IMAGE_TRASH"}
                <li><a href="{url name=admin_trash mytype=photo}" {if $mytype=='photo'}class="active"{/if}>{t}Images{/t}</a></li>
            {/acl}{/is_module_activated}

            {is_module_activated name="VIDEO_MANAGER"}
            {acl isAllowed="VIDEO_TRASH"}
                <li><a href="{url name=admin_trash mytype=video}" {if $mytype=='video'}class="active"{/if}>{t}Videos{/t}</a></li>
            {/acl}{/is_module_activated}

            {is_module_activated name="FILE_MANAGER"}
            {acl isAllowed="FILE_DELETE"}
                <li><a href="{url name=admin_trash mytype=attachment}" {if $mytype=='attachment'}class="active"{/if}>{t}Files{/t}</a></li>
            {/acl}{/is_module_activated}

            {is_module_activated name="POLL_MANAGER"}
            {acl isAllowed="POLL_DELETE"}
                <li><a href="{url name=admin_trash mytype=poll}" {if $mytype=='poll'}class="active"{/if}>{t}Polls{/t}</a></li>
            {/acl}{/is_module_activated}

            {is_module_activated name="STATIC_PAGES_MANAGER"}
            {acl isAllowed="STATIC_DELETE"}
                <li><a href="{url name=admin_trash mytype=static_page}" {if $mytype=='static_page'}class="active"{/if}>{t}Static Pages{/t}</a></li>
            {/acl}{/is_module_activated}
            {is_module_activated name="WIDGET_MANAGER"}
            {acl isAllowed="WIDGET_DELETE"}
                <li><a href="{url name=admin_trash mytype=widget}" {if $mytype=='widget'}class="active"{/if}>{t}Widgets{/t}</a></li>
            {/acl}{/is_module_activated}
        </ul>