<ul class="pills">
    <li>
        <a href="{$smarty.server.PHP_SELF}?action=statistics" {if $category==0}class="active"{/if}>
            {t}Global statistics{/t}
        </a>
    </li>
    {acl isAllowed="ADVERTISEMENT_ADMIN"}
    <li>
        <a href="{$smarty.server.PHP_SELF}?listmode={$listmode|default:""}&amp;category=2" {if $category==2}class="active"{/if}>
            {t}Advertisement{/t}</a>
    </li>
    {/acl}
    {include file="menu_categories.tpl" home=$home}
</ul>