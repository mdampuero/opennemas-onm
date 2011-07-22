

<table id='tabla{$aux}' name='tabla{$aux}' value="{$item->id}" width="100%" class="tabla" style="text-align:center;padding:0px;padding-bottom:4px;">
     <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >     
        <td style="text-align: left;width:10px;" >
           <input type="checkbox" class="minput" pos={$aux} id="selected_fld_art_{$aux}" name="selected_fld[]" value="{$item->id}"  style="cursor:pointer;" onClick="javascript:document.getElementById('selected_fld_art_{$aux}').click();">
        </td>
         <td style="text-align: left;">
            {is_clone item=$item}{$item->title|clearslash}
        </td>
        <td  class='no_width' style="text-align:center;width:50px;"  align="center">
            {$item->views}
        </td>
        <td style="width:50px;" class="no_view" align="center">
             {$item->rating}
        </td>
        <td style="width:50px;"  class="no_view" align="center">
             {$item->comment}
        </td>
        <td style="width:70px;" align="center">
            {$item->created}
        </td>

         <td  class='no_view' style="width:110px;" align="center">
                       {$item->publisher}
            </td>
            <td  class='no_view' style="width:110px;" align="center">
                       {$item->editor}
            </td>
        <td  align="center" style="text-align: center;width:50px;">
                <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$item->id}');" title="Editar">
                        <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" /></a>
        </td>
        <td  align="center" style="width:50px;">
                <a  onClick="javascript:confirmar_hemeroteca(this,'{$category}','{$item->id}') "  title="Archivar">
                        <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" /></a>
        </td>

         <td style="width:50px;" align="center">
            <div class="inhome" style="display:inline;">
                {if $item->in_home == 1}
                   <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/>
                {elseif $item->in_home == 2}                  
                    <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" alt="Sugerida en home" title="Sugerida en home"/>
                {else}                  
                      <img class="inhome" src="{$params.IMAGE_DIR}home_no.png" border="0" alt="No publicada en home" title="No publicada en home"/>
                {/if}
            </div>
        </td>
        <td  align="center"  style="width:50px;">
                <a href="#" onClick="javascript:delete_article('{$item->id}','{$category}',0);" title="Eliminar" alt="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
        </td>
    </tr>
</table>