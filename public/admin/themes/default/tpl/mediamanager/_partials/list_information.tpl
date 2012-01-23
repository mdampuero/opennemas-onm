<table class="listing-table">
    <thead>
        <tr>
            <th>{t}Title{/t}</th>
            <th class="center">{t}JPG images{/t}</th>
            <th class="center">{t}GIF images{/t}</th>
            <th class="center">{t}PNG images{/t}</th>
            <th class="center">{t}Other formats{/t}</th>
            <th class="center">{t}Color images{/t}</th>
            <th class="center">{t}B/W images{/t}</th>
            <th class="center">{t}Total Size (MB){/t}</th>
            <th class="center">{t}# photos{/t}</th>
        </tr>
    </thead>
    <tbody>
        {section name=c loop=$categorys}
        <tr>
            <td><a href="{$smarty.server.PHP_SELF}?category={$categorys[c]->pk_content_category}">{$categorys[c]->title|clearslash|escape:"html"}</a></td>
            <td class="center">{$num_photos[c]->jpg|default:0}</td>
            <td class="center">{$num_photos[c]->gif|default:0}</td>
            <td class="center">{$num_photos[c]->png|default:0}</td>
            <td class="center">{$num_photos[c]->other|default:0}</td>
            <td class="center">{$num_photos[c]->color|default:0}</td>
            <td class="center">{$num_photos[c]->BN|default:0}</td>
            <td class="center">{math equation="x / y" x=$num_photos[c]->size|default:0 y=1024 format="%.2f"} MB</td>
            <td class="center">{$num_photos[c]->total|default:0}</td>
        </tr>
        {section name=su loop=$subcategorys[c]}
        <tr>
            <td style="padding-left:25px;">&rArr; <a href="{$smarty.server.PHP_SELF}?category={$subcategorys[c][su]->pk_content_category}">{$subcategorys[c][su]->title}</a></td>
            <td class="center">{$num_sub_photos[c][su]->jpg|default:0}</td>
            <td class="center">{$num_sub_photos[c][su]->gif|default:0}</td>
            <td class="center">{$num_sub_photos[c][su]->png|default:0}</td>
            <td class="center">{$num_sub_photos[c][su]->other|default:0}</td>
            <td class="center">{$num_sub_photos[c][su]->color|default:0}</td>
            <td class="center">{$num_sub_photos[c][su]->BN|default:0}</td>
            <td class="center">{assign value=$num_sub_photos[c][su] var="subcat"}{math equation="x / y" x=$subcat->size|default:0 y=1024 format="%.2f"} MB</td>
            <td class="center">{$num_sub_photos[c][su]->total|default:0}</td>
        </tr>
        {/section}
        {/section}
        <tr>
            <td class="family_type" scope="col" colspan="9">{t}Specials{/t}</td>
        </tr>

        {section name=c loop=$num_especials}
        <tr>
            <td><a href="{$smarty.server.PHP_SELF}?category={$num_especials[c]->id}">{$num_especials[c]->title|clearslash|escape:"html"}</a></td>
            <td class="center">{$num_especials[c]->jpg|default:0}</td>
            <td class="center">{$num_especials[c]->gif|default:0}</td>
            <td class="center">{$num_especials[c]->png|default:0}</td>
            <td class="center">{$num_especials[c]->other|default:0}</td>
            <td class="center">{$num_especials[c]->color|default:0}</td>
            <td class="center">{$num_especials[c]->BN|default:0}</td>
            <td class="center">{math equation="x / y" x=$num_especials[c]->size|default:0 y=1024 format="%.2f"} MB</td>
            <td class="center">{$num_especials[c]->total|default:0}</td>
        </tr>
        {/section}
    </tbody>

    <tfoot>
        <tr>
            <td class="left"><strong>{t}TOTAL{/t}</strong></td>
            <td class="center">{$total_jpg|default:0}</td>
            <td class="center">{$total_gif|default:0}</td>
            <td class="center">{$total_png|default:0}</td>
            <td class="center">{$total_other|default:0}</td>
            <td class="center">{$total_color|default:0}</td>
            <td class="center">{$total_bn|default:0}</td>
            <td class="center">{math equation="x / y" x=$total_size|default:0 y=1024 format="%.2f"} MB</td>
            <td class="center">{$total_totales|default:0}</td>
        </tr>
    </tfoot>
</table>
