<?php 
ob_start ("ob_gzhandler");
header("Content-type: text/css");
header("Cache-Control: must-revalidate");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + (60*60)) . " GMT");
?>
.cabeceraActualidadVideos {
margin-left:24px;
width:252px;
height:42px;
float:left;
position:relative;
display:inline;
}

.zonaVisualizacionVideos {
margin-top:4px;
width:448px;
height:315px;
overflow:hidden;
float:left;
background-color:#e4ddc9;
}

.CZonaVisorVideos {
margin-left:12px;
margin-top:8px;
width:250px;
height:250px;
position:relative;
float:left;
display:inline;
background-color:#666;
}

.CZonaThumbsVideos {
margin-top:8px;
width:174px;
position:relative;
float:left;
display:inline;
}

.CThumbVideo {
margin-left:14px;
margin-bottom:6px;
width:72px;
height:72px;
position:relative;
float:left;
display:inline;
cursor:pointer;
}

div.CHolderThumbVideo {
display:table-cell;
width:72px;
height:72px;
text-align:center;
vertical-align:middle;
background:#000205;
overflow:hidden;
}

.CEdgeThumbVideo {
display:inline-block;
width:0;
height:100%;
vertical-align:middle;
}

.CContainerThumbVideo {
display:inline-block;
width:100%;
text-align:center;
vertical-align:middle;
}

.CContainerTituloVideo {
margin-left:14px;
position:relative;
float:left;
display:inline;
}

.linkMasMedia {
width:280px;
margin-left:14px;
margin-top:10px;
float:left;
position:relative;
display:inline;
font-size:10px;
}

.linkMasMedia a,.linkMasMedia a:hover {
color:#004B8E;
}

.cabeceraActualidadFotos {
margin-left:24px;
width:246px;
height:42px;
position:relative;
display:inline;
float:left;
}

.zonaVisualizacionFotos {
margin-top:4px;
width:300px;
height:315px;
float:left;
background-color:#e4ddc9;
overflow:hidden;
}

.CPiezaActualidadFotosHome {
margin-top:11px;
padding-bottom:15px;
width:300px;
display:inline;
float:left;
position:relative;
background-color:#E4DDC9;
}

.CContainerFotoActualidadFotos {
width:252px;
display:inline;
float:left;
overflow:hidden;
position:relative;
text-align:center;
cursor:pointer;
}

.zonaVisualizacionVideosNew {
overflow:hidden;
background-color:#d9e3ed;
float:left;
margin-top:4px;
width:295px;
margin-left:10px;
height:390px;
}

.ColumnaSepardorVideosFotosNew {
overflow:hidden;
display:inline;
float:left;
height:430px;
position:relative;
width:20px;
}

.zonaVisualizacionFotosNew {
overflow:hidden;
float:left;
margin-top:4px;
width:295px;
}

.CContainerTituloFotoNew {
position:relative;
float:left;
display:inline;
margin-left:4px;
overflow:hidden;
}

.CContainerFotoActualidadFotosNew {
width:250px;
height:250px;
display:inline;
float:left;
overflow:hidden;
position:relative;
text-align:center;
cursor:pointer;
margin-top:8px;
}

.CPiezaActualidadFotosHomeNew {
overflow:hidden;
background-color:#d9e3ed;
float:left;
margin-left:10px;
width:295px;
height:390px;
}

.CCuerpoPiezaFotoXornalNew {
overflow:hidden;
display:inline;
float:left;
margin-left:12px;
position:relative;
width:270px;
}

.CThumbFotoPrimeraNew {
overflow:hidden;
cursor:pointer;
display:inline;
float:left;
position:relative;
text-align:center;
width:75px;
height:72px;
margin-bottom:6px;
}

.CThumbFotoNew {
overflow:hidden;
cursor:pointer;
display:inline;
float:left;
position:relative;
text-align:center;
width:75px;
height:72px;
margin-left:14px;
margin-bottom:6px;
font-size:0;
}

.separadorVerticalNew {
overflow:hidden;
display:inline;
float:left;
height:430px;
position:relative;
width:35px;
}

.CZonaThumbsVideosNew,.CZonaThumbsFotosNew {
display:inline;
float:left;
margin-top:8px;
position:relative;
width:295px;
}.logoFotoVideoDia {
width:181px;
height:46px;
display:inline;
float:left;
position:relative;
}

.zonaVisualizacionFotoVideoDia {
width:181px;
height:116px;
display:inline;
float:left;
position:relative;
background-color:#eee;
}

.franjaAzulFotoVideoDia {
width:181px;
display:inline;
float:left;
position:relative;
background-image:url(../images/fotoVideoDia/bandaAzulInferior.gif);
}

.flechitaBlancaFotoVideoDia {
margin-left:5px;
margin-top:5px;
width:5px;
height:8px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/fotoVideoDia/flechitaBlanca.gif);
background-repeat:no-repeat;
}

.textoFranjaAzulFotoVideoDia {
margin-top:1px;
margin-left:5px;
width:154px;
display:inline;
float:left;
position:relative;
overflow:hidden;
font-family:Arial;
font-size:11px;
color:#fff;
}

.zonaPestanyasFotoVideoDia {
display:inline;
float:left;
position:relative;
font-family:Arial;
font-size:12px;
}

.pestanyaFotoDia {
width:88px;
height:30px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/fotoVideoDia/fondoPestanyaFoto.gif);
background-repeat:no-repeat;
background-color:#dee8f0;
}

.pestanyaVideoDia {
width:93px;
height:30px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/fotoVideoDia/fondoPestanyaVideo.gif);
background-repeat:no-repeat;
background-color:#dee8f0;
}

.textoPestanyaMediaDia {
margin-top:8px;
margin-left:8px;
display:inline;
float:left;
position:relative;
}

.pestanyaFotoDia a,.pestanyaVideoDia a {
text-decoration:none;
color:#fff;
}

.pestanyaFotoDia a:hover,.pestanyaVideoDia a:hover {
text-decoration:underline;
color:#fff;
}

.listaEnlacesFotoVideoDia {
width:171px;
margin-top:10px;
margin-left:10px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.enlaceFotoVideoDia {
width:171px;
display:inline;
float:left;
position:relative;
font-family:Arial;
font-size:14px;
color:#004B8E;
margin-top:2px;
}

.enlaceFotoVideoDia img {
margin-right:5px;
}

.fileteFotoVideoDia {
margin-top:5px;
width:161px;
height:1px;
overflow:hidden;
display:inline;
float:left;
position:relative;
background-image:url(../images/fotoVideoDia/fileteDashedDeportesXPress.gif);
}

.menuInferiorFotoVideoDia {
margin-top:10px;
width:161px;
display:inline;
float:left;
position:relative;
font-family:Arial;
font-size:10px;
color:#004B8E;
}

.posPaginador {
display:inline;
float:left;
margin-left:162px;
margin-top:15px;
position:relative;
width:572px;
}

.containerFotoVideoDiaMasListado,.containerFotoVideoDia {
width:181px;
display:inline;
float:left;
position:relative;
}

.enlaceFotoVideoDia a,.menuInferiorFotoVideoDia a,.menuInferiorFotoVideoDia a:hover {
color:#004B8E;
}.CContenedorPiezaGenteXornal {
float:left;
display:inline;
position:relative;
width:295px;
}

.CPiezaGenteXornalMasLinks {
margin-top:10px;
margin-left:20px;
float:left;
display:inline;
position:relative;
font-size:10px;
color:#004B8E;
}

.CPiezaGenteXornalMasLinks a,.CPiezaGenteXornalMasLinks a:hover {
color:#004B8E;
}

.CCabeceraPiezaGenteXornal {
margin-left:15px;
width:200px;
height:46px;
float:left;
display:inline;
position:relative;
overflow:hidden;
background-repeat:no-repeat;
}

.CCuerpoPiezaGenteXornal {
margin-left:22px;
width:252px;
float:left;
display:inline;
position:relative;
overflow:hidden;
}

.CCuerpoPiezaFotoXornal {
margin-left:22px;
width:270px;
float:left;
display:inline;
position:relative;
overflow:hidden;
}

.CContainerFotoPiezaGenteXornal {
background-color:#E4DDC9;
width:260px;
height:250px;
float:left;
display:inline;
position:relative;
overflow:hidden;
margin-top:10px;
}

.CBandaAzulPiezaGenteXornal {
width:252px;
height:14px;
float:left;
display:inline;
position:relative;
overflow:hidden;
background-image:url(../images/genteXornal/fondoBandaBajoFoto.gif);
background-repeat:no-repeat;
}

.CPieFotoPiezaFotoXornal {
margin-top:20px;
width:270px;
float:left;
display:inline;
position:relative;
overflow:hidden;
font-family:Arial;
font-size:12px;
color:#333;
}

.CPieFotoPiezaVideoXornal {
margin-top:15px;
width:434px;
float:left;
display:inline;
position:relative;
overflow:hidden;
font-family:Arial;
font-size:12px;
color:#333;
}

.CPieFotoPiezaGenteXornal {
margin-top:10px;
width:252px;
float:left;
display:inline;
position:relative;
overflow:hidden;
font-family:Arial;
font-size:12px;
color:#333;
}

.CFlechaGrisPieGenteXornal {
margin-top:2px;
width:10px;
height:9px;
float:left;
display:inline;
position:relative;
overflow:hidden;
background-image:url(../images/genteXornal/flechaPieGenteXornal.gif);
background-repeat:no-repeat;
}

.CFlechaBlancaFondoAzul {
margin-left:10px;
margin-top:2px;
width:10px;
height:8px;
float:left;
display:inline;
position:relative;
overflow:hidden;
background-image:url(../images/genteXornal/flechaBlancaFondoAzul.gif);
background-repeat:no-repeat;
}

.CTextoBandaAzulGenteXornal {
width:232px;
float:left;
display:inline;
position:relative;
overflow:hidden;
}

.CTextoBandaAzulGenteXornal a,.CTextoBandaAzulGenteXornal a:hover {
font-family:Arial;
font-size:10px;
color:#fff;
}

.CContainerFotoPiezaGenteXornalNew {
background-color:#E4DDC9;
display:inline;
float:left;
height:310px;
margin-top:10px;
overflow:hidden;
position:relative;
width:260px;
}

.CPieFotoPiezaVideoXornalNew,.CPieFotoPiezaFotoXornalNew {
color:#333;
display:inline;
float:left;
font-family:Arial;
font-size:12px;
margin-top:15px;
overflow:hidden;
position:relative;
width:270px;
height:30px;
}.containerLaBolsa {
float:left;
}

.cabeceraLaBolsa {
margin-left:10px;
display:inline;
float:left;
position:relative;
}

.cuerpoLaBolsa {
margin-top:7px;
float:left;
}

.cuerpoLaBolsa .miniFVPpal {
background:#FFF;
width:255px;
min-width:255px;
border:0;
margin:0 auto;
padding:0;
}

.cuerpoLaBolsa h1,.cuerpoLaBolsa h2 {
display:none;
}

.cuerpoLaBolsa .miniFV_logo a div {
background:url(../images/bolsa/MiniFicha_logo.gif) no-repeat 0 0;
cursor:hand;
height:37px;
width:142px;
margin:0 auto;
}

.cuerpoLaBolsa .miniFV_logo a:hover div {
background:url(../images/bolsa/MiniFicha_logoOver.gif) no-repeat 0 0;
}

.cuerpoLaBolsa .miniFV_valor {
background:url(../images/bolsa/MiniFicha_Fondo01.gif) repeat-x 0 0;
border:1px solid #cfcfcf;
height:17px;
width:auto;
margin:0 3px;
padding:4px 0 0 5px;
}

.cuerpoLaBolsa .miniFV_valor a {
color:#666;
font:bold 11px Arial,Tahoma,Verdana;
text-decoration:none;
}

.cuerpoLaBolsa .miniFV_valor a:hover {
color:#000;
}

.cuerpoLaBolsa .miniFV_datosPpal {
width:auto;
margin:9px 3px 5px;
}

.cuerpoLaBolsa .miniFV_datosPpal table {
width:100%;
margin:0;
}

.cuerpoLaBolsa .miniFV_tit {
color:#666;
font:bold 11px Arial,Verdana,Tahoma;
width:100%;
}

.cuerpoLaBolsa .miniFV_DatoDif_Suben {
color:#197d19;
font:bold 11px Arial,Verdana,Tahoma;
text-align:right;
}

.cuerpoLaBolsa .miniFV_DifFlecha_Suben div {
background:url(../images/bolsa/sprite04.gif) no-repeat 0 -32px;
font-size:1px;
height:6px;
width:11px;
}

.cuerpoLaBolsa .miniFV_DatoDif_Bajan {
color:#dc0000;
font:bold 11px Arial,Verdana,Tahoma;
text-align:right;
}

.cuerpoLaBolsa .miniFV_DifFlecha_Bajan {
width:100%;
}

.cuerpoLaBolsa .miniFV_DifFlecha_Bajan div {
text-align:right;
background:url(../images/bolsa/sprite04.gif) no-repeat 0 -38px;
font-size:1px;
height:6px;
width:11px;
margin:0 4px 0 auto;
}

.cuerpoLaBolsa .miniFV_DifFlecha_Mant div {
background:url(../images/bolsa/sprite04.gif) no-repeat 0 -44px;
font-size:1px;
height:9px;
width:6px;
}

.cuerpoLaBolsa .miniFV_graf {
text-align:center;
cursor:hand;
background:url(../images/bolsa/MiniFicha_Fondo02.gif) repeat-x 0 0;
border:1px solid #b0afaf;
width:250px;
margin:10px 1px;
}

.cuerpoLaBolsa .miniFV_masInfoPpal {
width:auto;
margin:3px 3px 5px;
}

.cuerpoLaBolsa .miniFV_masInfo a {
float:right;
background:url(../images/bolsa/MiniFicha_iconoMas.gif) no-repeat;
color:#666;
font:11px Arial,Verdana,Tahoma;
text-decoration:none;
padding:0 0 0 16px;
}

.cuerpoLaBolsa .miniFV_masInfo a:hover {
background:url(../images/bolsa/MiniFicha_iconoMasOver.gif) no-repeat 0 0;
color:#bfbfbf;
}

.cuerpoLaBolsa .miniFV_copy {
float:left;
color:#002872;
font:10px Arial,Verdana,Tahoma;
}

.cuerpoLaBolsa .miniFV_dato,.cuerpoLaBolsa .miniFV_DatoDif_Mant {
color:#666;
font:bold 11px Arial,Verdana,Tahoma;
text-align:right;
}

.cuerpoLaBolsa .miniFV_DifFlecha_Suben,.cuerpoLaBolsa .miniFV_DifFlecha_Mant {
text-align:right;
padding:0 0 0 50px;
}.containerVerano {
width:295px;
float:left;
display:inline;
position:relative;
background-color:#fff;
}

.containerNoticiasXPress {
margin-left:8px;
width:280px;
float:left;
display:inline;
position:relative;
background-color:#fff;
}

.cabeceraNoticiasXPress {
width:280px;
height:42px;
float:left;
background-image:url(../images/noticiasXPress/logoNoticiasXPress.gif);
background-repeat:no-repeat;
}

.cabeceraHumorGrafico {
width:280px;
height:42px;
float:left;
background-image:url(../images/noticiasXPress/logoHumorGrafico.jpg);
background-repeat:no-repeat;
}

.listaNoticiasXPress {
width:280px;
float:left;
display:inline;
position:relative;
}

.noticiaXPress {
margin-top:8px;
width:280px;
float:left;
display:inline;
position:relative;
}

.contHoraNoticiaXPress {
width:35px;
height:35px;
float:left;
display:inline;
position:relative;
}

.horaNoticiaXPress {
width:35px;
height:18px;
float:left;
display:inline;
position:relative;
font-family:Arial;
font-size:12px;
color:#004B8E;
}

.iconoRayoXPress {
margin-left:8px;
width:17px;
height:17px;
float:left;
display:inline;
position:relative;
background-image:url(../images/noticiasXPress/iconoRayoPeque.gif);
}

.contTextoFilete {
width:236px;
float:left;
display:inline;
position:relative;
}

.textoNoticiaXPress {
width:230px;
margin-left:5px;
margin-right:7px;
float:left;
display:inline;
position:relative;
font-family:Arial;
font-size:12px;
color:#333;
}

.textoNoticiaXPress a {
color:#333;
}

.fileteNoticiaXPress {
margin-top:10px;
width:240px;
height:1px;
float:left;
display:inline;
position:relative;
}

.linkMasNoticiasXPress {
width:151px;
margin-top:4px;
margin-left:32px;
float:left;
display:inline;
position:relative;
font-size:10px;
}

.linkMasNoticiasXPress a {
color:#004B8E;
}.logoTitulares {
width:290px;
height:52px;
float:left;
background-image:url(../images/titularesDia/logoTitularesDia.gif);
}

.grupoTitularesDia {
width:315px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#fff;
}

.listaTitularesDia {
float:left;
position:relative;
display:inline;
overflow:hidden;
margin:5px 0 5px 5px;
}

.contTitularDia {
margin-top:5px;
margin-bottom:5px;
width:310px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.contHoraLugarTitularDia {
width:310px;
font-family:Georgia,Times New Roman,Times,serif;
color:#004B8E;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.horaTitularDia {
padding-top:1px;
font-size:10px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.lugarTitularDia {
margin-left:5px;
padding-left:5px;
font-size:12px;
float:left;
position:relative;
display:inline;
overflow:hidden;
border-left:1px solid #004B8E;
}

.textoTitularDia {
width:280px;
overflow:hidden;
font-size:14px;
color:#666;
}

.textoTitularDia a,.textoTitularDia a:hover {
color:#666;
}

.linkMasNoticiasTitulares {
width:180px;
margin-top:10px;
margin-left:14px;
border-bottom:15px solid #FFF;
float:left;
position:relative;
display:inline;
overflow:hidden;
font-size:10px;
}

.contPestanyaTitularesDia {
width:314px;
height:26px;
float:left;
overflow:hidden;
vertical-align:top;
background-image:url(../images/titularesDia/contPestanyaTitulares.gif);
}

.pestanyaTitulares {
padding-left:5px;
padding-right:5px;
padding-top:8px;
float:left;
height:26px;
overflow:hidden;
vertical-align:top;
background-image:url(../images/titularesDia/pestanyaTitulares.gif);
font-family:Arial;
font-size:14px;
text-transform:uppercase;
font-weight:700;
color:#004B8E;
}

.flechaPestanyaTitulares {
margin-top:4px;
margin-right:5px;
width:5px;
height:8px;
background-image:url(../images/titularesDia/flechitaAzulTitulares.gif);
display:inline;
float:left;
overflow:hidden;
vertical-align:top;
}

.cierrePestanyaTitulares {
background-image:url(../images/titularesDia/cierrePestanyaTitulares.gif);
float:left;
display:inline;
height:26px;
width:3px;
overflow:hidden;
vertical-align:top;
}

.contOtrosEnlacesTitulares {
margin-left:10px;
margin-top:3px;
float:left;
font-family:Arial;
font-size:10px;
color:#004B8E;
text-transform:uppercase;
}

.enlaceTitular {
margin-left:18px;
float:left;
}

.pictoEnlaceTitular {
float:left;
}

.textoEnlaceTitular {
margin-left:8px;
margin-top:14px;
float:left;
}

.CPiezaGenteXornal {
width:295px;
height:315px;
background-color:#E4DDC9;
display:inline;
float:left;
position:relative;
}

.CPiezaGenteXornalNew {
background-color:#D9E3ED;
display:inline;
float:left;
height:390px;
position:relative;
width:295px;
}

.linkMasNoticiasTitulares a,.linkMasNoticiasTitulares a:hover,.textoEnlaceTitular a,.textoEnlaceTitular a:hover {
color:#004B8E;
}.CUltimaHora {
margin-left:20px;
width:934px;
height:30px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-color:#fff;
}

.CIconoUltimaHora {
width:30px;
height:32px;
display:inline;
float:left;
position:relative;
background-image:url(../images/ultimaHora/iconoUltimaHora.gif);
background-repeat:no-repeat;
}

.CZonaTextoInicial {
margin-left:10px;
width:150px;
height:30px;
display:inline;
float:left;
position:relative;
background-color:#cd071e;
overflow:hidden;
}

.CTextoInicialUltimaHora {
margin-top:3px;
margin-left:6px;
width:138px;
height:30px;
display:inline;
float:left;
position:relative;
overflow:hidden;
font-size:22px;
color:#fff;
font-weight:700;
text-align:center;
}

.CZonaTitularUltimaHora {
width:743px;
height:30px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-color:#ccc;
}

.CTextoTitularUltimaHora {
margin-top:8px;
margin-left:6px;
height:30px;
display:inline;
float:left;
position:relative;
overflow:hidden;
font-size:16px;
color:#333;
font-weight:700;
}