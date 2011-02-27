<table id='tabla{$aux}' name='tabla{$aux}' value="{$item->pk_opinion}" data="Opinion" width="100%" class="tabla">
    <tr class="row1{schedule_class item=$item}" style="cursor:pointer;">

        <td style="text-align: left; width:10px;">
            <input type="checkbox" class="minput" pos={$aux} id="selected_{$placeholder}_{$aux}" name="selected_fld[]" value="{$item->id}"  style="cursor:pointer;" />
        </td>
        <td align="left" style="width:90%">
            <strong>{t}OPINION{/t} - {$opinions[d]->author->name}: </strong>{$item->title}
        </td>

        <td align="left" style="width:3%">
            <a href="controllers/opinion/opinion.php?action=read&id={$opinions[d]->pk_opinion}" title="{t}Edit{/t}"><img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
        </td>

    </tr>
</table>
