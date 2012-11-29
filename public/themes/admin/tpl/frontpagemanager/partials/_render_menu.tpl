<ul id="categories" class="pills">
    {acl hasCategoryAccess=0}
    <li>
        <a href="{url name=admin_frontpage_list category=home}" class="{if $category == 'home' || $category == 0}active{/if}">{t}Home{/t}</a>
    </li>
    {/acl}
{foreach from=$menuItems item=menuItem}
    {if $menuItem->type == 'category'}
    {acl hasCategoryAccess=$menuItem->categoryID}
    <li class="cat {if count($menuItem->submenu) > 0}with-subcategories {/if} {if $category eq $menuItem->categoryID} active {/if}">
        <a href="{url name=admin_frontpage_list category=$menuItem->categoryID}" title="Sección: {$menuItem->title}"
            class="{if $category eq $menuItem->categoryID || ($datos_cat[0]->fk_content_category neq '0' && $menuItem->categoryID eq $datos_cat[0]->fk_content_category)} active{/if}">
            {$menuItem->title}
        </a>

        {if count($menuItem->submenu) > 0}
            {assign value=$menuItem->submenu var=submenu}
            <ul class="nav">
                {section  name=s loop=$submenu}
                    {acl hasCategoryAccess=$submenu[s]->categoryID}
                        <li class="subcat {if $category eq $submenu[s]->categoryID}active{/if}">
                            <a href="{url name=admin_frontpage_list category=$submenu[s]->categoryID}" title="{$submenu[s]->title|mb_lower}" class="cat {$menuItem->link}{if $category eq $menuItem->categoryID} active{/if}">
                                {$submenu[s]->title}
                            </a>
                        </li>
                    {/acl}
                {/section}
            </ul>
        {/if}
    </li>
    {/acl}
    {/if}
{/foreach}
</ul>
