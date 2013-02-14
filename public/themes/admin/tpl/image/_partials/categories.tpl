<ul class="pills">
    <li>
        <a href="{url name=admin_images category=all}" {if $category == 'all'}class="active"{/if}>
            {t}All{/t}
        </a>
    </li>
    {acl isAllowed="ADVERTISEMENT_CREATE"}
    <li>
        <a href="{url name=admin_images category=2}" {if $category==2}class="active"{/if}>
            {t}Advertisement{/t}
        </a>
    </li>
    {/acl}
    {include file="menu_categories.tpl" home=$home}
</ul>