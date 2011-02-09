<table id='tabla{$aux}' name='tabla{$aux}' value="{$item->pk_widget}" data="{$item->content_type}" width="100%" class="tabla">
    <tr class="row1{schedule_class item=$item}" style="cursor:pointer;">

        <td style="text-align: left; width:10px;">
            <input type="checkbox" class="minput" pos={$aux} id="selected_{$placeholder}_{$aux}" name="selected_fld[]" value="{$item->id}"  style="cursor:pointer;" />
        </td>
        <td align="left" style="width:90%">
            <strong>WIDGET:</strong> {$item->title}
        </td>


        <td align="left" style="width:3%">
            {if ($widgets[wgt]->renderlet != 'intelligentwidget')}
            <a href="controllers/widget/widget.php?action=edit&id={$widgets[d]->pk_widget}" title="{t}Edit{/t}"><img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
            {/if}
        </td>

    </tr>
</table>
