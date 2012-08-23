{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Sync Manager{/t} :: {t}Client side{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Add site to sync{/t}">
                    <img src="{$params.IMAGE_DIR}sync.png" title="{t}Add site to sync{/t}" alt="{t}Add site to sync{/t}" ><br />{t}Add site to sync{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    <div class="warnings-validation"></div>

    <form action="{$smarty.server.PHP_SELF}" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>

    	{render_messages}

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                {if count($elements) >0}
                    <th>{t}Site Url{/t}</th>
                    <th style='width:45% !important;'>{t}Categories to Sync{/t}</th>
                    <th style="width:10% !important;">{t}Color{/t}</th>
                    <th style="width:5% !important;">{t}Actions{/t}</th>
                </tr>
                {else}
                <tr>
                    <th coslpan=4>&nbsp;</th>
                </tr>
                {/if}
            </thead>

            <tbody>
                {foreach $elements as $num => $config}
                    {foreach $config as $site => $categories}
                    <tr>
                        <td>{$site}</td>
                        <td>
                            {foreach $categories as $category}
                                {$category}
                            {/foreach}
                        </td>
                        <td>
                            <div class="colopicker_viewer"
                                 style="background-color: #{$site_color[$site]};
                                        border-top-right-radius: 3px !important;
                                        border-bottom-right-radius: 3px !important;">
                            </div>
                        </td>
                        <td class="right nowrap">
                            <ul class="action-buttons">
                                <li>
                                    <a href="{$smarty.server.PHP_SELF}?action=edit&amp;site_url={$site}"
                                       title="{t}Edit{/t}">
                                        <img src="{$params.IMAGE_DIR}edit.png" />
                                    </a>
                                </li>
                                <li>
                                    <a href="{$smarty.server.PHP_SELF}?action=delete&amp;site_url={$site}"
                                       title="{t}Delete{/t}">
                                        <img src="{$params.IMAGE_DIR}trash.png" />
                                    </a>
                                </li>
                            </ul>
                        </td>

                    </tr>
                    {/foreach}
                {foreachelse}
                <tr>
                    <td colspan=4 class="empty">
                        <h2>
                            <b>{t}There are no synchorinize settings available{/t}</b>
                        </h2>
                        <p>{t}Try adding one site to synchronize on the config button above.{/t}</p>
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr class="pagination">
                    <td colspan="4">
                        <div class="pagination">
                            {$pagination->links|default:""}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
	</form>
</div>
{/block}
