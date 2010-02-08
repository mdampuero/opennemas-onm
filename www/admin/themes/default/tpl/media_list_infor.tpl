 <br style="clear:both;" />
            <table class="adminlist" id="tabla"  style="width:99%;">
                <tr>
                    <th class="title" align="left">Título</th>
                    <th style="width:100px" align="left">Total jpg</th>
                    <th style="width:100px" align="left">Total gif</th>
                    <th style="width:100px" align="left">Total png</th>
                    <th style="width:100px" align="left">Total Otros Formatos</th>
                    <th style="width:100px" align="left">Total Color</th>
                    <th style="width:100px" align="left">Total B/N</th>
                    <th style="width:100px" align="left">Total Size (MB)</th>
                    <th style="width:100px" align="left">Nº Fotos</th>
                </tr>

                <tr>
                    <td colspan="9">
                        {section name=c loop=$categorys}
                        <table width="100%" cellpadding="0" cellspacing="0"  id="{$categorys[c]->pk_content_category}">
                            <tr class="{cycle values="row0,row1"}">
                                <td style="padding: 0px 10px; height: 24px;font-size: 11px;">
                                    <b> {$categorys[c]->title|clearslash|escape:"html"}</b>
                                </td>                                
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_photos[c]->jpg|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_photos[c]->gif|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_photos[c]->png|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_photos[c]->other|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_photos[c]->color|default:0}
                                </td><td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_photos[c]->BN|default:0}
                                </td>
                                <td style="padding:0px; height:24px;font-size:11px; width:100px;" align="left">
                                     {math equation="x / y" x=$num_photos[c]->size|default:0 y=1024 format="%.2f"} MB
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_photos[c]->total|default:0}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="9">
                                    {section name=su loop=$subcategorys[c]}
                                        <table width="100%" cellpadding=0 cellspacing=0 id="{$subcategorys[c][su]->pk_content_category}" class="tabla">
                                            <tr class="{cycle values="row0,row1"}">
                                                <td style="padding: 0px 30px; height: 24px; font-size: 11px;">
                                                    <b>{$subcategorys[c][su]->title}</b>
                                                </td>
                                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                                    {$num_sub_photos[c][su]->jpg|default:0}
                                                </td>
                                                 <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                                    {$num_sub_photos[c][su]->gif|default:0}
                                                </td>
                                                 <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                                    {$num_sub_photos[c][su]->png|default:0}
                                                </td>
                                                 <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                                    {$num_sub_photos[c][su]->other|default:0}
                                                </td>
                                                 <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                                    {$num_sub_photos[c][su]->color|default:0}
                                                </td>
                                                 <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                                    {$num_sub_photos[c][su]->BN|default:0}
                                                </td>
                                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                                       {assign value=$num_sub_photos[c][su] var="subcat"}
                                                       {math equation="x / y" x=$subcat->size|default:0 y=1024 format="%.2f"} MB
                                                </td>
                                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
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
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_especials[c]->jpg|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_especials[c]->gif|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_especials[c]->png|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_especials[c]->other|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_especials[c]->color|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_especials[c]->BN|default:0}
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {math equation="x / y" x=$num_especials[c]->size|default:0 y=1024 format="%.2f"} MB
                                </td>
                                <td style="padding:0px; height:24px; font-size:11px; width:100px;" align="left">
                                    {$num_especials[c]->total|default:0}
                                </td>
                            </tr>
                        </table>
                        {/section}
                    </td>
                </tr>
            </table>