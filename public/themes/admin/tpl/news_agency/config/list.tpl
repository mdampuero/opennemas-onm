{extends file="base/admin.tpl"}

{block name="content"}
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}News agency{/t} :: {t}Configuration{/t}
                    </h4>
                </li>
            </ul>
        </div>
        <div class="all-actions pull-right">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <a class="btn btn-link" href="{url name=admin_news_agency}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                        <span class="fa fa-reply"></span>
                    </a>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <a class="btn btn-primary" href="{url name=admin_news_agency_server_create}">
                        {t}Add server{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="content">

    {render_messages}

    <div class="grid simple">
        <div class="grid-body no-padding">
            <table id="source-list" class="table table-condensed">
                <tr>
                    <th>{t}Source name{/t}</th>
                    <th class="center">{t}Sync from{/t}</th>
                    <th class="center" style="width:1px">{t}Activated{/t}</th>
                </tr>

                {foreach $servers as $server}
                <tr id="{$server['id']}">
                    <td class="server_name">
                        {$server['name']}
                        <div class="listing-inline-actions">
                            <a class="link" href="{url name=admin_news_agency_server_show id=$server['id']}" class="btn edit"><i class="fa fa-pencil"></i> Editar</a>
                            <a class="link"  href="{url name=admin_news_agency_server_clean_files id=$server['id']}" class="btn" title="{t}Removes the synchronized files for this source{/t}"><i class="fa fa-fire"></i> {t}Remove local files{/t}</a>
                            <a class="link link-danger" href="#" data-url="{url name=admin_news_agency_server_delete id=$server['id']}" class="btn btn-danger del-server"><i class="fa fa-trash-o"></i> {t}Remove{/t}</a>
                        </div>
                    </td>
                    <td class="server_name nowrap center">{$sync_from[$server['sync_from']]}</td>
                    <td class="server_name right">
                        <a class="btn" href="{url name=admin_news_agency_server_toogle id=$server['id']}" title="{t}Activate server{/t}">
                            {if $server['activated'] == 1}
                                <i class="fa fa-check"></i>
                            {else}
                                <i class="fa fa-times"></i>
                            {/if}
                        </a>
                    </td>
                </tr>
                {foreachelse}

                <tr>
                    <td colspan="4" class="center">No servers available</td>
                </tr>
                {/foreach}
            </table>
        </div>
    </div>
</div>
{include file="news_agency/modals/_modal_remove_config.tpl"}
{/block}
