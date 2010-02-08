<div class="CContainerCabeceraOpinion">
    <div class="CContainerFotoComentarista">
        {if $opinions[0].path_img}
             <img src="{$MEDIA_IMG_PATH_WEB}{$opinions[0].path_img}" alt="{$opinions[0].name}" width="112"/>
        {else}
            <img src="{$params.IMAGE_DIR}opinion/editorial.jpg" alt="{$opinions[0].name}" width="112"/>
        {/if}
    </div>
    <div class="CContainerDatosYTitularCabOpinion">
        <div class="CDatosCabOpinion"> 
            {if $author_id neq 1}
                {* 0 - autor, 1 - editorial, 2 - director *}
                <div class="CNombreCabOpinion"> </div>
                <div class="CSeparadorVAzulCabOpinion"></div>
            {else}
                <div class="CNombreCabOpinion">  <a class="CNombreCabOpinionLink" href="/opinions/opinions_do_autor/1/Editorial.html">Editorial</a> </div>
                <div class="CSeparadorVAzulCabOpinion"></div>
            {/if}
            <div class="CRolCabOpinion">{$opinions[0].condition}</div>
        </div>

        <div class="CTitularCabOpinion">
            {if $author_id neq 1}
                <h2>  <a class="CNombreAuthorLink" href="/opinions/opinions_do_autor/{$opinions[0].pk_author}/{$opinions[0].name|clearslash}.html">{$opinions[0].name}</a> </h2>
            {else}
                  <h2>  <a class="CNombreAuthorLink" href="/opinions/opinions_do_autor/1/Editorial.html">Editorial</a> </h2>
            {/if}
        </div>
    </div>
</div>
{*
<div class="listadoEnlacesPlanConecta">
    {section name=ac loop=$opinions start=0}
        {if $smarty.section.ac.index % 2 == 0}
            <div class="filaPortadaPC">
        {/if}
        <div class="elementoListadoMediaPagPortadaPC">
            <div class="contSeccionFechaListadoOpinion">
                <div class="fechaMediaListado">{$opinions[ac].created|date_format:"%d/%m/%y"}</div>
            </div>
            <div class="contTextoListadoOpinionTitulos">
                <div class="ListadoTitlesAuthor">
                    <div class="flechitaTextoPC">
                      <img alt="imagen" src="{$params.IMAGE_DIR}planConecta/flechitaTexto.gif"/>
                    </div>
                    <a href="{$opinions[ac].permalink}">{$opinions[ac].title|clearslash}</a>
                </div>
            </div>
        </div>
        {if $smarty.section.ac.index % 2 == 0}
            <div class="fileteVerticalIntraMedia"></div>
        {/if}
        {if $smarty.section.ac.index % 2 != 0 || $smarty.section.ac.last}
            </div>
            <div class="fileteHorizontalPC"></div>
        {/if}
    {/section}

</div>
*}


<div class="divListadoTitlesAuthor">
    {section name=ac loop=$opinions}
        <div class="ListadoTitlesAuthor">
            <div class="flechitaTextoPC">
                <img alt="imagen" src="{$params.IMAGE_DIR}planConecta/flechitaTexto.gif"/>
            </div>
            <a class="CNombreAuthorLink" href="{$opinions[ac].permalink}">{$opinions[ac].title|clearslash}</a>
            <div class="CFechaAuthorlist">
                {$opinions[ac].created|date_format:"%d/%m/%y %H:%M"}
            </div>
             <div class="CtextoAuthorlist">
                {$opinions[ac].body|clearslash|truncate:250|strip_tags}<a class="CAutorSigue" href="{$opinions[ac].permalink}"> &raquo;Sigue </a>
            </div>
        </div>

    {/section}
    <p align="center">{$pagination_list->links}</p>
</div>
