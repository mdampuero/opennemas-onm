<br />
<div id="nifty" style="margin:0 auto;  width:800px; background:#dedede; padding:5px; -moz-border-radius:10px 10px 10px 10px; border:1px solid #CCCCCC;">
    
        <table border='0' width="96%">
            <tr>
                <td style="width:200px;" align='right'> <b>Nombre de la imagen: </b></td>
                <td align='left'>
                        <input type="text" id="stringSearch" name="stringSearch" size="60" value="{$smarty.request.stringSearch}" />
                    <br />
                </td>
            </tr>
            <tr>
                <td align='right'> <b>Sección:</b></td>
                <td align='left'>
                    <select name="categ" id="categ" />
                        <option value="todas" {if $photo1->color eq "todas"} selected {/if}>Todas</option>
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
                <td align='right'> <b>Dimensiones:</b> </td>
                <td align='left'>
                    Ancho Máximo:  <input type="text" id="anchoMax" name="anchoMax" size="10" />
                    Alto Máximo: <input type="text" id="altoMax" name="altoMax" size="10" />
                </td>
            </tr>
            <tr>
                <td align='right'>   </td>
                <td align='left'>
                    Ancho Mínimo:  <input type="text" id="anchoMin" name="anchoMin" size="10" />
                    Alto Mínimo: <input type="text" id="altoMin" name="altoMin" size="10" />
                </td> 
            </tr>            
            <tr>
                <td align='right'> <b>Peso máximo:</b> </td>
                <td align='left'>
                    <input type="text" id="pesoMax" name="pesoMax" size="18" />  Kb
                </td>
            </tr>
            <tr>
                <td align='right'> <b>Peso mínimo:</b> </td>
                <td align='left'>
                    <input type="text" id="pesoMin" name="pesoMin" size="18" />  Kb
                </td>
            </tr>
            <tr>
                <td align='right'> <b>Tipo:</b></td>
                <td align='left'>
                    <select name="tipo" id="tipo" />
                        <option value="" selected >-----</option>
                        <option value="jpg" >jpg</option>
                        <option value="gif" >gif</option>
                        <option value="png" >png</option>
                        <option value="svg" >svg</option>
                        <option value="otros" >Otros</option>
                    </select>
                 </td>
            </tr>
            <tr>
                <td align='right'> <b>Color:</b></td>
                <td align='left'>
                    <select name="color" id="color" />
                         <option value="" selected  >-----</option>
                        <option value="BN" >B/N</option>
                        <option value="color" >Color</option>
                    </select>
                 </td>
            </tr>
            <tr> 
                <td align='right'> <b>Autor:</b></td>
                <td align='left'>
                    <input type="text" id="author" name="author"
                    value='{$photo1->author_name|clearslash|escape:'html'}' size="15"  title="Autor" />
                </td>
            </tr>
            <tr>
                <td align='right' style="vertical-align:top;"><b>Periodo:</b></td>
                <td align='left'>
                    Desde:<input type="text" size="18" id="starttime" name="starttime"
                    value=""  title="Fecha" />

                    Hasta:<input type="text" size="18" id="endtime" name="endtime"
                    value=""  title="Fecha" />
                 </td>
            </tr>
            <tr>
                <td colspan='2' align='center'>
                     <input type="hidden" name="action"  id="action" value="searchResult" />
                     <input type="hidden" name="acti"  id="acti" value="searchResult" />
                    <button onclick="javascript:enviar(this, '_self', 'searchResult', 0);">Buscar</button>
                </td>
            </tr>
       </table>

   

</div>


<script type="text/javascript" language="javascript">
{literal}

if($('starttime')) {
    new Control.DatePicker($('starttime'), {
        icon: './themes/default/images/template_manager/update16x16.png',
        locale: 'es_ES',
        timePicker: true,
        timePickerAdjacent: true,
        dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
    });

}

if($('endtime')) {
    new Control.DatePicker($('endtime'), {
        icon: './themes/default/images/template_manager/update16x16.png',
        locale: 'es_ES',
        timePicker: true,
        timePickerAdjacent: true,
        dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
    });

}
{/literal}
</script>