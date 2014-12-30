{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
.disqus .disqus-link { text-align: center;width: 25%;margin: 100px auto;padding: 15px 0;background-color: #444;}
.disqus a .disqus-link { font-size: 1.4em;color: #fff;}
.disqus a:hover { text-decoration: none;}
.disqus a:hover .disqus-link { background-color: #666;}
.disqus a .disqus-link img { width: 30px;margin-right: 10px;}
</style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Comments{/t}</h2>
        </div>
        <ul class="old-button">
            <li>
                <a href="{url name=admin_comments_disqus_config}" title="{t}Disqus module configuration{/t}">
                    <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png"><br />{t}Config{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="disqus">
    {if !empty($disqus_shortname) && !empty($disqus_secret_key)}
    <a href="http://{$disqus_shortname}.disqus.com/admin/moderate/" target="_blank">
        <div class="disqus-link">
            <img src="{$params.IMAGE_DIR}/disqus-icon.png" alt="Disqus" />
            {t}To moderate your Disqus comments, click here{/t}
        </div>
    </a>
    {else}
        <div class="wrapper-content center">
            <h3>{t}Disqus not configured{/t}</h3>
        </div>
    {/if}
</div>
{/block}

{block name="copyright"}
{/block}
