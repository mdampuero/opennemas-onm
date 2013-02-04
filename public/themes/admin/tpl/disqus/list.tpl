{extends file="base/admin.tpl"}


{block name="header-css" append}
    {script_tag src="/jquery/jquery.min.js"}
<style type="text/css">

.iframe {
    margin-top:60px;
    width:100%;
    margin:0 auto;
    bottom:0;
    min-height:100%;
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
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('.iframe iframe').css('min-height', (jQuery(window).height() - 140) + 'px');
    jQuery(window).resize(function(){
        jQuery('.iframe iframe').css('min-height', (jQuery(window).height() - 140) + 'px');
    })
});
</script>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <h2 class="disqus">{t}Comments{/t}</h2>
        </div>
        <ul class="old-button">
            <li>
                <a href="{url name=admin_comments_disqus_config}" title="{t}Disqus module configuration{/t}">
                    <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config disqus module{/t}" alt="{t}Config Europapress module{/t}" ><br />{t}Config{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="iframe">
    {if !empty($disqus_shortname)}
    <iframe src="http://{$disqus_shortname}.disqus.com/admin/moderate/?template=wordpress" style="width: 100%; height: 80%; min-height:700px;"></iframe>
    {else}
        <div class="wrapper-content center">
            <h3>{t}Disqus not configured{/t}</h3>
        </div>
    {/if}
</div>
{/block}

{block name="copyright"}
{/block}