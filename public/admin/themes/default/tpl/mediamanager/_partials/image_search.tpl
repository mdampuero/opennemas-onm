<br />
<form action="{$smarty.server.PHP_SELF}" method="get">
<div id="nifty" style="margin:0 auto; border:1px solid #dedede; padding:5px; -moz-border-radius:5px; border:1px solid #CCCCCC;">

        <table border='0' width="100%">
            <tr>
                <td style="width:200px;" align='right'> <strong>{t}Image name:{/t}</strong></td>
                <td align='left'>
                        <input type="text" id="stringSearch" name="stringSearch" size="60" value="{$smarty.request.stringSearch}" />
                    <br />
                </td>
            </tr>
            <tr>
                <td align='right'> <strong>{t}Section{/t}</strong></td>
                <td align='left'>
                    <select name="categ" id="categ" />
                        <option value="todas" {if $photo1->color eq "todas"} selected {/if}>{t}All{/t}</option>
                        {section name=as loop=$allcategorys}
                            <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}">{$allcategorys[as]->title}</option>
                            {section name=su loop=$subcat[as]}
                                <option value="{$subcat[as][su]->pk_content_category}" {if $category  eq $subcat[as][su]->pk_content_category} selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                           {/section}
                        {/section}
                    </select>
                 </td>
            </tr>
             <tr>
                <td align='right'> <strong>{t}Size:{/t}</strong> </td>
                <td align='left'>

                    <label for="anchoMax">{t}Max width:{/t} </label>
                    <input type="text" id="anchoMax" name="anchoMax" size="10" />

                    <label for="altoMax">{t}Max height:{/t}</label>
                    <input type="text" id="altoMax" name="altoMax" size="10" />

                </td>
            </tr>
            <tr>
                <td align='right'>   </td>
                <td align='left'>

                    <label for="anchoMin">{t}Min weight:{/t}</label>
                    <input type="text" id="anchoMin" name="anchoMin" size="10" />

                    <label for="altoMin">{t}Min height:{/t}</label>
                    <input type="text" id="altoMin" name="altoMin" size="10" />

                </td>
            </tr>
            <tr>
                <td align='right'> <strong>{t}File size:{/t}</strong> </td>
                <td align='left'>

                    <label for="pesoMax">{t}Max file size:{/t}</label>
                    <input type="text" id="pesoMax" name="pesoMax" size="18" />  Kb
                </td>
            </tr>
            <tr>
                <td align='right'></td>
                <td align='left'>
                    <label for="pesoMin">{t}Min file size:{/t}</label>
                    <input type="text" id="pesoMin" name="pesoMin" size="18" />  Kb
                </td>
            </tr>
            <tr>
                <td align='right'> <strong>{t}Type:{/t}</strong></td>
                <td align='left'>
                    <select name="tipo" id="tipo" />
                        <option value="" selected >{t} - All types - {/t}</option>
                        <option value="jpg" >jpg</option>
                        <option value="gif" >gif</option>
                        <option value="png" >png</option>
                        <option value="svg" >svg</option>
                        <option value="otros" >{t}Others{/t}</option>
                    </select>
                 </td>
            </tr>
            <tr>
                <td align='right'> <strong>{t}Color:{/t}</strong></td>
                <td align='left'>
                    <select name="color" id="color" />
                         <option value="" selected  >{t} - All types - {/t}</option>
                        <option value="BN" >{t}Black and white{/t}</option>
                        <option value="color" >{t}Color{/t}</option>
                    </select>
                 </td>
            </tr>
            <tr>
                <td align='right'> <strong>{t}Author:{/t}</strong></td>
                <td align='left'>
                    <input type="text" id="author" name="author"
                    value='{$photo1->author_name|clearslash|escape:'html'}' size="15"  title="Autor" />
                </td>
            </tr>
            <tr>
                <td align='right' style="vertical-align:top;"><strong>Periodo:</strong></td>
                <td align='left'>
                    {t}From:{/t}<input type="text" size="18" id="starttime" name="starttime"
                    value=""  title="Fecha" />

                    {t}To:{/t}<input type="text" size="18" id="endtime" name="endtime"
                    value=""  title="Fecha" />
                 </td>
            </tr>
            <tr>
                <td colspan='2' align='center'>
                     <input type="hidden" name="action"  id="action" value="searchResult" />
                     <input type="hidden" name="acti"  id="acti" value="searchResult" />
                    <button onclick="javascript:enviar(this, '_self', 'searchResult', 0);">{t}Search{/t}</button>
                </td>
            </tr>
       </table>


</div>

    <input type="hidden" id="action" name="action" value="searchResult" />
</form>
