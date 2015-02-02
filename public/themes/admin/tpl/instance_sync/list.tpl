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
                            <i class="fa fa-exchange"></i>
                            {t}Instance Synchronization{/t}
                        </h4>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h5>{t}Settings{/t}</h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a href="{url name=admin_instance_sync_create}" class="btn btn-primary" title="{t}Add site to sync{/t}">
                                <i class="fa fa-plus"></i>
                                {t}Add site{/t}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="content">

	{render_messages}

        <div class="grid simple">
            <div class="grid-body {if count($elements) >0}no-padding{/if}">
                {if count($elements) >0}
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>{t}Site Url{/t}</th>
                            <th style='width:45% !important;'>{t}Categories to Sync{/t}</th>
                            <th style="width:10% !important;">{t}Color{/t}</th>
                        </tr>
                    </thead>

                    <tbody>
                        {foreach $elements as $num => $config}
                            {foreach $config as $site => $categories}
                            <tr>
                                <td>
                                    {$site}
                                    <div class="listing-inline-actions">
                                        <a class="link" href="{url name=admin_instance_sync_show site_url=$site}"
                                           title="{t}Edit{/t}" class="btn">
                                            <i class="fa fa-pencil"></i> {t}Edit{/t}
                                        </a>
                                        <a class="link link-danger" href="{url name=admin_instance_sync_delete site_url=$site}"
                                           title="{t}Delete{/t}"  class="btn btn-danger">
                                            <i class="fa fa-trash-o"></i> {t}Remove{/t}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    {$categories|implode:", "}
                                </td>
                                <td>
                                    <div class="colorpicker_viewer" style="background-color:#{$site_color[$site]};"></div>
                                </td>
                            </tr>
                            {/foreach}
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
                {else}
                <div class="center">
                    <h4>{t}There are no synchronize settings available{/t}</h4>
                    <p>{t}Try adding one site to synchronize on the config button above.{/t}</p>
                </div>
                {/if}
            </div>
        </div>
	</form>
</div>
{/block}
