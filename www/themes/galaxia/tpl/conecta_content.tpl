<div class="zonaClasificacionContenidosPortadaPC">
    <div class="listadoEnlacesPlanConecta">

        <div class="titleContent"> FOTOGRAFÍAS</div>
        <div class="separadorContents" style="background-color:#5ED63B;"> </div>
        {foreach from=$photo_categorys  key=cat_id item=category_photo name=foo}
            {assign var=content value=$category_photo->contents}
            {if $smarty.foreach.foo.iteration % 2 != 0}
                <div class="filaPortadaPC" style="margin-top:10px;">
            {/if}
                <div class="elementoListadoMediaPagPortadaPC">
                    <div class="fotoElemPC"><a href="{$content[0]->permalink}"><img  style="width:150px;" src="{$MEDIA_CONECTA_WEB}{$content[0]->path_file}" /></a></div>
                    <div class="contSeccionFechaListadoPortadaPC">
                        <div class="seccionMediaListado"><a href='/conecta/fotografias/{$category_photo->name}/'>{$category_photo->title|upper} </a></div>
                        {assign var=id_author value=$content[0]->fk_user}
                        <div class="contentListado">  <a href="{$content[0]->permalink}">{$content[0]->title|clearslash}</a>
                          <br />  Enviado por: {$conecta_users[$id_author]->nick}
                          {humandate article=$content created=$content[0]->created}
                          
                           ... 
                        </div>
                    </div>
                    
                </div>
                {if $smarty.foreach.foo.iteration % 2 != 0}
                      <div class="fileteVerticalIntraMedia"></div>
                {else}
                    </div>
                    <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
                {/if}
         {/foreach}
         {if $smarty.foreach.foo.iteration % 2 != 0}
             </div>
              <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
         {/if}

          <div class="titleContent"> VIDEOS</div>
        <div class="separadorContents"  style="background-color:#A9087E;"> </div>
        {foreach from=$video_categorys  key=cat_id item=category_video name=foo}
            {assign var=content value=$category_video->contents}
            {if $smarty.foreach.foo.iteration % 2 != 0}
                <div class="filaPortadaPC" style="margin-top:10px;">
            {/if}
                <div class="elementoListadoMediaPagPortadaPC">
                    <div class="fotoElemPC"><a href="{$content[0]->permalink}"><img style="width:150px;height:100px;" src="http://i4.ytimg.com/vi/{$content[0]->code}/default.jpg" /></a></div>
                    <div class="contSeccionFechaListadoPortadaPC">
                        <div class="seccionMediaListado"><a href='/conecta/videos/{$category_video->name}/'>{$category_video->title|upper}</a> </div>
                        {assign var='id_author' value=$content[0]->fk_user}
                         <div class="contentListado">  <a href="{$content[0]->permalink}">{$content[0]->title|clearslash}</a>
                          <br />  Enviado por: {$conecta_users[$id_author]->nick}
                          {humandate article=$content created=$content[0]->created}
                        </div>
                     </div>
                </div>
                {if $smarty.foreach.foo.iteration % 2 != 0}
                      <div class="fileteVerticalIntraMedia"></div>
                {else}
                    </div>
                    <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
                {/if}
         {/foreach}
         {if $smarty.foreach.foo.iteration % 2 != 0}
             </div>
              <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
         {/if}

          <div class="titleContent"> OPINIÓN</div>
        <div class="separadorContents" style="background-color:#FFCA00;"> </div>
        {foreach from=$opinion_categorys  key=cat_id item=category_opinion name=foo}
            {assign var='content' value=$category_opinion->contents}
            {if $smarty.foreach.foo.iteration % 2 != 0}
                <div class="filaPortadaPC" style="margin-top:10px;">
            {/if}
               {section name=c loop= $content}
                <div class="elementoListadoMediaPagPortadaPC">                  
                    <div class="contSeccionFechaListado2PortadaPC">
                        <div class="seccionMediaListado"><a href='/conecta/opiniones/{$category_opinion->name}/'>{$category_opinion->title|upper} </a></div>
                        {assign var='id_author' value=$content[c]->fk_user}
                         <div class="contentListado">  <a href="{$content[c]->permalink}">{$content[c]->title|clearslash}</a>
                          <br />  Enviado por: {$conecta_users[$id_author]->nick}
                          {humandate article=$content created=$content[0]->created}
                        </div>
                      </div>
                </div>
                    <div class="fileteVerticalIntraMedia"></div>
                {/section}
                {if $smarty.foreach.foo.iteration % 2 != 0}
                      <div class="fileteVerticalIntraMedia"></div>
                {else}
                    </div>
                    <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
                {/if}
         {/foreach}
         {if $smarty.foreach.foo.iteration % 2 != 0}
             </div>
              <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
         {/if}

          <div class="titleContent"> CARTAS </div>
        <div class="separadorContents" style="background-color:#00E2F6;"> </div>
        {foreach from=$letter_categorys  key=cat_id item=category_letter name=foo}
            {assign var='content' value=$category_letter->contents}
            {if $smarty.foreach.foo.iteration % 2 != 0}
                <div class="filaPortadaPC" style="margin-top:10px;">
            {/if}
               {section name=c loop= $content}
                <div class="elementoListadoMediaPagPortadaPC">                    
                    <div class="contSeccionFechaListado2PortadaPC">
                        <div class="seccionMediaListado"><a href='/conecta/cartas/{$category_letter->name}/'>{$category_letter->title|upper} </a></div>
                        {assign var='id_author' value=$content[c]->fk_user}
                         <div class="contentListado">  <a href="{$content[c]->permalink}">{$content[c]->title|clearslash}</a>
                          <br />  Enviado por: {$conecta_users[$id_author]->nick}
                          {humandate article=$content created=$content[0]->created}
                        </div>
                     </div>
                </div>
                <div class="fileteVerticalIntraMedia"></div>
                {/section}
                {if $smarty.foreach.foo.iteration % 2 != 0}
                      <div class="fileteVerticalIntraMedia"></div>
                {else}
                    </div>
                    <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
                {/if}
         {/foreach}
         {if $smarty.foreach.foo.iteration % 2 != 0}
             </div>
              <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
         {/if}


    </div>
</div>
