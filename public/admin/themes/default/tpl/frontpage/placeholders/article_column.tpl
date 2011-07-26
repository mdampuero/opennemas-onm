<table style="border-right:1px solid #ccc">
    <!--<tr>
         <th align="center"> </th>
        <th align="center" style="width:200px">T&iacute;tulo</th>
        {if $category neq 'home'}
            <th align="center" style="width:30px;" class="rot270">Home</th>
        {else}
            <th align="center" style="width:50px;">Secci&oacute;n</th>
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
            <div id="{$place}_0" class="seccion" style=min-height:40px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                {rendercontent items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_0'}
            </div>

            <table width="100%" height="30" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;padding-bottom:1px;">
                <tr><td >Publi: Columna {substr($place,-1)+1} lugar 1</td></tr>
            </table>

            <div id="{$place}_1" class="seccion" style="min-height:120px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                {rendercontent items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_1'}
            </div>

            <table width="100%" height="30" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;padding-bottom:1px;">
                <tr><td >Publi: Columna {substr($place,-1)+1} lugar 2</td></tr>
            </table>

            <div id="{$place}_2" class="seccion" style="min-height:60px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                {rendercontent items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_2'}
            </div>

            <table width="100%" height="30" class="aa" style="background-color:#ccc !important;text-align:center;padding:0px;padding-bottom:1px;">
                <tr><td >{if $place eq 'placeholder_1'}Contenido widget interior {else}Publicidad 200x200 {if $place eq 'placeholder_0'}Izda{else}Dcha{/if}{/if}</td></tr>
            </table>
            <br>

            <table width="100%" height="30" class="aa" style="background-color:#ccc !important;text-align:center;padding:0px;padding-bottom:1px;">
                <tr><td >Separador con publicidad larga</td></tr>
            </table>

            <div id="{$place}_3" class="seccion" style="min-height:70px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                {rendercontent items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_3'}
            </div>

            <table width="100%" height="30" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;padding-bottom:1px;">
                <tr><td >Publi: Columna {substr($place,-1)+1} lugar 4</td></tr>
            </table>

            <div id="{$place}_4" class="seccion" style="min-height:70px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                {rendercontent items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_4'}
            </div>

            <table width="100%" height="30" class="aa" style="background-color:#E4DDC9;text-align:center;padding:0px;padding-bottom:1px;">
                <tr><td >Publi: Columna {substr($place,-1)+1} lugar 5</td></tr>
            </table>

            <div id="{$place}_5" class="seccion" style="min-height:70px;width:100%;background-color:#FFF;text-align:center;padding:0px;">
                {rendercontent items=$frontpage_articles tpl="article_render_fila.tpl"  placeholder=$place|cat:'_5'}
            </div>

            <table width="100%" height="30" class="aa" style="background-color:#ccc !important;text-align:center;padding:0px;padding-bottom:1px;">
                <tr><td >{if $place eq 'placeholder_1'}Contenido widget interior {else}Publi 200x200 {if $place eq 'placeholder_0'}Izda{else}Dcha{/if}{/if}</td></tr>
            </table>
            <br>

            <table width="100%" height="30" class="aa" style="background-color:#ccc !important;text-align:center;padding:0px;padding-bottom:1px;">
                <tr><td >Separador con publicidad larga</td></tr>
            </table>
        </td>
   </tr>
</table>
