<div class="component">
    <a href="{$home}" {if $category == 'all'}class="active"{/if}>{t}All categories{/t}</a>
    <h4>{t}Other Categories{/t}</h4>
    <ul class="categories">
        {section name=as loop=$allcategorys}
        {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
            {assign var=ca value=$allcategorys[as]->pk_content_category}

            <li >
                <a  {if $home}href="{$home}&amp;category={$ca}"{/if}
                    id="link_{$ca}"
                    class="links {if $category==$ca}active{else}{if $ca eq $datos_cat[0]->fk_content_category}active {/if}{/if}" >
                    {$allcategorys[as]->title}
                </a>

                <ul>
                    {section name=su loop=$subcat[as]}
                    {assign var=subca value=$subcat[as][su]->pk_content_category}
                    {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                        {if $allcategorys[as]->pk_content_category eq $category}
                        <li>
                            <a  href="{$home}&amp;category={$subcat[as][su]->pk_content_category}"
                                class="links"
                                {if $category==$subca}active {else}{if $subca eq $datos_cat[0]->fk_content_category}active{/if} {/if}>
                                &rarr; {$subcat[as][su]->title}
                            </a>
                        </li>
                        {else}
                            {assign var=subca value=$subcat[as][su]->pk_content_category}
                            <li>
                                <a  href="{$home}&amp;category={$subcat[as][su]->pk_content_category}"
                                    class="links"
                                    {if $category==$subca}active {else}{if $subca eq $datos_cat[0]->fk_content_category}active{/if} {/if}>
                                    &rarr; {$subcat[as][su]->title}
                                </a>
                            </li>
                        {/if}
                    {/acl}
                    {/section}
                </ul>
            </li>
        {/acl}
        {/section}

    </ul>
</div>