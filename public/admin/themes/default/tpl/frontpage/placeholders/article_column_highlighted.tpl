<table class="adminlist">
    <!--<tr>
         <th align="center"> </th>
        <th align="center">T&iacute;tulo</th>
        {if $category neq 'home'}
            <th align="center" class="rot270">Home</th>
        {else}
            <th align="center" >Secci&oacute;n</th>
        {/if}
        <th align="center" style="width:20px;" class="rot270">Edit</th>
        <th align="center" style="width:20px;" class="rot270">Arch</th>
        <th align="center" style="width:10px;" class="rot270">Desp</th>
        {if $category neq 'home'}
            <th align="center" style="width:20px;"class="rot270">Borr</th>
        {/if}
   </tr>-->
   <tr>
       <td colspan="10">
            <table width="100%" height="30" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;padding-bottom:1px;">
                <tr><td>{t}Highlighted article (Pay attention on images bigger than 391px){/t}</td></tr>
            </table>
            <div id="{$place}_0" class="seccion" style=min-height:40px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                {rendercontent items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_0'}
            </div>
       </td>
   </tr>
</table>
