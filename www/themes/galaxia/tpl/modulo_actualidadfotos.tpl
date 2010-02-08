<div class="actualidadFotos">
    <div class="cabeceraActualidadFotos"><a href="{$lastAlbumContent->permalink}"><img src="{$params.IMAGE_DIR}actualidadVideosFotos/logoActualidadFotos.gif" alt="Actualidad Fotos"></a></div>
    <div class="zonaVisualizacionFotos">
        <div class="CPiezaActualidadFotosHome">
            <div class="CCuerpoPiezaFotoXornal">
                <div class="CContainerFotoActualidadFotos">
                    <a title="{$lastAlbumContent->title|clearslash}" href="{$lastAlbumContent->permalink}">
                        <img style="height: 250px;" alt=" {$lastAlbumContent->title|clearslash}" src="{$MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbumContent->id}.jpg"/>
                    </a>
                </div>
                <div class="CPieFotoPiezaFotoXornal">
                    <div class="CFlechaGrisPieGenteXornal"></div>
                        <a title="{$lastAlbumContent->title|clearslash}" href="{$lastAlbumContent->permalink}">
                            {$lastAlbumContent->title|clearslash}
                        </a>
                </div>
            </div>
        </div>
    </div>
    <div class="linkMasMedia"><a href="{$lastAlbumContent->permalink}">+ Foto galerías</a></div>
</div>
