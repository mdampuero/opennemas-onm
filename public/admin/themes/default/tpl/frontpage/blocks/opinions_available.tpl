<table class="adminlist">
    <thead>
        <tr>
            <th align="center" style="width:20px"></th>
            <th align="left">{t}Title{/t}</th>
            <th align="center" style="width:40px">{t}Edit{/t}</th>
            <th align="center" style="width:40px">{t}Delete{/t}</th>
        </tr>
    </thead>
    <tr>
        <td colspan=4>
            <div id="available_opinions" class="seccion">
                {assign var=aux value='100'}
                {section name=d loop=$opinions}
                    <table id="tabla{$aux}" name="tabla{$aux}" width="100%" value="{$opinions[d]->id}" data="Opinion" class="tabla">
                        <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >

                            <td align="center" style="width:30px">
                                <input type="checkbox" class="minput" id="selected_fld_art_{$smarty.section.d.iteration}"
                                       name="no_selected_fld[]" value="{$opinions[d]->id}" style="cursor:pointer;" >&nbsp;
                            </td>

                            <td align="left">
                                <strong>OPINION: {$opinions[d]->author->name} - </strong> {$opinions[d]->title}
                            </td>

                            <td align="left" style="width:40px">
                                <a href="controllers/opinion/opinion.php?action=read&id={$opinions[d]->id}&category={$smarty.request.category}" title="{t}Edit{/t}"><img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                            </td>
                            <td  align="center" style="width:40px">
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
