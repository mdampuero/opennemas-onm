
<table class="adminlist" style="width:100%;">
    <tr>
        <th align="center">Selec</th>
        <th align="left" style="width:90%">T&iacute;tulo</th>
        <th align="center">Editar</th>
        <th align="center">Elim</th>
    </tr>
    <tr>
        <td colspan=4>
            <div id="available_opinions" class="seccion">
                {assign var=aux value='100'}
                {section name=d loop=$opinions}
                    <table id="tabla{$aux}" name="tabla{$aux}" width="100%" value="{$opinions[d]->id}" data="Opinion" class="tabla">
                        <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >

                            <td align="left" style="width:3%">
                                <input type="checkbox" class="minput" id="selected_fld_art_{$smarty.section.d.iteration}"
                                       name="no_selected_fld[]" value="{$opinions[d]->id}" style="cursor:pointer;" >&nbsp;
                            </td>

                            <td align="left" style="width:90%">
                                <strong>OPINION: {$opinions[d]->author->name} - </strong> {$opinions[d]->title}
                            </td>

                            <td align="left" style="width:3%">
                                <a href="controllers/opinion/opinion.php?action=read&id={$opinions[d]->id}" title="{t}Edit{/t}"><img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                            </td>
                            <td  align="center"  class="un_width"  style="width:20px;">
                                <a href="controllers/opinion/opinion.php?action=delete&id={$opinions[d]->id}" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                            </td>
                        </tr>
                    </table>
                    {assign var=aux value=$aux+1}
                {/section}
                <br/>
            </div>
        </td>
   </tr>
</table>
