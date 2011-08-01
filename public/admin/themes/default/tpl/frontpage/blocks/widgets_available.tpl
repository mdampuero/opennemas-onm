
<table class="adminlist">
    <thead>
        <tr>
            <th align="center" style="width:30px">Selec</th>
            <th align="left">{t}Title{/t}</th>
            <th align="center" style="width:80px">{t}Actions{/t}</th>
        </tr>
    </thead>
    <tr>
        <td colspan=4>
            {if $category == 'home'}
                <div id="art" class="seccion">
            {else}
                <div id="widgets_available" class="seccion">
            {/if}
                {assign var=aux value='100'}
                {section name=d loop=$widgets}
                    <table id="tabla{$aux}" name="tabla{$aux}" width="100%" value="{$widgets[d]->pk_widget}" data="{$widgets[d]->content_type}" class="tabla">
                        <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >

                            <td style="width:40px;">
                                <input type="checkbox" class="minput" pos={$aux} id="selected_{$placeholder}_{$aux}" name="selected_fld[]" value="{$widgets[d]->id}"  style="cursor:pointer;" />
                            </td>
                            <td align="left" >

                                <strong>WIDGET:</strong> {$widgets[d]->title}
                            </td>

                            <td align="center" style="width:10px">
                                {if ($widgets[d]->renderlet != 'intelligentwidget')}
                                <a href="controllers/widget/widget.php?action=edit&id={$widgets[d]->pk_widget}&category={$smarty.request.category}" title="{t}Edit{/t}"><img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                                {else}
                                    &nbsp;
                                {/if}
                            </td>
                            <td  align="center"  class="un_width"  style="width:10px;">
                                <a href="controllers/widget/widget.php?action=delete&id={$widgets[d]->pk_widget}" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
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
