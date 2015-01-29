{extends file="base/admin.tpl"}

{block name="content"}
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-file-o"></i>
                        {t}Files{/t}
                    </h4>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <h5>{t}Statistics{/t}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" href="{url name=admin_files}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
                            <span class="fa fa-reply"></span>
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
        <div class="grid-body no-padding">
            <div class="table-wrapper">
                <table class="table table-hover no-margin">
                    <thead>
                        <tr>
                            <th class="title" align="left">{t}Title{/t}</th>
                            <th width="40px" align="left">{t}Files (#){/t}</th>
                            <th width="40px" align="left">{t}Size (MB){/t}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {section name=c loop=$categorys}
                        <tr>
                            <td style="width:300;">
                                <a href="{url name=admin_files category=$categorys[c]->pk_content_category}">{$categorys[c]->title|clearslash|escape:"html"}</a>
                            </td>
                            <td style="width:10%;" class="center">
                                {$num_photos[c]}
                            </td>
                            <td style="width:10%;" class="center">
                                {math equation="x / y" x=$size[c]|default:0 y=1024*1024 format="%.2f"} MB
                            </td>

                        </tr>
                        {section name=su loop=$subcategorys[c]}
                        <tr>
                            <td style="padding: 5px 5px 5px 20px; width:300;">
                                <strong>=></strong> <a href="{url name=admin_files category=$subcategorys[c][su]->pk_content_category}">{$subcategorys[c][su]->title|clearslash|escape:"html"}</a>
                            </td>
                            <td style="padding: 0px 10px; width:10%;" class="center">
                                {$num_sub_photos[c][$subcategorys[c][su]->pk_content_category]}
                            </td>
                            <td style="padding: 0px 10px; width:10%;" class="center">
                                {math equation="x / y" x=$sub_size[c][$subcategorys[c][su]->pk_content_category]|default:0 y=1024*1024 format="%.2f"} MB</a>
                            </td>
                         </tr>
                        {/section}
                        {/section}
                        <tr>
                            <td colspan="2">
                            {section name=c loop=$num_especials}
                                <table width="100%">
                                <tr>
                                    <td >
                                         <b> {$num_especials[c].title|upper|clearslash|escape:"html"}</b>
                                    </td>
                                    <td style="width:40px;" align="left">
                                        {$num_especials[c].num}
                                    </td>
                                 </tr>

                                </table>
                            {/section}
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="left">
                                <strong>{t}TOTAL{/t}</strong>
                            </td>
                            <td style="width:10%;" class="center">
                                {$total_img}
                            </td>
                            <td style="width:10%;" class="center">
                                {math equation="x / y" x=$total_size|default:0 y=1024*1024 format="%.2f"} MB
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <input type="hidden" name="category" id="category" value="{$category}" />
        <input type="hidden" id="status" name="status" value="" />
        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</div>
{/block}
