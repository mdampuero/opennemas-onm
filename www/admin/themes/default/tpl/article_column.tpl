<table class="adminlist">
    <tr>
         <th align="center"> </th>
        <th align="center">T&iacute;tulo</th>
        {if $category neq 'home'}
            <th align="center" style="width:30px;">Home</th>
        {else}
            <th align="center" style="width:50px;">Secci&oacute;n</th>
        {/if}
        <th align="center" style="width:30px;">Edit</th>
        <th align="center" style="width:30px;">Arch</th>
        <th align="center" style="width:30px;">Des</th>
        {if $category neq 'home'}
            <th align="center" style="width:30px;">El</th>
        {/if}
   </tr>
   <tr>
       <td colspan="10">
                <div id="{$place}_0" class="seccion" style="min-height:40px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                    {renderarticle items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_0' }
                </div>
                <table width="100%" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;padding-bottom:4px;">                    
                    <tr height="30" style="background-color:#EEF;"><td > Other content 1</td></tr>
                </table>
                <div id="{$place}_1" class="seccion" style="min-height:120px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                    {renderarticle items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_1' }                    
                </div>
                <table width="100%" height="30" class="aa" style="background-color:#EEF;text-align:center;padding:0px;padding-bottom:4px;">
                   <tr ><td >  Other content 2</td></tr>
                </table>
                <div id="{$place}_2" class="seccion" style="min-height:40px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                    {renderarticle items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_2' }
                </div>
                 <table width="100%" height="30" class="aa" style="background-color:#EEF;text-align:center;padding:0px;">
                   <tr ><td >  Other content 3</td></tr>
                </table>
                 
                <div id="{$place}_3" class="seccion" style="min-height:50px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                    {renderarticle items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_3' }
                </div>
               
          
       </td>
   </tr>
</table>


{*literal}
<script type="text/javascript">
document.observe('dom:loaded', function() {
$('bloquegente').setStyle({cursor: 'pointer'});
$('bloquegente').observe('click', function() {
    new Ajax.Request('?action=toggleBlock&category={/literal}{$category}{literal}', {
        onSuccess: function(transport) {
            $('bloquegente').update(transport.responseText);
        }
    });
});
});
</script>
{/literal*}