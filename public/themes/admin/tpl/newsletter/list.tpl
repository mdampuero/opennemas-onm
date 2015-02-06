{extends file="base/admin.tpl"}

{block name="header-js" append}
    {javascripts src="@AdminTheme/js/onm/jquery-functions.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Newsletters{/t}
                    </h4>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li>
                        <a class="btn btn-link" href="{url name=admin_newsletter_config}" class="admin_add" title="{t}Config newsletter module{/t}">
                            <span class="fa fa-cog"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li>
                        <a class="btn btn-white" href="{url name=admin_newsletter_subscriptors}" class="admin_add" id="submit_mult" title="{t}Subscriptors{/t}">
                            <span class="fa fa-users"></span>
                            {t}Subscriptors{/t}
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                        <a class="btn btn-primary" href="{url name=admin_newsletter_create}" accesskey="N" tabindex="1">
                            <i class="fa fa-plus"></i>
                            {t}Create{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="content">

    {render_messages}
    <div class="alert">
        {if $maxAllowed gt 0}
            {t 1=$lastInvoice 2=$totalSendings 3=$maxAllowed} Since %1 you have sent %2 of %3 allowed{/t}
        {else}
            {t 1=$lastInvoice 2=$totalSendings} Since %1 you have sent %2 emails{/t}
        {/if}
    </div>
    <div class="grid simple">
        <div class="grid-body no-padding">

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
                        <th class="right">{t}Sendings{/t}</th>
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

                            <div class="listing-inline-actions">
                                <a class="link" href="{url name=admin_newsletter_show_contents id=$newsletter->id}" title="{t}Edit{/t}" >
                                    <i class="fa fa-pencil"></i> {t}Edit{/t}
                                </a>

                                <a href="{url name=admin_newsletter_preview id=$newsletter->id}" title="{t}Preview{/t}" class="link">
                                    <i class="fa fa-eye"></i>
                                    {t}Show contents{/t}
                                </a>
                                {if $newsletter->sent lt 1}
                                <a class="del link link-danger"
                                   data-id="{$newsletter->id}"
                                   href="{url name=admin_newsletter_delete id=$newsletter->id}" >
                                    <i class="fa fa-trash-o"></i>
                                    {t}Remove{/t}
                                </a>
                                {/if}
                            </div>
                        </td>
                        <td class="left">
                            {$newsletter->created}
                        </td>
                        <td class="left">
                            {$newsletter->updated}
                        </td>
                        <td class="right">
                        {if $newsletter->sent gt 0}
                            {$newsletter->sent}
                        {else}
                            {t}No{/t}
                        {/if}
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
            </table>
            <div class="grid-footer clearfix ng-cloak" ng-if="!loading">
                <div class="pull-left pagination-info" ng-if="contents.length > 0">
                    <!-- {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total|number %] -->
                </div>
                <div class="pull-right" ng-if="contents.length > 0">
                    {$pagination|default:""}
                </div>
            </div>
        </div>
    </div>
</div>
</form>
{/block}
