{extends file="base/admin.tpl"}


{block name="content"}
<div class="modal hide" id="media-uploader">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 class="modal-title">{t}Media gallery{/t}</h3>
    </div>
    <div class="modal-body">
        <div class="modal-body-wrapper">
            <div class="modal-body-sidebar full-height">
                <ul>
                    <li><a href="#">{t}Upload{/t}</a></li>
                    <li><a href="#">{t}Browse elements{/t}</a></li>
                </ul>
            </div>
            <div class="modal-body-content full-height">
                <div class="modal-body-content-wrapper">
                    <p><a href="http://www.nuevatribuna.es/articulo/economia/las-deudas-del-futbol-con-hacienda/20121110182935083864.html" target="_blank"><u>La deuda global con la Agencia Tributaria</u></a> de las Sociedades Anónimas Deportivas (SAD), a 30 de abril de 2013, asciende a <strong>663.876.441 euros</strong>, de los que 506.504.230 euros corresponden a las SAD de primera división, según la respuesta del Gobierno a una pregunta escrita formulada por <strong>el portavoz socialista de Deportes, Manuel Pezzi.</strong></p>
                    <p><strong>El Gobierno no ha facilitado, sin embargo, la cifra de la deuda que mantienen con la Agencia Tributaria los clubes de fútbol de primera división</strong> que no son Sociedades Anónimas Deportivas –<strong>Real Madrid, F.C. Barcelona, Athletic Club de Bilbao y Osasuna</strong>-, ni en el resto de categorías, argumentando que “habida cuenta del reducido número de clubes que no tienen la consideración de Sociedad Anónima Deportiva, se podría vulnerar la confidencialidad que impone el ordenamiento jurídico, al permitir la identificación indirecta de la deuda de algún club en concreto”.</p>
                    <p>Estas cifras se añaden a la <a href="http://www.nuevatribuna.es/articulo/economia/los-clubes-de-futbol-deben-a-la-seguridad-social-16-6-millones/20130607124403093151.html" target="_blank"><u>deuda de los clubes de fútbol con la Seguridad Social, que asciende a 16,6 millones</u></a>, según los datos del Fichero General de Recaudación de la Seguridad Social a fecha 13 de mayo de 2013.</p>
                    <p>Por otro lado, se indica que la <strong>deuda con la Agencia Tributaria de las SAD que está aplazada</strong> por acuerdo o convenio, a 30 de abril de 2013, asciende a <strong>347.346.010 euros</strong>, y que la deuda de las SAD que están <strong>en concurso es de 380.439.071</strong>.</p>
                    <p>Por último, se informa de que la deuda global con la Agencia Tributaria de todos los clubes y SAD de todos los deportes federados con excepción del fútbol –es decir, la deuda de los clubes de baloncesto, balonmano, clubes federados de tenis y de voleibol- a 30 de abril de 2013, asciende a 30.559.659 euros.</p>
                    <p><u>En respuesta a otra pregunta del portavoz socialista, el Gobierno confirma que la deuda de los clubes de fútbol y SAD a finales de 2012 estaba “en torno a los 700 millones de euros”.</u></p>
                    <p>El Gobierno revela también que <u>en 2011, obtuvieron aplazamiento de sus deudas con la Agencia Tributaria 17 clubes de fútbol de primera y segunda división,&nbsp; mientras que en 2012 fueron 15.</u></p>
                    <p>Asimismo, ofrecen la relación de los clubes y SAD que han presentado las cuentas anuales cerradas a 30 de junio de 2012,&nbsp; y la de aquellos que no han presentado los estados financieros intermedios cerrados a 31 de diciembre de 2012, correspondientes a la temporada 12/13, cuyo plazo finalizó el 31 de marzo.</p>
                    <p>Por otro lado, el Ejecutivo señala que, según lo acordado por la Liga de Fútbol Profesional y el Consejo Superior de Deportes, en la elaboración del presupuestos de los clubes, “se aplicará un límite de coste de plantilla, que variará en función de la diferencia entre los ingresos totales y los gastos no deportivos”, con el objetivo de conseguir “el equilibrio en los presupuestos de los clubes y SAD” y “que dichos presupuestos sean realistas”.</p>
                    <p><u><strong>El PSOE denuncia la “falta de transparencia” del Gobierno</strong></u></p>
                    <p><strong>El portavoz de deportes del PSOE, Manuel Pezzi</strong>, ha denunciado la “falta de transparencia” del Gobierno respecto a las deudas de los clubes de fútbol, después de que, en la comparecencia en el Congreso del secretario de Estado de Deporte el pasado 27 de junio, presentara un documento sobre la situación económica y financiera del fútbol español que se circunscribía a la época de gobierno socialista y eludía dar los datos de 2012 y 2013, que –como se puede ver- estaban disponibles.</p>
                    <p>Ante la “opacidad del Gobierno”, <strong>el Grupo Socialista ha solicitado la comparecencia del presidente de la Liga de Fútbol Profesional, Javier Tebas</strong>, para que informe sobre los presupuestos desagregados de la LFP en 2012 y 2013 y de todos los clubes y SAD que la forman, así como las deudas con Hacienda y la Seguridad Social en esos años.</p>
                    <p>Pezzi denuncia “el grave problema de endeudamiento que afecta al futbol en nuestro país” y ha reclamado al Gobierno que aplique “un control riguroso” y que se “exija a los clubes el pago de las deudas” del mismo modo que al resto de las empresas y de los ciudadanos. Ha insistido en la necesidad de garantizar el “juego limpio financiero, con clubes que cumplan las leyes y que paguen sus deudas con Hacienda y con la Seguridad Social”.</p>
                    <div class="browse-files hidden">Content for file browsing</div>
                    <div class="file-details hidden">Content for file details</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="buttons pull-right">
            <a class="btn btn-primary yes" href="#">{t}Exit{/t}</a>
        </div>
    </div>
</div>
{/block}


{block name="footer-js" append}
<script>
jQuery(document).ready(function($) {
    jQuery("#media-uploader").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: true,
    });
});
</script>
{/block}

{block name="header-css" append}
<style>
    .full-height {
        min-height:100%;height:100%;
    }
    #media-uploader {
        width:96% !important;
        height:90% !important;
        top:5%;
        left:2%;
        right:2%;
        margin-left:0;
    }
    #media-uploader .modal-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 13px 0;
        /*z-index: 150;*/
    }
    #media-uploader .modal-footer .buttons {
        margin-right:10px;
    }
    #media-uploader .modal-body {
        min-height:80%;height:80%;
        padding:0;
        overflow:hidden;
    }
    #media-uploader .modal-body > div{
        position:relative;
        height:100%;
        max-height:100%;
    }
    #media-uploader .modal-body-sidebar {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 199px;
        border-right: 1px solid #eee;
    }
    #media-uploader .modal-body-content {
        margin-left:200px;
        overflow:hidden;
        max-height:100%;
        height:100%;
        position:relative;
    }
    #media-uploader .modal-body-content-wrapper {
        overflow-x:hidden;
        overflow-y:visible;
        position:absolute;
        top:10px;
        bottom:10px;
        left:10px;
        right:0px;
    }
    #media-uploader .hidden {
        display:none;
    }
    #media-uploader .modal-body-sidebar ul {
        margin:20px 0 10px 20px;
    }
    #media-uploader .modal-body-sidebar li {
        list-style:none;
        margin-bottom:10px;
    }
    #media-uploader .modal-body-sidebar a {
        padding: 4px 10px;
        margin: 0;
        line-height: 18px;
        font-size: 14px;
        color: #21759b;
        text-shadow: 0 1px 0 #fff;
        text-decoration: none;
    }
</style>
{/block}