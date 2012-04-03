 <div id="content-provider" class="clearfix" title="{t}Available contents{/t}">
    <div class="content-provider-block-wrapper wrapper-content clearfix">
        <ul>
            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/opinion/opinion.php?action=content-provider&amp;category={$category}">{t}Opinions{/t}</a>
            </li>

            <li>
                <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/widget/widget.php?action=content-provider&amp;category={$category}">{t}Widgets{/t}</a>
            </li>

            <li>
                <a href="#{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/album/album.php?action=content-provider&amp;category={$category}">{t}Albums{/t}</a>
            </li>

            <li>
                <a href="#{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/specials/especial.php?action=content-provider&amp;category={$category}">{t}Specials{/t}</a>
            </li>

            <li>
                <a href="#{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/letter/letter.php?action=content-provider&amp;category={$category}">{t}Letters{/t}</a>
            </li>
        </ul>
    </div>

</div><!-- /content-provider -->