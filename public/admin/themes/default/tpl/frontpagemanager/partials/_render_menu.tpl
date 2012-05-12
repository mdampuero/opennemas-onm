<ul id="categories" class="pills">
{section  name=m loop=$menuItems}
    {if $menuItems[m]->categoryID neq 4}
    {acl hasCategoryAccess=$menuItems[m]->categoryID}
    <li class="cat {if count($menuItems[m]->submenu) > 0}with-subcategories {/if} {if $category eq $menuItems[m]->categoryID} active {/if}">
        <a href="{url name=admin_frontpage_list category=$menuItems[m]->categoryID}" title="Sección: {$menuItems[m]->title}"
            class="{if $category eq $menuItems[m]->categoryID || ($datos_cat[0]->fk_content_category neq '0' && $menuItems[m]->categoryID eq $datos_cat[0]->fk_content_category)} active{/if}">
            {$menuItems[m]->title}
        </a>

        {if count($menuItems[m]->submenu) > 0}
            {assign value=$menuItems[m]->submenu var=submenu}
            <ul class="nav">
                {section  name=s loop=$submenu}
                    {acl hasCategoryAccess=$submenu[s]->categoryID}
                        <li class="subcat {if $category eq $submenu[s]->categoryID}active{/if}">
                            <a href="{url name=admin_frontpage_list category=$submenu[s]->categoryID}" title="{$submenu[s]->title|mb_lower}" class="cat {$menuItems[m]->link}{if $category eq $menuItems[m]->categoryID} active{/if}">
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
{/section}
</ul>
