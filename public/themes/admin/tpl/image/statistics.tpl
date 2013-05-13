{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
    <div class="title"><h2>{t}Images{/t} :: {t}Statistics{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a class="admin_add" href="{url name=admin_images_search}">
                    <img src="{$params.IMAGE_DIR}search.png" alt="Buscar Imágenes"><br />{t}Search{/t}
                </a>
            </li>
            {acl isAllowed="IMAGE_SETTINGS"}
            <li class="separator"></li>
                <li>
                    <a href="{url name=admin_images}" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="" /><br />
                        {t}Go back{/t}
                    </a>
                </li>
            {/acl}
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

    <table class="table table-hover table-condensed">
        <thead>
            <tr>
                <th>{t}Title{/t}</th>
                <th class="center">{t}# photos{/t}</th>
                <th class="center">{t}Total Size (MB){/t}</th>
                <th class="center">{t}Average Size (KB){/t}</th>
                <th class="center">{t}JPG images{/t}</th>
                <th class="center">{t}GIF images{/t}</th>
                <th class="center">{t}PNG images{/t}</th>
                <th class="center">{t}Other formats{/t}</th>
                <th class="center">{t}Color images{/t}</th>
                <th class="center">{t}B/W images{/t}</th>
            </tr>
        </thead>
        <tbody>
            {section name=c loop=$categorys}
                {math assign="total_size" equation="x / y" x=$num_photos[c]->size|default:0 y=1024 format="%.2f"}
                {math assign="avg_size" equation="x / y" x=$num_photos[c]->size|default:0 y=$num_photos[c]->total|default:1 format="%.2f"}
                <tr>
                    <td><a href="{$smarty.server.PHP_SELF}?action=category_catalog&amp;category={$categorys[c]->pk_content_category}">{$categorys[c]->title|clearslash|escape:"html"}</a></td>
                    <td class="center"><strong>{$num_photos[c]->total|default:0}</strong></td>
                    <td class="center"><strong>{$total_size} MB</strong></td>
                    <td class="center" style="border-right:1px solid #999"><strong>{$avg_size} KB</strong></td>
                    <td class="center">{$num_photos[c]->jpg|default:0}</td>
                    <td class="center">{$num_photos[c]->gif|default:0}</td>
                    <td class="center">{$num_photos[c]->png|default:0}</td>
                    <td class="center">{$num_photos[c]->other|default:0}</td>
                    <td class="center">{$num_photos[c]->color|default:0}</td>
                    <td class="center">{$num_photos[c]->BN|default:0}</td>
                </tr>
                {section name=su loop=$subcategorys[c]}
                    {assign value=$num_sub_photos[c][su] var="subcat"}
                    {math assign="sub_total_size" equation="x / y" x=$subcat->size|default:0 y=1024 format="%.2f"}
                    {math assign="sub_avg_size" equation="x / y" x=$subcat->size|default:0 y=$num_sub_photos[c][su]->total|default:1 format="%.2f"}
                    <tr>
                        <td style="padding-left:25px;">&rArr; <a href="{$smarty.server.PHP_SELF}?action=category_catalog&amp;category={$subcategorys[c][su]->pk_content_category}">{$subcategorys[c][su]->title}</a></td>
                        <td class="center"><strong>{$num_sub_photos[c][su]->total|default:0}</strong></td>
                        <td class="center"><strong>{$sub_total_size} MB</strong></td>
                        <td class="center" style="border-right:1px solid #999"><strong>{$sub_avg_size} KB</strong></td>
                        <td class="center">{$num_sub_photos[c][su]->jpg|default:0}</td>
                        <td class="center">{$num_sub_photos[c][su]->gif|default:0}</td>
                        <td class="center">{$num_sub_photos[c][su]->png|default:0}</td>
                        <td class="center">{$num_sub_photos[c][su]->other|default:0}</td>
                        <td class="center">{$num_sub_photos[c][su]->color|default:0}</td>
                        <td class="center">{$num_sub_photos[c][su]->BN|default:0}</td>
                    </tr>
                {/section}
            {/section}
            {if count($especials)>0}
                <tr>
                    <td class="family_type" colspan="10">{t}Specials{/t}</td>
                </tr>

                {section name=c loop=$num_especials}
                <tr>
                    <td><a href="{$smarty.server.PHP_SELF}?action=category_catalog&amp;category={$num_especials[c]->id}">{$num_especials[c]->title|clearslash|escape:"html"}</a></td>
                    <td class="center"><strong>{$num_especials[c]->total|default:0}</strong></td>
                    <td class="center"><strong>{math equation="x / y" x=$num_especials[c]->size|default:0 y=1024 format="%.2f"} MB</strong></td>
                    <td class="center" style="border-right:1px solid #999"><strong>{math equation="x / y" x=$num_especials[c]->size|default:0 y=$num_especials[c]->total|default:1 format="%.2f"} KB</strong></td>
                    <td class="center">{$num_especials[c]->jpg|default:0}</td>
                    <td class="center">{$num_especials[c]->gif|default:0}</td>
                    <td class="center">{$num_especials[c]->png|default:0}</td>
                    <td class="center">{$num_especials[c]->other|default:0}</td>
                    <td class="center">{$num_especials[c]->color|default:0}</td>
                    <td class="center">{$num_especials[c]->BN|default:0}</td>
                </tr>
                {/section}
            {/if}
        </tbody>

        <tfoot>
            <tr>
                <td class="left"><strong>{t}TOTAL{/t}</strong></td>
                <td class="center"><strong>{$totals['total']|default:0}</strong></td>
                <td class="center"><strong>{math equation="x / y" x=$totals['size']|default:0 y=1024 format="%.2f"} MB</strong></td>
                <td class="center"><strong>{math equation="x / y" x=$totals['size']|default:0 y=$totals['total']|default:1 format="%.2f"} KB</strong></td>
                <td class="center">{$totals['jpg']|default:0}</td>
                <td class="center">{$totals['gif']|default:0}</td>
                <td class="center">{$totals['png']|default:0}</td>
                <td class="center">{$totals['other']|default:0}</td>
                <td class="center">{$totals['color']|default:0}</td>
                <td class="center">{$totals['bn']|default:0}</td>
            </tr>
        </tfoot>
    </table>
</div>
{/block}
