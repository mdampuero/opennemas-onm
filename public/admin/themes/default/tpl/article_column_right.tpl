<table class="adminlist">
    <tr>
         <th align="center"> </th>
        <th align="center">T&iacute;tulo</th>
        <th align="center" style="width:30px;">Visto</th>
        <th align="center" style="width:60px;">Fecha</th>
        {if $category neq 'home'}
            <th align="center" style="width:30px;">Home</th>
        {else}
            <th align="center" style="width:50px;">Secci&oacute;n</th>
        {/if}
        <th align="center" style="width:30px;">Edit</th>
        <th align="center" style="width:30px;">Arch</th>
        <th align="center" style="width:30px;">Des</th>
        <th align="center" style="width:30px;">El</th>
   </tr>
   <tr>
       <td colspan=13>
           <div id="odd" class="seccion" style="position:relative;width:100%;min-height:480px; border:1px solid gray;">
               <table width="100%" height="50" class="aa" style="background-color:#E1E3E5;text-align:center;padding:0px;padding-bottom:4px;">
                   <tr><td> NOTICIAS EXPRESS</td></tr>
               </table>
                <div id="hole1" class="seccion" style="min-height:40px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                    {renderarticle items=$oddpublished tpl="article_render_fila.tpl"  placeholder="placeholder_1_0" }
                    {renderarticle items=$oddpublished tpl="article_render_fila.tpl"  placeholder="" odd_rating=$odd_rating }
                </div>
                <table width="100%" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;padding-bottom:4px;">
                    <tr height="50" style="background-color:#E4DDC9;"><td><div><img src="{$params.IMAGE_DIR}iconos/agt_reload.png">&nbsp;(Hacer click para cambiar)</div>
                        <div id="bloquegente">{$bloqueGente}</div></td></tr>
                    <tr height="30" style="background-color:#EEF;"><td > PUBLICIDAD 1</td></tr>
                </table>
                <div id="hole2" class="seccion" style="min-height:120px;width:100%;background-color:#F5F5F5;text-align:center;padding:0px;">
                    {renderarticle items=$oddpublished tpl="article_render_fila.tpl"  placeholder="placeholder_1_1"}
                </div>
                <table width="100%" height="30" class="aa" style="background-color:#EEF;text-align:center;padding:0px;padding-bottom:4px;">
                   <tr ><td > PUBLICIDAD 2</td></tr>
                </table>
                <div id="hole3" class="seccion" style="min-height:40px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                    {renderarticle items=$oddpublished tpl="article_render_fila.tpl"  placeholder="placeholder_1_2" }
                </div>
                <div id="hole4" class="seccion" style="min-height:50px;width:100%;background-color:#E4DDC9;text-align:center;padding:0px;">                    
                    ESPECIAL
                    {renderarticle items=$oddpublished tpl="article_render_fila.tpl"  placeholder="placeholder_1_3" }
                </div>
                <table width="100%" height="30" class="aa" style="background-color:#EEF;text-align:center;padding:0px;">
                   <tr ><td > PUBLICIDAD 3</td></tr>
                </table>
                <table width="100%" height="40" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;">
                           <tr><td > HUMOR</td></tr>
                </table>
           </div>{* div odd *}
       </td>
   </tr>
</table>


{literal}
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
{/literal}