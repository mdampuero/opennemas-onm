{include file="pc_header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.post.action) || $smarty.post.action eq "list"}
<div id="msg" style="color:#BB1313;font-size:16px;font-weight:bold;padding:8px;">
     {$smarty.get.msg}
</div>
<div id="{$category}">
    <h3>Plan Conecta - Fotografías</h3>
    <table border="1" style="width:99%;" cellspacing="20">   
        <tr valign="top" >
            {foreach from=$photo_categorys  key=cat_id item=category_photo name=foo}
                <td  style="width:50%;">
                    <table class="adminheading">
                        <tr>
                            <th nowrap> {$category_photo->title|upper}  </th>
                        </tr>
                    </table>
                    {assign var=content value=$category_photo->contents}
                    {if !empty($content)}
                        <table class="adminlist" border=0>
                            <tr>
                                    <th class="title">Ver</th>
                                    <th class="title">Título</th>
                                    <th align="center">Fecha</th>
                                    <th align="center">Autor</th>
                                    <th align="center">IP</th>
                                    <th align="center">Favorito</th>
                                    <th align="center">Archivar</th>
                                    <th align="center">Despublicar</th>
                             </tr>
                             {section name=c loop= $content}
                                 <tr {cycle values="class=row0,class=row1"} >
                                     <td style="padding:10px;font-size: 11px;">
                                         <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;"  onmouseout="UnTip()" onmouseover="Tip('<img src=\'{$MEDIA_CONECTA_WEB}{$contents[c]->path_file}\' style=\'max-width:600px;\' > ', SHADOW, true, ABOVE, true, WIDTH, 600)" />
                                     </td>
                                     <td style="padding:1px;font-size: 11px;">{$content[c]->title}</td>
                                     <td style="padding:1px;font-size: 11px;" align="center">{$content[c]->created}</td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                         {assign var='id_author' value=$content[c]->fk_user}
                                         {$conecta_users[$id_author]->nick}
                                     </td>
                                     <td style="padding:1px;font-size: 11px;">{$content[c]->ip}</th>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                     {if $content[c]->favorite == 1}
                                            <a href="pc_photo.php?id={$content[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" class="favourite_on"  title="Publicado"></a>
                                     {else}
                                            <a href="pc_photo.php?id={$content[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" class="favourite_off"  title="Pendiente"></a>
                                     {/if}
                                     </td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                                <a href="pc_photo.php?id={$content[c]->id}&amp;action=change_status&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Publicado">
                                                       <img src="{php}echo($this->image_dir);{/php}archive_no2.png" border="0" alt="Archivar a hemeroteca" /></a>
                                     </td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                     {if $content[c]->available == 1}
                                            <a href="pc_photo.php?id={$content[c]->id}&amp;action=change_available&amp;status=0&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Publicado">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                                     {else}
                                            <a href="pc_photo.php?id={$content[c]->id}&amp;action=change_available&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Pendiente">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                                     {/if}
                                     </td>
                                 </tr>
                             {/section}
                         </table>
                      {else}
                          <h2 color="#996633">Esta sección está publicada, no debería estar vacía</h2>
                      {/if}
                  </td>
                  {if $smarty.foreach.foo.iteration % 2 == 0}
                      </tr><tr valign='top'>
                  {/if}
              {/foreach}
          </tr>
      </table>

    <h3>Plan Conecta - Vídeos</h3>
    <table border="1" style="width:99%;" cellspacing="20">
        <tr valign="top" >
            {foreach from=$video_categorys  key=cat_id item=category_video name=foo}
                <td  style="width:50%;">
                    <table class="adminheading">
                        <tr>
                            <th nowrap> {$category_video->title|upper}  </th>
                        </tr>
                    </table>
                    {assign var=content value=$category_video->contents}
                    {if !empty($content)}
                        <table class="adminlist" border=0>
                            <tr>
                                    <th class="title">Ver</th>
                                    <th class="title">Título</th>
                                    <th align="center">Fecha</th>
                                    <th align="center">Autor</th>
                                    <th align="center">IP</th>
                                    <th align="center">Favorito</th>
                                    <th align="center">Archivar</th>
                                    <th align="center">Despublicar</th>
                             </tr>
                         
                             {section name=c loop= $content}
                                 <tr {cycle values="class=row0,class=row1"} >
                                     <td style="padding:10px;font-size: 11px;">
                                         <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;"  onmouseout="UnTip()" onmouseover="Tip('<img src=\'{$MEDIA_CONECTA_WEB}{$contents[c]->path_file}\' style=\'max-width:600px;\' > ', SHADOW, true, ABOVE, true, WIDTH, 600)" />
                                     </td>
                                     <td style="padding:1px;font-size: 11px;">{$content[c]->title}</td>
                                     <td style="padding:1px;font-size: 11px;" align="center">{$content[c]->created}</td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                         {assign var='id_author' value=$content[c]->fk_user}
                                         {$conecta_users[$id_author]->nick}
                                     </td>
                                     <td style="padding:1px;font-size: 11px;">{$content[c]->ip}</th>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                     {if $content[c]->favorite == 1}
                                            <a href="pc_video.php?id={$content[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" class="favourite_on"  title="Publicado"></a>
                                     {else}
                                            <a href="pc_video.php?id={$content[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" class="favourite_off"  title="Pendiente"></a>
                                     {/if}
                                     </td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                                <a href="pc_video.php?id={$content[c]->id}&amp;action=change_status&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Publicado">
                                                       <img src="{php}echo($this->image_dir);{/php}archive_no2.png" border="0" alt="Archivar a hemeroteca" /></a>
                                     </td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                     {if $content[c]->available == 1}
                                            <a href="pc_video.php?id={$content[c]->id}&amp;action=change_available&amp;status=0&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Publicado">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                                     {else}
                                            <a href="pc_video.php?id={$content[c]->id}&amp;action=change_available&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Pendiente">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                                     {/if}
                                     </td>
                                 </tr>
                             {/section}
                         </table>
                      {else}
                          <h2 color="#996633">Esta sección está publicada, no debería estar vacía</h2>
                      {/if}
                  </td>
                  {if $smarty.foreach.foo.iteration % 2 == 0}
                      </tr><tr valign='top'>
                  {/if}
              {/foreach}
          </tr>
      </table>

    <h3>Plan Conecta - Opiniones</h3>
    <table border="1" style="width:99%;" cellspacing="20">
        <tr valign="top" >
            {foreach from=$opinion_categorys  key=cat_id item=category_opinion name=foo}
                <td  style="width:50%;">
                    <table class="adminheading">
                        <tr>
                            <th nowrap> {$category_opinion->title|upper}  </th>
                        </tr>
                    </table>
                    {assign var=content value=$category_opinion->contents}
                    {if !empty($content)}
                        <table class="adminlist" border=0>
                            <tr>
                                <th class="title">Ver</th>
                                <th class="title">Título</th>
                                <th align="center">Fecha</th>
                                <th align="center">Autor</th>
                                <th align="center">IP</th>
                                <th align="center">Favorito</th>
                                <th align="center">Archivar</th>
                                <th align="center">Despublicar</th>
                            </tr>
                        
                            {section name=c loop= $content}
                                 <tr {cycle values="class=row0,class=row1"} >
                                     <td style="padding:10px;font-size: 11px;">
                                         <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;"  onmouseout="UnTip()" onmouseover="Tip('<img src=\'{$MEDIA_CONECTA_WEB}{$contents[c]->path_file}\' style=\'max-width:600px;\' > ', SHADOW, true, ABOVE, true, WIDTH, 600)" />
                                     </td>
                                     <td style="padding:1px;font-size: 11px;">{$content[c]->title}</td>
                                     <td style="padding:1px;font-size: 11px;" align="center">{$content[c]->created}</td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                         {assign var='id_author' value=$content[c]->fk_user}
                                         {$conecta_users[$id_author]->nick}
                                     </td>
                                     <td style="padding:1px;font-size: 11px;">{$content[c]->ip}</th>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                     {if $content[c]->favorite == 1}
                                            <a href="pc_opinion.php?id={$content[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" class="favourite_on"  title="Publicado"></a>
                                     {else}
                                            <a href="pc_opinion.php?id={$content[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" class="favourite_off"  title="Pendiente"></a>
                                     {/if}
                                     </td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                                <a href="pc_opinion.php?id={$content[c]->id}&amp;action=change_status&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Publicado">
                                                       <img src="{php}echo($this->image_dir);{/php}archive_no2.png" border="0" alt="Archivar a hemeroteca" /></a>
                                     </td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                     {if $content[c]->available == 1}
                                            <a href="pc_opinion.php?id={$content[c]->id}&amp;action=change_available&amp;status=0&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Publicado">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                                     {else}
                                            <a href="pc_opinion.php?id={$content[c]->id}&amp;action=change_available&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Pendiente">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                                     {/if}
                                     </td>
                                 </tr>
                             {/section}
                         </table>
                      {else}
                          <h2 color="#996633">Esta sección está publicada, no debería estar vacía</h2>
                      {/if}
                  </td>
                  {if $smarty.foreach.foo.iteration % 2 == 0}
                      </tr><tr valign='top'>
                  {/if}
              {/foreach}
          </tr>
      </table>

    <h3>Plan Conecta - Cartas</h3>
    <table border="1" style="width:99%;" cellspacing="20">
        <tr valign="top" >
            {foreach from=$letter_categorys  key=cat_id item=category_letter name=foo}
                <td  style="width:50%;">
                    <table class="adminheading">
                        <tr>
                            <th nowrap> {$category_letter->title|upper}  </th>
                        </tr>
                    </table>
                    {assign var=content value=$category_letter->contents}
                    {if !empty($content)}
                        <table class="adminlist" border=0>
                            <tr>
                                <th class="title">Ver</th>
                                <th class="title">Título</th>
                                <th align="center">Fecha</th>
                                <th align="center">Autor</th>
                                <th align="center">IP</th>
                                <th align="center">Favorito</th>
                                <th align="center">Archivar</th>
                                <th align="center">Despublicar</th>
                            </tr>
                         
                             {section name=c loop= $content}
                                 <tr {cycle values="class=row0,class=row1"} >
                                     <td style="padding:10px;font-size: 11px;">
                                         <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;"  onmouseout="UnTip()" onmouseover="Tip('<img src=\'{$MEDIA_CONECTA_WEB}{$contents[c]->path_file}\' style=\'max-width:600px;\' > ', SHADOW, true, ABOVE, true, WIDTH, 600)" />
                                     </td>
                                     <td style="padding:1px;font-size: 11px;">{$content[c]->title}</td>
                                     <td style="padding:1px;font-size: 11px;" align="center">{$content[c]->created}</td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                         {assign var='id_author' value=$content[c]->fk_user}
                                         {$conecta_users[$id_author]->nick}
                                     </td>
                                     <td style="padding:1px;font-size: 11px;">{$content[c]->ip}</th>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                     {if $content[c]->favorite == 1}
                                            <a href="pc_letter.php?id={$content[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" class="favourite_on"  title="Publicado"></a>
                                     {else}
                                            <a href="pc_letter.php?id={$content[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" class="favourite_off"  title="Pendiente"></a>
                                     {/if}
                                     </td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                                <a href="pc_letter.php?id={$content[c]->id}&amp;action=change_status&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Publicado">
                                                       <img src="{php}echo($this->image_dir);{/php}archive_no2.png" border="0" alt="Archivar a hemeroteca" /></a>
                                     </td>
                                     <td style="padding:1px;font-size: 11px;" align="center">
                                     {if $content[c]->available == 1}
                                            <a href="pc_letter.php?id={$content[c]->id}&amp;action=change_available&amp;status=0&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Publicado">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                                     {else}
                                            <a href="pc_letter.php?id={$content[c]->id}&amp;action=change_available&amp;status=1&amp;category={$content[c]->fk_pc_content_category}&amp;from=index" title="Pendiente">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                                     {/if}
                                     </td>
                                 </tr>
                             {/section}
                         </table>
                      {else}
                          <h2 color="#996633">Esta sección está publicada, no debería estar vacía</h2>
                      {/if}
                  </td>
                  {if $smarty.foreach.foo.iteration % 2 == 0}
                      </tr><tr valign='top'>
                  {/if}
              {/foreach}
          </tr>
      </table>

    <h3>Plan Conecta - Encuestas</h3>
    <table class="adminlist">
        <tr>
            <th class="title" align="left">T&iacute;tulo</th>
            <th class="title" align="left">Subt&iacute;tulo</th>
            <th align="center">Votos</th>
            <th align="center">Visto</th>
            <th align="center">Fecha</th>
            <th align="center">Favorito</th>
            <th align="center">Ver en columna</th>
            <th align="center">Publicado</th>

        </tr>

	{section name=c loop=$polls}
            <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;">

                <td style="padding:10px;font-size: 11px;width:40%;">
                        {$polls[c]->title|clearslash}
                </td>
                <td style="padding:10px;font-size: 11px;width:20%;">
                        {$polls[c]->subtitle|clearslash}
                </td>
                <td style="padding:10px;font-size: 11px;width:10%;" align="center">
                        {$polls[c]->total_votes}
                </td>

                <td style="padding:1px;font-size: 11px;width:10%;" align="center">
                        {$polls[c]->views}
                </td>
                <td style="padding:1px;width:10%;font-size: 11px;" align="center">
                        {$polls[c]->created}
                </td>
                <td style="padding:10px;font-size: 11px;width:6%;" align="center">
                    {if $polls[c]->favorite == 1}
                        <a href="pc_poll.php?id={$polls[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$polls[c]->fk_pc_content_category}&amp;from=index" class="favourite_on" title="Publicado"></a>
                    {else}
                        <a href="pc_poll.php?id={$polls[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$polls[c]->fk_pc_content_category}&amp;from=index" class="favourite_off" title="Pendiente"></a>
                    {/if}
                </td>
                <td style="padding:10px;font-size: 11px;width:6%;" align="center">
                    {if $polls[c]->view_column == 1}
                        <a href="pc_poll.php?id={$polls[c]->id}&amp;action=set_view_column&amp;status=0&amp;category={$polls[c]->fk_pc_content_category}&amp;from=index" class="no_home" title="Publicado"></a>
                    {else}
                        <a href="pc_poll.php?id={$polls[c]->id}&amp;action=set_view_column&amp;status=1&amp;category={$polls[c]->fk_pc_content_category}&amp;from=index" class="go_home" title="Pendiente"></a>
                    {/if}
                </td>
                <td style="padding:10px;font-size: 11px;width:10%;" align="center">
                    {if $polls[c]->available == 1}
                        <a href="pc_poll.php?id={$polls[c]->id}&amp;action=change_available&amp;status=0&amp;category={$polls[c]->fk_pc_content_category}&amp;from=index" title="Publicado">
                            <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                    {else}
                        <a href="pc_poll.php?id={$polls[c]->id}&amp;action=change_available&amp;status=1&amp;category={$polls[c]->fk_pc_content_category}&amp;from=index" title="Pendiente">
                            <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                    {/if}
                </td>
            </tr>
        {sectionelse}
              <h2 color="#996633">Esta sección está publicada, no debería estar vacía</h2>
	{/section}
	</table>
	
	
</div>

{/if}


{include file="footer.tpl"}