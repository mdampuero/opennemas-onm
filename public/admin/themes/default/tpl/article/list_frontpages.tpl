
<table class="adminlist">
    <tr valign="top">
       <td>
         <table class="adminlist">
            <tr>
                    <th align="center"></th>
                    <th align="center">T&iacute;tulo</th>
                    <th align="center" style="width:50px;">Visto</th>
                    <th align="center" style="width:50px;">Votos</th>
                    <th align="center" style="width:50px;"><img src="{$params.IMAGE_DIR}coment.png" border="0" alt="Numero comentarios" /></th>
                    <th align="center" style="width:70px;">Fecha</th>

                    <th align="center" style="width:110px;">Publisher</th>
                    <th align="center" style="width:110px;">Last Editor</th>
                    <th align="center" style="width:50px;">Editar</th>
                    <th align="center" style="width:50px;">Archivar</th>
                    <th align="center" style="width:50px;">Despub</th>
                    <th align="center" style="width:50px;">Elim</th>
                </tr>
            </table>
    </td>
    </tr>
    <tr>
        <td colspan="2"> <div id="art"></div>
           <div id="art2" class="seccion" style="float:left;width:100%; border:1px solid gray;"> <br>
               <table class="adminlist">
                    <tr valign="top">
                        <td colspan="2">
                            <div id="top" style="border:1px #333;">
                                {renderarticlecondition items=$articles tpl="article/partials/_article_render_frontpages_home.tpl"   odd_rating=$art_rating odd_comment=$art_comment odd_editors =$art_editors odd_publishers=$art_publishers} </div>
                           <div id="left">   </div> <div id="right">   </div>
                          </td>
                    </tr>
                   {* <tr valign="top">
                        <td width="50%" style="border-right:1px solid #DDDDDD;">
                           <div id="left">
                                {renderarticle items=$articles tpl="article_render_frontpages_home.tpl" placeholder="placeholder_0_1" odd_rating=$art_rating odd_comment=$art_comment odd_editors =$art_editors odd_publishers=$art_publishers}
                           </div>
                         </td>
                         <td width="50%" style="padding:4px;">
                            <div id="right">
                                    {renderarticle items=$articles tpl="article_render_frontpages_home.tpl"  placeholder="placeholder_1_0" odd_rating=$art_rating odd_comment=$art_comment odd_editors =$art_editors odd_publishers=$art_publishers}
                                    {renderarticle items=$articles tpl="article_render_frontpages_home.tpl"  placeholder="placeholder_1_1" odd_rating=$art_rating odd_comment=$art_comment odd_editors =$art_editors odd_publishers=$art_publishers}
                                    {renderarticle items=$articles tpl="article_render_frontpages_home.tpl"  placeholder="placeholder_1_2" odd_rating=$art_rating odd_comment=$art_comment odd_editors =$art_editors odd_publishers=$art_publishers}
                                    {renderarticle items=$articles tpl="article_render_frontpages_home.tpl"  placeholder="placeholder_1_3" odd_rating=$art_rating odd_comment=$art_comment odd_editors =$art_editors odd_publishers=$art_publishers}
                            </div>
                          </td>
                    </tr>
                    *}
                </table>

           </div>
        </td>
    </tr>
</table>
