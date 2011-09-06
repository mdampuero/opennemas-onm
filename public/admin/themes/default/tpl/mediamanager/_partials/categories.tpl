<ul class="pills">
    <li>
        <a href="mediamanager.php?listmode={$listmode|default:""}&category=GLOBAL" {if $category==0}class="active"{/if}>
            {t}GLOBAL{/t}</a>
    </li>
    {* <li>
         <a href="{$home}?listmode={$listmode}&category=3" {if $category==3}class="active"{/if}>
             ALBUMS</a>
     </li>
     *}
    {acl isAllowed="ADVERTISEMENT_ADMIN"}
    <li>
        <a href="{$smarty.server.PHP_SELF}?listmode={$listmode|default:""}&category=2" {if $category==2}class="active"{/if}>
            {t}ADS{/t}</a>
    </li>
    {/acl}
    {include file="menu_categories.tpl" home="mediamanager.php?listmode="}
</ul>
