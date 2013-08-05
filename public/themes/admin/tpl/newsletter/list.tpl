{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Newsletters{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{url name=admin_newsletter_create}" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}/list-add.png" alt="{t}New newsletter{/t}"><br />{t}New newsletter{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_newsletter_config}" class="admin_add" title="{t}Config newsletter module{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Settings{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_newsletter_subscriptors}" class="admin_add" id="submit_mult" title="{t}Subscriptors{/t}">
                        <img src="{$params.IMAGE_DIR}authors.png" title="{t}Subscriptors{/t}" alt="{t}Subscriptors{/t}"><br />{t}Subscriptors{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div id="warnings-validation"></div>

        <div class="table-info clearfix">
            {if $maxAllowed gt 0}
            {t 1={{setting name=last_invoice}|truncate:10:false:false:''} 2=$totalSendings 3=$maxAllowed} Since %1 you have sent %2 of %3 newsletters{/t}
            {/if}
        </div>
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    {if count($newsletters) > 0}
                    <th style="width:15px;">
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
                    <th>{t}Title{/t}</th>
                    <th class="left"  style="width:150px;">{t}Created{/t}</th>
                    <th class="left"  style="width:150px;">{t}Updated{/t}</th>
                    <th class="left">{t}Sendings{/t}</th>
                    <th class="right" style="width:100px;">{t}Actions{/t}</th>
                    {else}
                    <th class="center">
                        &nbsp;
                    </th>
                    {/if}
                </tr>
            </thead>
            <tbody>
                {foreach name=c from=$newsletters item=newsletter}
                <tr data-id="{$newsletter->id}" style="cursor:pointer;">
                    <td>
                        <input type="checkbox" class="minput" id="selected_{$smarty.foreach.c.iteration}" name="selected_fld[]" value="{$newsletter->id}"  style="cursor:pointer;">
                    </td>
                    <td class="left">
                        {if !empty($newsletter->title)}
                            {$newsletter->title}
                        {else}
                            {t}Newsletter{/t}  -  {$newsletter->created}
                        {/if}
                    </td>
                    <td class="left">
                        {$newsletter->created}
                    </td>
                    <td class="left">
                        {$newsletter->updated}
                    </td>
                    <td class="left">
                    {if $newsletter->sent gt 0}
                        {$newsletter->sent}
                    {else}
                        {t}No{/t}
                    {/if}
                    </td>
                    <td style="padding:1px; font-size:11px;" class="right">
                        <div class="btn-group">
                            <a class="btn" href="{url name=admin_newsletter_show_contents id=$newsletter->id}" title="{t}Edit{/t}" >
                                <i class="icon-pencil"></i> {t}Edit{/t}
                            </a>

                            <a href="{url name=admin_newsletter_preview id=$newsletter->id}" title="{t}Preview{/t}" class="btn">
                                <i class="icon-eye-open"></i>
                            </a>
                            {if $newsletter->sent lt 1}
                            <a class="del btn btn-danger"
                               data-id="{$newsletter->id}"
                               href="{url name=admin_newsletter_delete id=$newsletter->id}" >
                                <i class="icon-trash icon-white"></i>
                            </a>
                            {/if}
                        </div>
                    </td>
                </tr>
                {foreachelse}
                <tr>
                    <td class="empty" colspan="8">
                        {t}There is no newsletters yet.{/t}
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <div class="pagination">
                            {$pagination|default:""}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>
</form>
{/block}
