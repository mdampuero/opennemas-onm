<br style="clear:both;" />
<table class="adminheading" >
    <tr>
        <td>{t}Image statistics{/t}</td>
    </tr>
</table>
<table class="adminlist" id="tabla">
    <thead>
        <tr>
            <th class="title" style="width:20%">{t}Title{/t}</th>
            <th style="width:10%">{t}JPG images{/t}</th>
            <th style="width:10%">{t}GIF images{/t}</th>
            <th style="width:10%">{t}PNG images{/t}</th>
            <th style="width:10%">{t}Other formats{/t}</th>
            <th style="width:10%">{t}Color images{/t}</th>
            <th style="width:10%">{t}B/W images{/t}</th>
            <th style="width:10%">{t}Total Size (MB){/t}</th>
            <th style="text-align: center; width:10%">{t}# photos{/t}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="9">
                {section name=c loop=$categorys}
                <table width="100%" cellpadding="0" cellspacing="0"  id="{$categorys[c]->pk_content_category}">
                    <tr class="{cycle values="row0,row1"}">
                        <td style="width:20%; padding: 0px 10px; height: 24px;font-size: 11px;">
                            <b> {$categorys[c]->title|clearslash|escape:"html"}</b>
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_photos[c]->jpg|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_photos[c]->gif|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_photos[c]->png|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_photos[c]->other|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_photos[c]->color|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_photos[c]->BN|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px;font-size:11px; width:10%;">
                             {math equation="x / y" x=$num_photos[c]->size|default:0 y=1024 format="%.2f"} MB
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_photos[c]->total|default:0}
                        </td>
                    </tr>
                    <tr style="text-align:center">
                        <td colspan="9">
                            {section name=su loop=$subcategorys[c]}
                                <table width="100%" cellpadding=0 cellspacing=0 id="{$subcategorys[c][su]->pk_content_category}" class="tabla">
                                    <tr class="{cycle values="row0,row1"}">
                                        <td style="padding: 0px 30px; height: 24px; font-size: 11px;">
                                            <b>{$subcategorys[c][su]->title}</b>
                                        </td>
                                         <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                                            {$num_sub_photos[c][su]->jpg|default:0}
                                        </td>
                                         <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                                            {$num_sub_photos[c][su]->gif|default:0}
                                        </td>
                                         <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                                            {$num_sub_photos[c][su]->png|default:0}
                                        </td>
                                         <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                                            {$num_sub_photos[c][su]->other|default:0}
                                        </td>
                                         <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                                            {$num_sub_photos[c][su]->color|default:0}
                                        </td>
                                         <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                                            {$num_sub_photos[c][su]->BN|default:0}
                                        </td>
                                         <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                                               {assign value=$num_sub_photos[c][su] var="subcat"}
                                               {math equation="x / y" x=$subcat->size|default:0 y=1024 format="%.2f"} MB
                                        </td>
                                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                                            {$num_sub_photos[c][su]->total|default:0}
                                        </td>
                                    </tr>
                                </table>
                            {/section}
                        </td>
                    </tr>
                </table>
                {/section}
            </td>
        </tr>

        <tr>
            <td colspan="9">
                {section name=c loop=$num_especials}
                <table width="100%" cellpadding=0 cellspacing=0 >
                    <tr class="{cycle values="row0,row1"}">
                        <td style="padding: 0px 10px; height: 24px;font-size: 11px;">
                            <b> {$num_especials[c]->title|upper|clearslash|escape:"html"}</b>
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_especials[c]->jpg|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_especials[c]->gif|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_especials[c]->png|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_especials[c]->other|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_especials[c]->color|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_especials[c]->BN|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {math equation="x / y" x=$num_especials[c]->size|default:0 y=1024 format="%.2f"} MB
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$num_especials[c]->total|default:0}
                        </td>
                    </tr>
                </table>
                {/section}
            </td>
        </tr>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="9" style="padding: 10px 0px;">
                <table width="100%" cellpadding=0 cellspacing=0>
                    <tr class="{cycle values="row0"}">
                        <td style="padding: 0px 10px; height: 24px;font-size: 11px;">
                            <strong> TOTALES </strong>
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$total_jpg|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$total_gif|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$total_png|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$total_other|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$total_color|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$total_bn|default:0}
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {math equation="x / y" x=$total_size|default:0 y=1024 format="%.2f"} MB
                        </td>
                        <td style="text-align: center; padding:0px; height:24px; font-size:11px; width:10%;">
                            {$total_totales|default:0}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tfoot>
</table>
