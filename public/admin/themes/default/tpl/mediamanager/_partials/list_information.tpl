<style type="text/css">
table.adminlist tr td {
    padding:8px 5px;
}
</style>
<br style="clear:both;" />
<table class="adminheading" >
    <tr>
        <td>{t}Image statistics{/t}</td>
    </tr>
</table>
<table class="adminlist">
    <thead>
        <tr>
            <th style="width:20%; text-align:left;">{t}Title{/t}</th>
            <th style="width:10%; text-align:center;">{t}JPG images{/t}</th>
            <th style="width:10%; text-align:center;">{t}GIF images{/t}</th>
            <th style="width:10%; text-align:center;">{t}PNG images{/t}</th>
            <th style="width:10%; text-align:center;">{t}Other formats{/t}</th>
            <th style="width:10%; text-align:center;">{t}Color images{/t}</th>
            <th style="width:10%; text-align:center;">{t}B/W images{/t}</th>
            <th style="width:10%; text-align:center;">{t}Total Size (MB){/t}</th>
            <th style="text-align: center; width:10%">{t}# photos{/t}</th>
        </tr>
    </thead>
    <tbody>
        {section name=c loop=$categorys}
        <tr class="{cycle values="row0,row1"}">
            <td><strong>{$categorys[c]->title|clearslash|escape:"html"}</strong></td>
            <td>{$num_photos[c]->jpg|default:0}</td>
            <td>{$num_photos[c]->gif|default:0}</td>
            <td>{$num_photos[c]->png|default:0}</td>
            <td>{$num_photos[c]->other|default:0}</td>
            <td>{$num_photos[c]->color|default:0}</td>
            <td>{$num_photos[c]->BN|default:0}</td>
            <td>{math equation="x / y" x=$num_photos[c]->size|default:0 y=1024 format="%.2f"} MB</td>
            <td>{$num_photos[c]->total|default:0}</td>
        </tr>
        {section name=su loop=$subcategorys[c]}
        <tr class="{cycle values="row0,row1"}">
            <td><strong>&nbsp;&nbsp;&nbsp;|_&nbsp;{$subcategorys[c][su]->title}</strong></td>
            <td>{$num_sub_photos[c][su]->jpg|default:0}</td>
            <td>{$num_sub_photos[c][su]->gif|default:0}</td>
            <td>{$num_sub_photos[c][su]->png|default:0}</td>
            <td>{$num_sub_photos[c][su]->other|default:0}</td>
            <td>{$num_sub_photos[c][su]->color|default:0}</td>
            <td>{$num_sub_photos[c][su]->BN|default:0}</td>
            <td>{assign value=$num_sub_photos[c][su] var="subcat"}{math equation="x / y" x=$subcat->size|default:0 y=1024 format="%.2f"} MB</td>
            <td>{$num_sub_photos[c][su]->total|default:0}</td>
        </tr>
        {/section}
        {/section}

        {section name=c loop=$num_especials}
        <tr class="{cycle values="row0,row1"}">
            <td><strong> {$num_especials[c]->title|upper|clearslash|escape:"html"}</strong></td>
            <td>{$num_especials[c]->jpg|default:0}</td>
            <td>{$num_especials[c]->gif|default:0}</td>
            <td>{$num_especials[c]->png|default:0}</td>
            <td>{$num_especials[c]->other|default:0}</td>
            <td>{$num_especials[c]->color|default:0}</td>
            <td>{$num_especials[c]->BN|default:0}</td>
            <td>{math equation="x / y" x=$num_especials[c]->size|default:0 y=1024 format="%.2f"} MB</td>
            <td>{$num_especials[c]->total|default:0}</td>
        </tr>
        {/section}
    </tbody>

    <tfoot>
        <tr>
            <td><strong> TOTALES </strong></td>
            <td>{$total_jpg|default:0}</td>
            <td>{$total_gif|default:0}</td>
            <td>{$total_png|default:0}</td>
            <td>{$total_other|default:0}</td>
            <td>{$total_color|default:0}</td>
            <td>{$total_bn|default:0}</td>
            <td>{math equation="x / y" x=$total_size|default:0 y=1024 format="%.2f"} MB</td>
            <td>{$total_totales|default:0}</td>
        </tr>
    </tfoot>
</table>
