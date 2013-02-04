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
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Instance synchronization{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{url name=admin_instance_sync_create}" class="admin_add" title="{t}Add site to sync{/t}">
                    <img src="{$params.IMAGE_DIR}sync.png" title="{t}Add site to sync{/t}" alt="{t}Add site to sync{/t}" ><br />{t}Add site to sync{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

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
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="{url name=admin_instance_sync_delete site_url=$site}"
                                   title="{t}Delete{/t}"  class="btn btn-danger">
                                    <i class="icon-trash icon-white"></i>
                                </a>
                            </div>
                        </td>

                    </tr>
                    {/foreach}
                {foreachelse}
                <tr>
                    <td colspan=4 class="empty">
                        <h4>{t}There are no synchorinize settings available{/t}</h4>
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
