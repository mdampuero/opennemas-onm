        <ul class="tabs2">
            <li>
                <a href="mediamanager.php?listmode={$listmode}&category=GLOBAL" {if $category==0}style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>
                    GLOBAL</a>
            </li>
            {* <li>
                 <a href="{$home}?listmode={$listmode}&category=3" {if $category==3} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>
                     ALBUMS</a>
             </li>
             *}
            <li>
                <a href="{$smarty.server.PHP_SELF}?listmode={$listmode}&category=2" {if $category==2} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>
                    PUBLICIDAD</a>
            </li>
            {include file="menu_categorys.tpl" home="mediamanager.php?listmode="}

        </ul>

        <br />
        <br />