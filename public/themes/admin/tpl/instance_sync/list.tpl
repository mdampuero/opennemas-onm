{extends file="base/admin.tpl"}
{block name="header-css" append}
<style type="text/css">
    .colorpicker_viewer {
        border-top-right-radius: 3px !important;
        border-bottom-right-radius: 3px !important;
        display:block;

    }
</style>
{/block}

{block name="content"}
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}Instance Synchronization{/t} :: {t}Settings{/t}
                        </h4>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a href="{url name=admin_instance_sync_create}" class="btn btn-primary" title="{t}Add site to sync{/t}">
                                {t}Add site{/t}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<div class="content">

    <div class="warnings-validation"></div>

    <form action="{url name=admin_instance_create}" method="GET" id="formulario">

    	{render_messages}

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                {if count($elements) >0}
                    <th>{t}Site Url{/t}</th>
                    <th style='width:45% !important;'>{t}Categories to Sync{/t}</th>
                    <th style="width:10% !important;">{t}Color{/t}</th>
                    <th style="width:5% !important;">{t}Actions{/t}</th>
                {else}
                    <th coslpan=4>&nbsp;</th>
                {/if}
                </tr>
            </thead>

            <tbody>
                {foreach $elements as $num => $config}
                    {foreach $config as $site => $categories}
                    <tr>
                        <td>{$site}</td>
                        <td>
                            {$categories|implode:", "}
                        </td>
                        <td>
                            <div class="colorpicker_viewer" style="background-color:#{$site_color[$site]};"></div>
                        </td>
                        <td class="right nowrap">
                            <div class="btn-group">
                                <a href="{url name=admin_instance_sync_show site_url=$site}"
                                   title="{t}Edit{/t}" class="btn">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="{url name=admin_instance_sync_delete site_url=$site}"
                                   title="{t}Delete{/t}"  class="btn btn-danger">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </div>
                        </td>

                    </tr>
                    {/foreach}
                {foreachelse}
                <tr>
                    <td colspan=4 class="empty">
                        <h4>{t}There are no synchronize settings available{/t}</h4>
                        <p>{t}Try adding one site to synchronize on the config button above.{/t}</p>
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <div class="pagination">
                            {$pagination->links|default:"&nbsp;"}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
	</form>
</div>
{/block}
