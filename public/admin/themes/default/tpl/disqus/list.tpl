{extends file="base/admin.tpl"}


{block name="header-css" append}
<style type="text/css">

.iframe {
    margin-top:60px;
    width:100%;
    margin:0 auto;
    bottom:0;
}

iframe {
    margin:0 auto;
    min-height:100%%;
    border:0 none;
    overflow:visible;
}
.top-action-bar .title > * {
    display: inline-block;
    padding: 0;
}
</style>
{/block}

{block name="content"}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2 class="disqus">{t}Comment manager{/t}</h2>
            <ul class="old-button">
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Reload list{/t}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config disqus module{/t}" alt="{t}Config Europapress module{/t}" ><br />{t}Config{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="iframe">
    {if isset($disqus_shortname)}
    <iframe src="http://{$disqus_shortname}.disqus.com/admin/moderate/?template=wordpress" width="100%"></iframe>
    {else}
        {t}Disqus not configured{/t}
    {/if}
</div>
{/block}

{block name="copyright"}
{/block}