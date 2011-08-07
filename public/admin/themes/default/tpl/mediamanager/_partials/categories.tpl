<ul class="tabs2">
    <li>
        <a href="mediamanager.php?listmode={$listmode|default:""}&category=GLOBAL" {if $category==0}style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>
            {t}GLOBAL{/t}</a>
    </li>
    {* <li>
         <a href="{$home}?listmode={$listmode}&category=3" {if $category==3} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>
             ALBUMS</a>
     </li>
     *}
    {acl isAllowed="ADVERTISEMENT_ADMIN"}
    <li>
        <a href="{$smarty.server.PHP_SELF}?listmode={$listmode|default:""}&category=2" {if $category==2} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>
            {t}ADS{/t}</a>
    </li>
    {/acl}
    {include file="menu_categorys.tpl" home="mediamanager.php?listmode="}

</ul>
