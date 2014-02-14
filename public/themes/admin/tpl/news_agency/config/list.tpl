{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });
});
</script>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}News agency{/t} :: {t}Configuration{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{url name=admin_news_agency_server_create}">
                    <img src="{$params.IMAGE_DIR}list-add.png" alt="{t}Save and exit{/t}"><br />
                    {t}Add server{/t}
                </a>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_news_agency}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

    <table id="source-list" class="table-condensed table">
        <tr>
            <th>{t}Source name{/t}</th>
            <th class="center">{t}Sync from{/t}</th>
            <th class="center">{t}Activated{/t}</th>
            <th style="width:10px;" class="center">{t}Actions{/t}</th>
        </tr>

        {foreach $servers as $server}
        <tr id="{$server['id']}">
            <td class="server_name">{$server['name']}</td>
            <td class="server_name nowrap center">{$sync_from[$server['sync_from']]}</td>
            <td class="server_name center">
                <a class="btn" href="{url name=admin_news_agency_server_toogle id=$server['id']}" title="{t}Activate server{/t}">
                    {if $server['activated'] == 1}
                        <i class="icon16 icon-ok"></i>
                    {else}
                        <i class="icon16 icon-remove"></i>
                    {/if}
                </a>
            </td>
            <td class="right">
                <div class="btn-group">
                    <a href="{url name=admin_news_agency_server_show id=$server['id']}" class="btn edit"><i class="pencil"></i> Editar</a>
                    <a href="{url name=admin_news_agency_server_clean_files id=$server['id']}" class="btn" title="{t}Removes the synchronized files for this source{/t}"><i class="icon-fire"></i></a>
                    <a href="#" data-url="{url name=admin_news_agency_server_delete id=$server['id']}" class="btn btn-danger del-server"><i class="icon-trash icon-white"></i></a>
                </div>
            </td>
        </tr>
        {foreachelse}

        <tr>
            <td colspan="4" class="center">No servers available</td>
        </tr>
        {/foreach}
    </table>
</div>
{include file="news_agency/modals/_modal_remove_config.tpl"}
{/block}
