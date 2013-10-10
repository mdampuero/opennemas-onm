{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .map {
        background: white;
        padding: 15px;
        box-shadow: 0 0 20px #999;
        border-radius: 2px;
        margin:10px 0;
    }
    .map > div {
        height:500px;
    }
</style>
{/block}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/onm/jquery.datepicker.js"}

    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
    {script_tag src="/libs/gmaps.js"}
{/block}

{block name="footer-js" append}
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });
    $('[rel="tooltip"]').tooltip();
});
</script>
{/block}

{block name="content"}
<form id="formulario" name="form_upload" action="{url name=admin_image_update}" method="POST">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Editing image{/t}</h2></div>
            <ul class="old-button">
                {acl isAllowed="IMAGE_UPDATE"}
                <li>
                    <button type="submit" name="action" value="validate">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" >
                        <br />
                        {t}Save{/t}
                    </button>
                </li>
                {/acl}
                <li class="separator"></li>
                <li>
                    {if !isset($smarty.request.stringSearch)}
                        <a href="{url name=admin_images}" class="admin_add" title="{t}Go back{/t}">
                    {else}
                        <a href="{url name=admin_search stringSearch=$smarty.get.stringSearch} photo=on id=0"
                           title="Cancelar">
                    {/if}
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back to listing{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        {foreach from=$photos item=photo name=photo_show}
            {include file="image/_partials/photo_data.tpl" display='inline'}
        {/foreach}
    </div>
</form>
{/block}
