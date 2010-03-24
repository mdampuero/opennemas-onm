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
                <table width="100%" height="30" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;padding-bottom:1px;">
                    <tr height="20"><td > Destacada </td></tr>
                </table>
                <div id="{$place}_0" class="seccion" style="min-height:40px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                    {renderarticle items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_0' }
                </div>                
                    <table width="100%" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;padding-bottom:1px;">
                        <tr height="3" style="background-color:#EEF;"><td > </td></tr>
                    </table>
               
                <div id="{$place}_1" class="seccion" style="min-height:120px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                    {renderarticle items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_1' }                    
                </div>
                 {if $place eq 'placeholder_1'}
                    <table width="100%" height="110" class="aa" style="background-color:#EEF;text-align:center;padding:0px;">
                        <tr ><td >  Widget Ocio </td></tr>
                    </table>
                 {else}
                    <table width="100%" height="50" class="aa" style="background-color:#EEF;text-align:center;padding:0px;padding-bottom:4px;">
                       <tr ><td >  Widget Publicidad  </td></tr>                   
                        {if $place eq 'placeholder_2'}
                            <tr ><td >  Widget Titulares </td></tr>                       
                        {/if}
                     </table>
                    <div id="{$place}_2" class="seccion" style="min-height:60px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                        {renderarticle items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_2' }
                    </div>
                   
                 {/if}
                 <table width="100%" height="30" class="aa" style="background-color:#EEF;text-align:center;padding:0px;">
                   <tr ><td >  Widget Social </td></tr>
                </table>
                {if $place eq 'placeholder_2'}
                    <table width="100%" height="70" class="aa" style="background-color:#EEF;text-align:center;padding:0px;">
                        <tr ><td >  Widget Titulares </td></tr>
                    </table>
                {else}
                    <div id="{$place}_3" class="seccion" style="min-height:70px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                        {renderarticle items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_3' }
                    </div>
                {/if}
          
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