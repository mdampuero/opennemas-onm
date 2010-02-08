<div class="containerOpinion">
    <a href="/seccion/opinion/"><img src="{$params.IMAGE_DIR}opinion/logoOpinion.gif" alt="Opinion Xornal" /></a>
    <div class="listaPiezasOpinion">
        <div class="parPiezasOpinion">
          <div class="piezaOpinionPrimeraFila">
            <div class="cabeceraPiezaOpinion">Editoriales:</div>
            <div class="cuerpoPiezaOpinionPrimera">
                <div class="textoPiezaOpinionPrimera">
                    {section name="ac" loop=$editorial}
                    <img src="{$params.IMAGE_DIR}flechitaMenu.gif" alt=""/>
                    <a href="{$editorial[ac]->permalink|default:"#"}">{$editorial[ac]->title|clearslash}</a>
                    <br /><br />
                    {/section}
                </div>
            </div>
            </div>
            <div class="separadorVerticalDirectorOpinion"></div>
            <div class="piezaOpinionPrimeraFila">
                <div class="cabeceraPiezaOpinion"><a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/2/{$cartadirector->name|clearslash}.html">{$cartadirector->name}</a><br/>Director</div>
                <div class="cuerpoPiezaOpinion">
                    <div class="fotoPiezaOpinion">
                     {if $cartadirector->foto }
                        <img src="{$MEDIA_IMG_PATH_WEB}{$cartadirector->foto}" alt="{$cartadirector->name}" height="70"/>
                      {else} <img src="{$params.IMAGE_DIR}opinion/fondoFraseOpinion.gif" alt="{$cartadirector->name}"/>
                      {/if}
                    </div>
                    <div class="textoPiezaOpinion"><img src="{$params.IMAGE_DIR}flechitaMenu.gif" alt=""/>
                        <a href="{$cartadirector->permalink|default:"#"}">{$cartadirector->title|clearslash}</a></div>
                </div>
            </div>
            <div class="separadorHorizontalOpinion"></div>
        </div>
        <div class="parPiezasOpinion">
          {section name="ac" loop=$opiniones}
          <div class="piezaOpinion">
            {*<div class="cabeceraPiezaOpinion"><a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/{$opiniones[ac]->fk_author}/{$opiniones[ac]->name|clearslash}.html"> {$opiniones[ac]->name}</a></div>*}
            <div class="cuerpoPiezaOpinion">
              <div class="fotoPiezaOpinion">
                  { if $opiniones[ac]->photo} 
                    <img style="padding-right:4px;" align="left" src="{$MEDIA_IMG_PATH_WEB}{$opiniones[ac]->photo}" alt="{$opiniones[ac]->name}"/>
                  {/if}
                <a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/{$opiniones[ac]->fk_author}/{$opiniones[ac]->name|clearslash}.html"> {$opiniones[ac]->name}</a>
              </div>
              <div class="textoPiezaOpinion"><img src="{$params.IMAGE_DIR}flechitaMenu.gif" alt=""/>
                <a href="{$opiniones[ac]->permalink|default:"#"}">{$opiniones[ac]->title|clearslash}</a></div>
            </div>
          </div>
            {if $smarty.section.ac.index % 2 != 0}
            <div class="separadorHorizontalOpinion"></div>
            {else}<div class="separadorVerticalOpinion"></div>
            {/if}
          {/section}
        </div>
    </div>
</div>