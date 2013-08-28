{extends file="base/base.tpl"}

{block name="content"}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Instance Manager{/t}</h2>
        </div>
        <ul class="old-button">
            <li>
                <a href="{url name=manager_instance_create}" class="admin_add"
                   title="{t}New widget{/t}">
                    <img border="0" src="{$params.COMMON_ASSET_DIR}images/list-add.png" title="" alt="" />
                    <br />{t}New{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
    {render_messages}
    <form action="{url name=manager_instances}" method="get" name="formulario" id="formulario">
        <div class="table-info clearfix">
            <div class="pull-left">
                {$pagination->_totalItems} instances
                <a href="{url name=manager_instances_list_export filter_name=$filter_name}">{image_tag src="{$params.COMMON_ASSET_DIR}images/csv.png" base_url=""} Export list</a>
            </div>
            <div class="pull-right form-inline">
                <input type="text" id="username" placeholder="{t}Filter by name{/t}" name="filter_name" onchange="this.form.submit();" value="{$filter_title}" />
                <label for="usergroup">
                    {t}Per page{/t}
                    <select id="usergroup" name="filter_per_page" onchange="this.form.submit();">
                         <option value="10" {if $per_page eq 10}selected{/if}>10</option>
                         <option value="20" {if $per_page eq 20}selected{/if}>20</option>
                         <option value="50" {if $per_page eq 50}selected{/if}>50</option>
                         <option value="100" {if $per_page eq 100}selected{/if}>100</option>
                    </select>
                </label>
                <input type="hidden" name="page" value="1" />
                <button type="submit" class="btn">{t}Search{/t}</button>
            </th>

            </div>
        </div>

        <table class="table table-hover table-condensed" >

            <thead>
                {if count($instances) > 0}
                <th width="15px">{t}#{/t}</th>
                <th width="200px">{t}Name{/t}</th>
                <th width="200px">{t}Domains{/t}</th>
                <th width="200px">{t}Contact{/t}</th>
                <th class="center">{t}Last access{/t}</th>
                <th width="100px" class="center">{t}Created{/t}</th>
                <th class="center" width="10px">{t}Activated{/t}</th>
                <th class="center" width="10px">{t}Actions{/t}</th>
                {else}
                <th scope="col" colspan=4>&nbsp;</th>
                {/if}
            </thead>

            <tbody>
                {foreach from=$instances item=instance name=instance_list}
                <tr>
                    <td>
                        {$instance->id}
                    </td>
                    <td>
                        <a href="{url name=manager_instance_show id=$instance->id}" title="{t}Edit{/t}">
                            {$instance->name}
                        </a>
                    </td>
                    <td>
                        {foreach from=$instance->domains item=domain name=instance_domains}
                            <a href="http://{$domain}" target="_blank" title="{$instance->name}">{$domain}</a><br/>
                        {/foreach}
                    </td>
                    <td>
                        {if !empty($instance->configs['contact_mail'])}
                        <a href="mailto:{$instance->configs['contact_mail']}" title="Send an email to the instance manager">{$instance->configs['contact_mail']}</a>
                        {/if}
                    </td>
                    <td class="center">
                        {datetime date=$instance->configs['last_login']}
                    </td>
                     <td class="nowrap center">
                        {$instance->configs['site_created']}
                    </td>
                    <td class="center">
                        {if $instance->activated == 1}
                        <a href="{url name=manager_instance_toggleavailable id=$instance->id}" title="{t}Published{/t}">
                            <img src="{$params.COMMON_ASSET_DIR}images/publish_g.png" border="0" alt="{t}Published{/t}" /></a>
                        {else}
                        <a href="{url name=manager_instance_toggleavailable id=$instance->id}" title="{t}Unpublished{/t}">
                            <img src="{$params.COMMON_ASSET_DIR}images/publish_r.png" border="0" alt="{t}Unpublished{/t}" /></a>
                        {/if}
                    </td>

                    <td class="right nowrap">
                        <div class="btn-group">
                            <a href="#" class="btn info" rel="popover" data-content="{t}Articles{/t}: {$instance->totals[1]}<br>
                                                                                     {t}Images{/t}: {$instance->totals[8]}<br>
                                                                                     {t}Ads{/t}: {$instance->totals[2]}<br>">
                                <i class="icon-info-sign"></i>
                            </a>
                            <a class="btn" href="{url name=manager_instance_show id=$instance->id}" title="{t}Edit{/t}">
                                <i class="icon-pencil"></i>
                            </a>
                            <a class="btn btn-danger del"
                                data-title="{$instance->name}"
                                data-url="{url name=manager_instance_delete id=$instance->id}"
                                href="{url name=manager_instance_delete id=$instance->id}" title="{t}Delete{/t}">
                                <i class="icon-trash icon-white"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                {foreachelse}
                <tr>
                    <td class="empty" colspan="8">{t}There is no available instances yet{/t}</td>
                </tr>
                {/foreach}
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="8" class="center">
                        <div class="pagination">
                            {$pagination->links}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>
{include file="instances/modals/_modalDelete.tpl"}
{/block}

{block name="footer-js"}
<script type="text/javascript">
$('.info').popover({
    title: "{t}Number of contents{/t}",
    html: true,
    placement: "top",
    trigger: 'hover',
});
</script>
{/block}
