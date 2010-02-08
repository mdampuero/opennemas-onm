<div class="containerActualidadFoto">
    <div class="column123">
        <div class="cabeceraVisorVideos">
            {if $category_name eq 'humor-grafico'}<img alt="imagen" src="{$params.IMAGE_DIR}noticiasXPress/logoHumorGrafico.jpg"/></div>
            {else}<img alt="imagen" src="{$params.IMAGE_DIR}galeriaFotos/cabeceraGaleriaFotos.gif"/></div>
            {/if}
        <!-- PESTANYAS -->
        <div class="CContainerPestanyasActualidadFotos">
            <div class="pestanyaSelecList">
                <div class="contInfoPestanyaGrande">
                    <div class="flechaPestanyaSelecList"></div>
                    <div class="textoPestanyaSelecList">FOTOGALER&Iacute;AS MAS VISTAS</div>
                </div>
                <div class="cierrePestanyaSelecList"></div>
            </div>
            <div class="espacioInterPestanyasGrande"></div>
            <a href="/video">
            <div class="pestanyaNoSelecList">
                <div class="contInfoPestanyaGrande">
                    <div class="flechaPestanyaNoSelecList"></div>
                    <div class="textoPestanyaNoSelecList">ACTUALIDAD VIDEO</div>
                </div>
                <div class="cierrePestanyaNoSelecList"></div>
            </div>
            </a>
        </div>
        <!-- ***************** VISOR DE FOTOS **************-->
        <div class="zonaVisorFotos">
            <div class="cuerpoVisorFotos">
                <div class="contVisorFoto">
                  {if $albumArray}
                    <a href="{$MEDIA_IMG_PATH_WEB}{$albumArray[0]->path_file}{$albumArray[0]->name}" class="lightwindow" rel='xornal[album]' title="{$album->title|clearslash|escape:'html'}" caption="{$albumDescrip[0]|clearslash|escape:'html'}" author="{$album->agency}" onClick="return false;">
                        <div class="CVisorRealFoto"><img alt="imagen" width="498px" height="340px" src="{$MEDIA_IMG_PATH_WEB}{$albumArray[0]->path_file}{$albumArray[0]->name}"></div>
                        <div class="CBandaAzulVisorFoto">
                            <div class="CVerFotosVisorFotosBandaAzul">                          
                                <img alt="imagen" src="{$params.IMAGE_DIR}galeriaFotos/flechitaBlanca.gif"/>Haz clic para ver las fotos
                            </div>
                        </div>
                    </a>
                    {else}
                        <div class="CVisorRealFoto"><img alt="imagen" width="498px" height="340px" src="{$MEDIA_IMG_PATH_WEB}{$albumArray[0]->path_file}{$albumArray[0]->name}"></div>
                    {/if}
                </div>
                <div class="marcoInfoFoto">
                    <div class="contInfoFoto">
                        <div class="posInfoFoto">
                            <div class="CTitularVisorFotos">{$album->title|clearslash|escape:'html'}</div>
                            <div class="CTextoVisorFotos">{$album->description|clearslash|escape:'html'}</div>
                            <div class="CClickParaVerFotosVisorFotos">
                               {if $albumArray}   <a href="{$MEDIA_IMG_PATH_WEB}{$albumArray[1]->path_file}{$albumArray[1]->name}" class="lightwindow" rel='xornal[album]' title="{$album->title|clearslash|escape:'html'}" caption="{$albumDescrip[1]|clearslash|escape:'html'}" author="{$album->agency}"><img alt="imagen" src="{$params.IMAGE_DIR}galeriaFotos/flechitaOscura.gif"/>Haz clic para ver las fotos</a> {/if}
                            </div>
                        </div>
                        <div class="agenciaInfoFoto"><img alt="imagen" src="{$params.IMAGE_DIR}galeriaFotos/flechitaClara.gif"/>{$album->agency}</div>
                    </div>
                </div>
            </div>
        </div>
        <!--
            {literal}<a href="javascript: myLightWindow.activateWindow({{/literal}href: '{$MEDIA_IMG_PATH_WEB}{$albumArray[0]->path_file}{$albumArray[0]->name}', title: '{$album->title|clearslash|escape:'html'}', author: '{$album->agency}', caption: '{$albumDescrip[0]|clearslash|escape:'html'}', rel: 'xornal[album]'{literal}});">{/literal}<img alt="imagen" src="{$params.IMAGE_DIR}galeriaFotos/flechitaBlanca.gif"/>Haz clic para ver las fotos</a>
        -->
        {section name=n start=2 loop=$albumArray}
            <a href="{$MEDIA_IMG_PATH_WEB}{$albumArray[n]->path_file}{$albumArray[n]->name}" class="lightwindow lightwindow_hidden" rel='xornal[album]' title="{$album->title|clearslash|escape:'html'}" caption="{$albumDescrip[n]|clearslash|escape:'html'}" author="{$album->agency}">image #{$smarty.section.n.index}</a>
        {/section}
        <!-- PESTANYA -->
        <div class="zonaPestanyasMedia3Cols">
            <div class="pestanyaSelecList">
                <div class="contInfoPestanyaGrande">
                    <div class="flechaPestanyaSelecList"></div>
                    <div class="textoPestanyaSelecList">MAS FOTOGALER&Iacute;AS</div>
                </div>
                <div class="cierrePestanyaSelecList"></div>
            </div>
        </div>
    </div>
    <div class="column123">
        <div class="agrupaColumnas fondoActualidadVideo">
            <div class="column12 separacionVertical15">
                <div class="zonaClasificacionVideos">
                    <div class="listadoMedia" id="div_albums">
                        {section name=n loop=$list_albums}
                        <div class="elementoListadoMediaPag">
                            <div class="fotoElemMedia">
                               <img style="height:88px;" src="/media/images/album/crops/{$list_albums[n]->id}.jpg">
                            </div>
                            <div class="contSeccionFechaListado">
                                <div class="seccionMediaListado"><a href="{$list_albums[n]->permalink}" style="color:#004B8D;">{$list_albums[n]->title|clearslash}</a></div>
                                <div class="fechaMediaListado">{$list_albums[n]->created|date_format:"%d/%m/%Y"}</div>
                            </div>
                            <div class="contTextoElemMediaListado">                               
                                <div class="textoElemMediaListado">
                                    <a href="{$list_albums[n]->permalink}"> {$list_albums[n]->description|clearslash}</a>
                                </div>
                            </div>
                            <div class="fileteIntraMedia"></div>
                        </div>
                        {/section}
                       

                        <div class="posPaginadorGaliciaTitulares">
							<div class="CContenedorPaginado">
								<div class="link_paginador">+ Albums </div>
								<div class="CPaginas"> {$pages->links}									
								</div>
							</div>
						</div>
                        
                    </div>
                </div>
                
            </div>
            <div class="column3">
                {include file="modulo_column3_containerFotoVideoDiaMasListado.tpl"}
                <div class="separadorHorizontal"></div>
                {include file="modulo_weather.tpl"}
            </div>
        </div>
    </div>
</div>