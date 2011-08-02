<table class="adminlist">
    <thead>
        <tr>
            <th align="center" style="width:15px"></th>
            <th align="left">{t}Title{/t}</th>
            <th align="center" style="width:60px">{t}Actions{/t}</th>
        </tr>
    </thead>
    <tr>
        <td colspan=4>
            <div id="available_opinions" class="seccion">
                {assign var=aux value='100'}
                {section name=d loop=$opinions}
                    <table id="tabla{$aux}" name="tabla{$aux}" width="100%" value="{$opinions[d]->id}" data="Opinion" class="tabla">
                        <tr>
                            <td align="center" style="width:10px">
                                <input type="checkbox" class="minput" id="selected_fld_art_{$smarty.section.d.iteration}"
                                       name="no_selected_fld[]" value="{$opinions[d]->id}" style="cursor:pointer;" >
                            </td>

                            <td align="left">
                                <strong>OPINION: {$opinions[d]->author->name} - </strong> {$opinions[d]->title}
                            </td>
                            <td style="width:80px; text-align:right; padding-right:10px;">
                                <ul class="action-buttons">
                                    <li>
                                        <a href="controllers/opinion/opinion.php?action=read&id={$opinions[d]->id}&category={$smarty.request.category}" title="{t}Edit{/t}"><img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                                    </li>
                                    <li>
                                        <a href="controllers/opinion/opinion.php?action=delete&id={$opinions[d]->id}" title="Eliminar"><img height=16px  src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                                    </li>
                                </ul>
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
