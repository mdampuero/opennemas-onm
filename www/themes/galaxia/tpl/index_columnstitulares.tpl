<div class="col1Titulares">
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Pol&iacute;tica
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        {section name=c loop=$titulares_polItica}
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia">{$titulares_polItica[c].created|date_format:"%H:%M"}h | </span>&nbsp;
              <span class="textoTitularDia"><a href="{$titulares_polItica[c].permalink}">{$titulares_polItica[c].title|clearslash}</a></span>
              </div>
          </div>
        {/section}
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Galicia
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        {section name=c loop=$titulares_galicia}
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia">{$titulares_galicia[c].created|date_format:"%H:%M"}h | </span>&nbsp;
              <span class="textoTitularDia"><a href="{$titulares_galicia[c].permalink}">{$titulares_galicia[c].title|clearslash}</a></span>
              </div>
          </div>
        {/section}
        </div>
    </div>
<br/>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Mundo
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        {section name=c loop=$titulares_mundo}
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia">{$titulares_mundo[c].created|date_format:"%H:%M"}h | </span>&nbsp;
              <span class="textoTitularDia"><a href="{$titulares_mundo[c].permalink}">{$titulares_mundo[c].title|clearslash}</a></span>
              </div>
          </div>
        {/section}
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>gente
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        {section name=c loop=4}
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia">{$titulares_gente[c]->created|date_format:"%H:%M"}h | </span>&nbsp;
              <span class="textoTitularDia"><a href="{$titulares_gente[c]->permalink}">{$titulares_gente[c]->title|clearslash}</a></span>
              </div>
          </div>
        {/section}
        </div>
    </div>
</div>
<div class="col2Titulares">
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Espa&ntilde;a
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        {section name=c loop=$titulares_espana}
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia">{$titulares_espana[c].created|date_format:"%H:%M"}h | </span>&nbsp;
              <span class="textoTitularDia"><a href="{$titulares_espana[c].permalink}">{$titulares_espana[c].title|clearslash}</a></span>
              </div>
          </div>
        {/section}
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>econom&iacute;a
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        {section name=c loop=$titulares_economia}
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia">{$titulares_economia[c].created|date_format:"%H:%M"}h | </span>&nbsp;
              <span class="textoTitularDia"><a href="{$titulares_economia[c].permalink}">{$titulares_economia[c].title|clearslash}</a></span>
              </div>
          </div>
        {/section}
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>cultura
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        {section name=c loop=$titulares_cultura}
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia">{$titulares_cultura[c].created|date_format:"%H:%M"}h | </span>&nbsp;
              <span class="textoTitularDia"><a href="{$titulares_cultura[c].permalink}">{$titulares_cultura[c].title|clearslash}</a></span>
              </div>
          </div>
        {/section}
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Sociedad
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        {section name=c loop=4}
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia">{$titulares_sociedad[c].created|date_format:"%H:%M"}h | </span>&nbsp;
              <span class="textoTitularDia"><a href="{$titulares_sociedad[c].permalink}">{$titulares_sociedad[c].title|clearslash}</a></span>
              </div>
          </div>
        {/section}
        </div>
    </div>
    {*<div class="grupoTitularesDia">
        <!-- PESTANYA TITULARES-->
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Comunicaci&oacute;n
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        {section name=c loop=4}
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia">{$titulares_comunicacion[c].created|date_format:"%H:%M"}h | </span>&nbsp;
              <span class="textoTitularDia"><a href="{$titulares_comunicacion[c].permalink}">{$titulares_comunicacion[c].title|clearslash}</a></span>
              </div>
          </div>
        {/section}
        </div>
    </div>*}
</div>