<h4>{t}Categories{/t}</h4>
<div class="component">
    {if $hide_all != true}
        {acl hasCategoryAccess=0}
        <a href="{$home}" class="all {if $category == 'all'}active{/if}">{t}All categories{/t}</a>
        {/acl}
    {/if}
    {is_module_activated name="ADS_MANAGER"}
    {if $ads == true}
        {acl isAllowed="ADVERTISEMENT_CREATE"}
        <a href="{$home}&category=2" class="all {if $category == 2}active{/if}">{t}Advertisement{/t}</a>
        {/acl}
    {/if}
    {/is_module_activated}
    {if $opinion == true}
        {acl isAllowed="OPINION_CREATE"}
        <a href="{$home}&category=4" class="all {if $category == 4}active{/if}">{t}Opinion{/t}</a>
        {/acl}
    {/if}
    <ul class="categories">
        {section name=as loop=$allcategorys}
        {assign var=ca value=$allcategorys[as]->pk_content_category}
            <li >
                {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                <a  {if $home}href="{$home}&amp;category={$ca}"{/if}
                    id="link_{$ca}"
                    class="links {if $category==$ca}active{/if}">
                    {$allcategorys[as]->title}
                    {if $allcategorys[as]->inmenu eq 0}<span class="inactive">{t}(inactive){/t}</span>{/if}
                </a>
                {/acl}
                <ul>
                    {section name=su loop=$subcat[as]}
                    {assign var=subca value=$subcat[as][su]->pk_content_category}
                    {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                        {if $allcategorys[as]->pk_content_category eq $category}
                        <li>
                            <a  href="{$home}&amp;category={$subcat[as][su]->pk_content_category}"
                                class="links {if $category==$subca}active{/if}">
                                &rarr; {$subcat[as][su]->title}
                                {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}<span class="inactive">{t}(inactive){/t}</span>{/if}
                            </a>
                        </li>
                        {else}
                            {assign var=subca value=$subcat[as][su]->pk_content_category}
                            <li>
                                <a  href="{$home}&amp;category={$subcat[as][su]->pk_content_category}"
                                    class="links {if $category==$subca}active{/if}">
                                    &rarr; {$subcat[as][su]->title}
                                    {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}<span class="inactive">{t}(inactive){/t}</span>{/if}
                                </a>
                            </li>
                        {/if}
                    {/acl}
                    {/section}
                </ul>
            </li>
        {/section}

    </ul>
</div>