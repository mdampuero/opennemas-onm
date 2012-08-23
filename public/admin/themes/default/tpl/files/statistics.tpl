{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="GET" name="formulario" id="formulario">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Files manager :: {/t}{t}General statistics{/t}</h2></div>

    </div>
</div>
<div class="wrapper-content">
    <ul class="pills">
        <li>
            <a href="{url name=admin_files_widget}" >{t}WIDGET HOME{/t}</a>
        </li>
    </ul>

    {render_messages}

    <div id="{$category}">
        <table class="table table-hover table-condensed">
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
                        {math equation="x / y" x=$size[c]|default:0 y=1024*100 format="%.2f"} MB
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
                        {math equation="x / y" x=$sub_size[c][$subcategorys[c][su]->pk_content_category]|default:0 y=1024*100 format="%.2f"} MB</a>
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
                        {math equation="x / y" x=$total_size|default:0 y=1024*100 format="%.2f"} MB
                    </td>
                </tr>
            </tfoot>
         </table>

        <input type="hidden" name="category" id="category" value="{$category}" />
        <input type="hidden" id="status" name="status" value="" />
        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</div>
</form>
{/block}
