<div class="containerLaBolsa">
    <div class="cabeceraLaBolsa">
        <img src="{$params.IMAGE_DIR}bolsa/logoBolsa.gif" alt="La Bolsa" />
    </div>
    <div class="cuerpoLaBolsa">
        {* 6 hours for cachelife/More info ./trunk/www/themes/xornal/plugins/function.remotecontent.php *}
        {remotecontent url="http://www.infobolsa.es/mini-ficha/ibex35.htm" onafter="remotecontent_onafter_infobolsa" cache="true" cachelife="30"}
    </div>
    <div class="cuerpoPiezaOpinionEconomia">
        <div class="fotoPiezaOpinionEconomia">
            <a href="/opinions/opinions_do_autor/56/Vicente Martin.html" class="contSeccionListadoPortadaPCAuthor">
                <img alt="Vicente Martin" src="/themes/xornal/images/opinion/analisis_vicente_martin.gif"/>
            </a>
        </div>
        <div class="textoPiezaOpinionEconomia"><img alt="" src="/themes/xornal/images/flechitaMenu.gif"/>
            <a href="{$opinionVicenteMartin->permalink|default:"#"}">{$opinionVicenteMartin->title|clearslash}</a>
        </div>
    </div>
</div>