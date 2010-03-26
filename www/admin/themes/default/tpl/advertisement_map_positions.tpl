{literal}
<style>
#advertisement-mosaic {
    margin: 0;
    padding: 0;    
    position: relative;
    width: 240px;
    height: 572px;
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
    <img src="images/advertisement/positions.jpg" width="240" height="572" border="0" usemap="#mapPortada" />
</div>

<map name="mapPortada">
    
    <area shape="rect" coords="1,1,183,23" alt="Big banner superior izquierdo" title="Big banner superior izquierdo"
          href="javascript:adPositionPortada.selectPosition(1);" />
    <area shape="rect" coords="186,1,239,23" alt="Banner superior derecho" title="Banner superior derecho" 
          href="javascript:adPositionPortada.selectPosition(2);" />
    <area shape="rect" coords="6,138,79,170" alt="Botón columna 1" title="Botón columna 1"
          href="javascript:adPositionPortada.selectPosition(3);" />
    <area shape="rect" coords="163,204,236,236" alt="Botón columna 3" title="Botón columna 3"
          href="javascript:adPositionPortada.selectPosition(4);" />
    <area shape="rect" coords="6,320,160,345" alt="Separador horizontal" title="Separador horizontal"
          href="javascript:adPositionPortada.selectPosition(5);" />
    <area shape="rect" coords="163,320,236,332" alt="Mini derecho 1" title="Mini derecho 1"
          href="javascript:adPositionPortada.selectPosition(6);" />
    <area shape="rect" coords="163,333,236,345" alt="Mini derecho 2" title="Mini derecho 2"
          href="javascript:adPositionPortada.selectPosition(7);" />
    <area shape="rect" coords="163,485,236,517" alt="Botón inferior derecho" title="Botón inferior derecho"
          href="javascript:adPositionPortada.selectPosition(8);" />
    <area shape="rect" coords="3,531,182,549" alt="Big banner inferior izquierdo"
        title="Big banner inferior izquierdo" href="javascript:adPositionPortada.selectPosition(9);" />
    <area shape="rect" coords="185,531,239,549" alt="Banner inferior derecho" title="Banner inferior derecho"
          href="javascript:adPositionPortada.selectPosition(10);" />
    
</map>


