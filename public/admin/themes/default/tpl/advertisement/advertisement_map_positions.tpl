{literal}
<style>
#advertisement-mosaic {
    margin: 0;
    padding: 0;
    position: relative;
    width: 240px;
    height: 880px;
}

#advertisement-mosaic-frame {
    position: absolute;
    top: 0;
    left: 0;
    width: 0px;
    height: 0px;
    z-index: 100;
    border: 1px dashed #F00;
    background-color: #996633;
}
</style>
{/literal}

<div id="advertisement-mosaic" style="display: none;">
    <div id="advertisement-mosaic-frame"></div>
    <img src="/admin/images/advertisement/front_lucidity_240.png" width="240" height="878" border="0" usemap="#mapPortada" />
</div>

<map name="mapPortada">
    <!-- #$-:Image map file created by GIMP Image Map plug-in -->
    <!-- #$-:GIMP Image Map plug-in by Maurits Rijk -->
    <!-- #$-:Please do not edit lines starting with "#$" -->
    <!-- #$VERSION:2.3 -->
    <!-- #$AUTHOR:sandra -->
    <area shape="rect" coords="1,1,176,26" alt="Big banner superior izquierdo" title={t}"Big banner top left"{/t}
          href="javascript:adPositionPortada.selectPosition(1);" />
    <area shape="rect" coords="177,1,239,26" alt="Banner superior derecho" title={t}"Banner top right"{/t}
          href="javascript:adPositionPortada.selectPosition(2);" />
    <area shape="rect" coords="3,277,81,339" alt="Botón columna 1" title={t}"Button column 1"{/t}
          href="javascript:adPositionPortada.selectPosition(3);" />
    <area shape="rect" coords="160,346,237,410" alt="Botón columna 3" title={t}"Button column 3"{/t}
          href="javascript:adPositionPortada.selectPosition(4);" />

    <area shape="rect" coords="3,553,158,575" alt="Separador horizontal" title={t}"Horizontal separator"{/t}
          href="javascript:adPositionPortada.selectPosition(5);" />
    <area shape="rect" coords="159,553,233,566" alt="Mini derecho 1" title={t}"Mini right 1"{/t}
          href="javascript:adPositionPortada.selectPosition(6);" />
    <area shape="rect" coords="159,566,233,578" alt="Mini derecho 2" title={t}"Mini right 2"{/t}
          href="javascript:adPositionPortada.selectPosition(7);" />

    <area shape="rect" coords="159,753,237,812" alt="Botón inferior derecho" title={t}"Button bottom right"{/t}
          href="javascript:adPositionPortada.selectPosition(8);" />
    <area shape="rect" coords="1,826,176,849" alt="Big banner inferior izquierdo"
        title={t}"Big banner bottom left"{/t} href="javascript:adPositionPortada.selectPosition(9);" />
    <area shape="rect" coords="177,826,239,849" alt="Banner inferior derecho" title={t}"Banner bottom right"{/t}
          href="javascript:adPositionPortada.selectPosition(10);" />

</map>
