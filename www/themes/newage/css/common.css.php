<?php 
ob_start ("ob_gzhandler");
header("Content-type: text/css");
header("Cache-Control: must-revalidate");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + (60*60)) . " GMT");
?>
.contBannerYTextoPublicidad {
margin-top:-10px;
width:300px;
float:left;
}

.textoBannerPublicidad {
font-family:Arial;
font-size:9px;
float:left;
}

.contBannerPublicidad {
margin-top:12px;
width:300px;
text-align:left;
overflow:hidden;
}

.contBannerYTextoPublicidadCol3 {
margin-top:-8px;
display:inline;
float:left;
position:relative;
}

.contBannerPublicidadCol3 {
margin-top:8px;
text-align:center;
overflow:hidden;
display:inline;
float:left;
position:relative;
}

.CContainerPublicidadCol1 {
width:572px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}* {
margin:0;
padding:0;
}

a {
color:#000;
text-decoration:none;
}

a:hover {
color:#000;
text-decoration:underline;
}

a img {
border:0;
}

body {
font-family:Arial,Helvetica,sans-serif;
font-size:12px;
color:#000;
background-color:#f0f0f0;
}

html > body {
margin:0;
}

h2 {
margin-bottom:5px;
font-size:2em;
font-weight:400;
clear:left;
font-family:Georgia,Times New Roman,Times,serif;
width:410px;
}

.global_metacontainer {
margin-left:auto;
margin-right:auto;
width:996px;
position:relative;
text-align:left;
}

.header {
width:976px;
float:left;
background-color:#fff;
line-height:20px;
}

.logoXornalYBanner {
width:976px;
height:70px;
overflow:hidden;
float:left;
background:#e4e4e4 url(../images/fondo-header.png) bottom right no-repeat;
}

.logoXornal {
margin-left:30px;
float:left;
padding-top:15px;
text-align:center;
}

.zonaBannerYMenuInferior {
height:70px;
float:right;
background-color:#024687;
}

.bannerInt {
width:634px;
height:94px;
float:left;
background-color:#999;
}

.menuInferiorInt {
margin-top:0;
width:634px;
height:18px;
float:left;
}

.separadorHorizontalInt {
margin-top:6px;
margin-bottom:0;
width:634px;
height:1px;
float:left;
overflow:hidden;
background-color:#b3b3b3;
}

.zonaSecciones {
width:976px;
height:23px;
float:left;
}

.zonaHoraBusqueda {
height:25px;
float:left;
background-color:#e4ddc9;
color:#666;
font-size:12px;
margin-top:-2px;
padding-top:5px;
width:976px;
}

#rss {
vertical-align:bottom;
margin-bottom:2px;
}

.zonaHoraFecha {
padding-left:12px;
float:left;
display:inline;
position:relative;
}

.zonaBusqueda {
margin-top:8px;
width:976px;
height:21px;
float:left;
background-color:#999;
}

.zonaBusquedaBarraHora {
width:450px;
height:21px;
float:right;
display:inline;
position:relative;
font-weight:700;
}

.separadorElemMenuBarraFecha {
margin-top:3px;
margin-left:8px;
margin-right:8px;
width:1px;
height:15px;
float:left;
display:inline;
position:relative;
background-color:#666;
}

.cajaBusqueda {
margin-left:10px;
width:170px;
height:16px;
float:left;
display:inline;
position:relative;
}

.destinoBusqueda {
height:15px;
float:left;
display:inline;
position:relative;
vertical-align:top;
line-height:15px;
margin:4px 0 0 20px;
padding:0;
}

.radioBusqueda input {
height:13px;
float:left;
display:inline;
position:relative;
margin:0;
padding:0;
}

.dondeBuscar {
margin-left:3px;
float:left;
display:inline;
position:relative;
padding:0;
}

.textoABuscar {
width:170px;
height:15px;
border:0;
overflow:hidden;
}

.container {
width:976px;
display:inline;
float:left;
position:relative;
background-color:#fff;
}

.containerNoticias {
margin-top:12px;
width:976px;
display:inline;
float:left;
position:relative;
background:url(../images/medianil.gif) repeat-y scroll 700px 0 transparent;
}

.containerNoticiasFrontpages {
margin-top:12px;
width:976px;
display:inline;
float:left;
position:relative;
background-color:#fff;
background-image:url(fileteFo.gif);
}

.ultimaHora {
margin-top:10px;
width:976px;
height:30px;
float:left;
}

.footer {
width:976px;
float:left;
background-color:#fff;
border-bottom:10px solid #FFF;
line-height:20px;
}

.zonaSeccionesFooter {
width:976px;
height:26px;
background-color:#999;
}

.column12 {
width:695px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.column12big {
width:962px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.containerCol12 {
width:695px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background:url(../images/medianil.gif) repeat-y scroll 430px 0 transparent;
}

.containerCol123 {
width:962px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(filetesF.gif);
}

.column1big {
margin-left:7px;
width:635px;
display:inline;
float:left;
position:relative;
overflow:hidden;
padding-right:7px;
border-right:1px solid silver;
padding-top:14px;
}

.column2big {
margin-left:5px;
width:300px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.column1 {
width:420px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.column2 {
margin-left:20px;
width:250px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.column3 {
margin-left:20px;
width:255px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.noticiaEspecialHome {
margin-left:7px;
margin-bottom:10px;
width:755px;
display:inline;
float:left;
position:relative;
}

.zonaPestanyas {
margin-top:8px;
width:976px;
height:21px;
float:left;
background-color:#999;
line-height:20px;
}

.separadorBanners70pxwidth {
width:976px;
height:70px;
float:left;
display:inline;
position:relative;
overflow:hidden;
background-color:#fff;
}

.banner610x70 {
width:610px;
height:70px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-color:#999;
}

.banner295x295 {
width:295px;
height:295px;
margin-top:8px;
overflow:hidden;
text-align:center;
display:inline;
float:left;
}

.banner486x60 {
width:486px;
height:60px;
margin-top:16px;
overflow:hidden;
text-align:center;
display:inline;
float:left;
}

.banner340x70 {
margin-left:7px;
width:340px;
height:70px;
display:inline;
float:right;
position:relative;
overflow:hidden;
}

.containerActualVideosYDeportes {
width:976px;
background-color:#fff;
background-image:url(../images/fileteFondoActualidad.gif);
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.containerActualidad {
margin-left:7px;
width:755px;
height:389px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-color:#fff;
}

.actualidadVideos {
width:448px;
height:389px;
display:inline;
float:left;
position:relative;
overflow:hidden;
margin-right:12px;
}

.actualidadFotos {
width:245px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.containerActualVideosYDeportesNew {
background-color:#FFF;
background-image:url(../images/fileteFondoActualidadNew.gif);
display:inline;
float:left;
overflow:hidden;
position:relative;
width:976px;
}

.containerActualidadNew {
overflow:hidden;
background-color:#fff;
display:inline;
float:left;
margin-left:7px;
position:relative;
width:620px;
}

.deportesExpress {
margin-left:25px;
width:181px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.titularesDia {
width:976px;
float:left;
}

.cabeceraTitulares {
margin-left:7px;
width:962px;
height:61px;
float:left;
}

.contenedorColTitulares {
margin-top:12px;
width:976px;
float:left;
background-image:url(../images/filetesFondoTitulares.gif);
}

.col1Titulares {
margin-left:7px;
width:304px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.col2Titulares {
margin-left:25px;
width:310px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.col3Titulares {
margin-left:25px;
width:304px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.separadorHorizontal {
margin-top:12px;
margin-bottom:12px;
margin-left:5px;
width:976px;
height:1px;
float:left;
overflow:hidden;
background-color:#b3b3b3;
}

.containerColumnas12Noticia {
margin-top:12px;
width:976px;
display:inline;
float:left;
position:relative;
background-color:#fff;
background-image:url(../images/fileteFondoNota.gif);
}

.column1Noticia {
margin-left:7px;
width:642px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.column2Noticia {
margin-left:25px;
width:295px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.CNoticiaRelacionada a,.CNoticiaRelacionada a:hover,.CSigue a,.CSigue a:hover {
color:#004b8e;
}

ul.breadcrub {
list-style:none;
border:0;
margin:0;
}

ul.breadcrub li {
float:left;
color:#024687;
padding:4px;
}

ul.breadcrub li a {
color:#024687;
text-decoration:none;
font-weight:400;
}

.txt_desc_extras {
color:#707070;
font-family:arial;
font-size:11px;
margin-left:4px;
}

item_related {
margin-bottom:15px;
}

.opinion_icon {
background:url(../images/noticia/opinion.gif) no-repeat 0 0;
padding-left:20px;
margin-top:2px;
}

.image_icon {
background:url(../images/noticia/galery.gif) no-repeat 0 0;
padding-left:20px;
margin-top:2px;
}

.file_icon {
background:url(../images/noticia/doc.gif) no-repeat 0 0;
padding-left:20px;
margin-top:2px;
}

.video_icon {
background:url(../images/noticia/video.gif) no-repeat 0 0;
padding-left:20px;
margin-top:2px;
}

.loading_container {
width:100%;
height:100%;
background:url(../images/carousel/loading.gif) no-repeat 50% 50%;
display:block;
min-height:55px;
}

* html .loading_container {
height:55px;
}

.portada {
text-align:center;
float:left;
margin:5px;
}

.portada a {
display:block;
font-size:12px;
border:1px dashed #e5e5e5;
text-decoration:none;
padding:5px;
}

.portada a:hover {
background-color:#e5e5e5;
font-size:12px;
border:1px solid #e5e5e5;
}

#kiosko_menu div a {
display:block;
margin-top:10px;
margin-bottom:10px;
margin-left:5px;
border-bottom:1px dashed #e5e5e5;
vertical-align:middle;
text-decoration:none;
font-size:12px;
padding:5px;
}

#kiosko_menu div a:hover {
background-color:#e5e5e5;
border-bottom:1px solid #e5e5e5;
font-size:12px;
}

div#formAuthConecta {
background-color:#EEE;
line-height:normal;
width:300px;
height:190px;
padding:1em;
}

div#formAuthConecta h1 {
font-size:1.2em;
font-weight:700;
color:#004C8E;
margin:1em 0;
}

div#formAuthConecta dt {
width:150px;
color:#004C8E;
font-weight:700;
}

div#formAuthConecta dd {
width:270px;
float:left;
margin-bottom:.5em;
}

div#formAuthConecta div#auth_message {
color:#933;
font-weight:700;
}

div.profile {
margin-top:25px;
background-color:#fff;
border:1px solid #ccc;
padding:.4em;
}

div.profile div.profile-pic {
float:left;
background-color:#EEE;
border-right:1px solid #CCC;
border-bottom:1px solid #CCC;
padding:2px;
}

div.profile div.profile-info {
color:#666;
font-size:.9em;
margin-left:.5em;
float:left;
}

div.profile div.profile-info a {
font-size:1.1em;
font-weight:700;
color:#004B8E;
}

.rightSide {
text-align:right;
}

div.profile .rightSide a {
font-weight:700;
color:#004B8E;
}

.clear,.clearer {
clear:both;
}

.marco_metacontainer,.metacontainer {
width:976px;
display:inline;
float:left;
position:relative;
background-color:#FFF;
}

.elemMenuBarraFecha,.containerBusqueda,.radioBusqueda {
float:left;
display:inline;
position:relative;
}

.elemMenuBarraFecha a,.CContenedorParticipacion a,.CContenedorParticipacion a:hover {
color:#666;
}

.footer a,div.profile .rightSide a:hover {
text-decoration:none;
}

.footer a:hover,ul.breadcrub li a:hover {
text-decoration:underline;
}

.separadorBannersTOP,.separadorBanners {
width:976px;
height:90px;
float:left;
display:inline;
position:relative;
overflow:hidden;
background-color:#fff;
}

.bannerTOP728x90,.banner728x90 {
width:728px;
height:90px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-color:#999;
}

.bannerTOP234x90,.banner234x90 {
margin-left:7px;
width:234px;
height:90px;
display:inline;
float:right;
position:relative;
overflow:hidden;
}

.actualidadVideosNew,.actualidadFotosNew {
overflow:hidden;
display:inline;
float:left;
position:relative;
width:295px;
}

a.no_underline,a.no_underline:hover {
color:#004B8E;
text-decoration:none;
}

.item_icon,.article_icon {
background:url(../images/noticia/nnrr.gif) no-repeat 0 0;
padding-left:20px;
margin-top:2px;
}

a.related_link,.related_link a:hover {
color:#004B8E;
}

::-moz-selection,::selection {
background:#c00;
color:#fff;
}

code::-moz-selection,code::selection {
background:#333;
}.cuerpoVisorVideos {
margin-top:10px;
width:434px;
height:399px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-color:#e4ddc9;
}

.contVisorVideo {
margin-left:33px;
margin-top:16px;
width:370px;
height:268px;
background-color:#333;
overflow:hidden;
}

.contFlechaTextoGaleria {
margin-top:10px;
margin-left:36px;
width:360px;
height:100px;
display:inline;
float:left;
position:relative;
overflow:hidden;
text-align:left;
}

.textoVideoGaleria {
margin-left:5px;
width:348px;
display:inline;
float:left;
position:relative;
overflow:hidden;
font-family:Arial;
font-size:24px;
color:#333;
}

.flechaVideoGaleria {
margin-top:8px;
width:5px;
height:8px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.contNuestraSeleccion {
width:302px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.listaMediaSeleccionada {
margin-left:15px;
margin-top:23px;
width:269px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.elementoMediaSelec {
margin-top:6px;
width:266px;
height:75px;
position:relative;
overflow:hidden;
}

.fotoElemMedia {
width:75px;
height:75px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#7fcaee;
}

.contTextoElemMedia {
width:190px;
height:75px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-image:url(../images/galeriaVideos/backgroundElemento.gif);
background-repeat:no-repeat;
font-family:Arial;
font-size:14px;
font-weight:700;
}

.textoElemMedia {
margin-top:10px;
margin-left:10px;
width:170px;
height:55px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.fondoContainerActualidad {
background-image:none;
}

.iconoPlay {
left:0;
top:0;
width:75px;
height:75px;
position:absolute;
background-image:url(../images/gifs_trans/iconoPlay.png);
background-repeat:no-repeat;
}

.listadoMedia {
width:760px;
margin-top:10px;
margin-left:72px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.elementoListadoMediaPag {
margin-top:6px;
width:686px;
height:96px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.fotoElemMediaListado {
margin-left:6px;
width:78px;
height:94px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.contSeccionFechaListado {
margin-left:14px;
width:500px;
float:left;
position:relative;
display:inline;
overflow:hidden;
font-family:Arial;
font-size:14px;
font-weight:700;
color:#004b8d;
}

.seccionMediaListado {
height:20px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.fechaMediaListado {
margin-left:10px;
padding-left:10px;
float:left;
position:relative;
display:inline;
overflow:hidden;
border-left:1px solid #004b8d;
font-weight:500;
}

.contTextoElemMediaListado {
width:590px;
height:70px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-image:url(../images/galeriaVideos/backgroundElementoLista.gif);
background-repeat:no-repeat;
}

.textoElemMediaListado {
margin-top:16px;
margin-left:10px;
width:570px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.fileteIntraMedia {
width:750px;
height:2px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-image:url(../images/galeriaVideos/fileteIntraMedia.gif);
background-repeat:no-repeat;
}

.posPaginadorGaliciaTitulares {
width:572px;
margin-left:162px;
margin-top:15px;
float:left;
display:inline;
position:relative;
}

.contUnicaPestanyaGrande {
height:32px;
width:295px;
float:left;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/contPestanyasList.gif);
}

.zonaPestanyasMedia {
width:760px;
margin-top:22px;
float:left;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/contPestanyasList.gif);
}

.zonaVisorVideos,.cabeceraVisorVideos {
display:inline;
float:left;
position:relative;
}.column123 {
width:976px;
display:inline;
float:left;
position:relative;
}

.zonaPestanyasMedia3Cols {
margin-top:15px;
margin-left:6px;
width:960px;
display:inline;
float:left;
position:relative;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/contPestanyasList.gif);
}

.CContainerPestanyasActualidadFotos {
margin-left:212px;
margin-top:30px;
width:442px;
display:inline;
float:left;
position:relative;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/contPestanyasList.gif);
}

.agrupaColumnas {
margin-top:15px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.fondoActualidadVideo {
background-image:url(../images/fileteFondoActualidad.gif);
}

.cuerpoVisorFotos {
margin-left:10px;
margin-top:10px;
width:954px;
height:400px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-color:#e4ddc9;
}

.contVisorFoto {
margin-left:33px;
margin-top:16px;
width:498px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.CVisorRealFoto {
width:498px;
height:340px;
display:inline;
float:left;
position:relative;
background-color:#333;
overflow:hidden;
}

.CBandaAzulVisorFoto {
width:498px;
height:23px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/galeriaFotos/fondoBandaAzul.gif);
}

.CVerFotosVisorFotosBandaAzul {
margin-left:10px;
margin-top:6px;
width:330px;
display:inline;
float:left;
position:relative;
font-size:10px;
}

.CVerFotosVisorFotosBandaAzul a,.CVerFotosVisorFotosBandaAzul a:hover {
color:#fff;
}

.marcoInfoFoto {
margin-left:30px;
margin-top:16px;
width:366px;
height:363px;
display:inline;
float:left;
position:relative;
background-color:#86847f;
}

.contInfoFoto {
margin-top:1px;
margin-left:1px;
width:364px;
height:361px;
display:inline;
float:left;
position:relative;
background-color:#e4ddc9;
}

.posInfoFoto {
margin-top:20px;
margin-left:10px;
width:348px;
display:inline;
float:left;
position:relative;
}

.CTitularVisorFotos {
margin-left:20px;
width:330px;
display:inline;
float:left;
position:relative;
font-size:24px;
color:#333;
font-weight:700;
}

.CTextoVisorFotos {
margin-left:20px;
margin-top:20px;
width:330px;
display:inline;
float:left;
position:relative;
font-size:16px;
}

.CClickParaVerFotosVisorFotos {
margin-left:20px;
margin-top:18px;
width:330px;
display:inline;
float:left;
position:relative;
font-size:10px;
}

.containerActualidadFoto {
margin-top:12px;
width:976px;
display:inline;
float:left;
position:relative;
background-color:#fff;
}

.agenciaInfoFoto {
left:10px;
top:343px;
position:absolute;
display:inline;
color:#333;
font-size:12px;
}

.zonaVisorFotos,.cabeceraVisorFotos {
display:inline;
float:left;
position:relative;
}

.CVerFotosVisorFotosBandaAzul img,.CClickParaVerFotosVisorFotos img,.agenciaInfoFoto img {
margin-right:5px;
}.CNoticiaHome1 {
margin-left:10px;
width:415px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#fff;
}

.CNoticiaHome1big {
margin-left:10px;
width:625px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#fff;
}

.antetitulo {
width:415px;
font-size:12px;
color:#394756;
text-transform:uppercase;
font-weight:700;
}

.firma {
margin-bottom:10px;
width:100%;
float:left;
font-size:11px;
color:gray;
}

.CNoticiaHome1-contenedorTexto {
margin-bottom:5px;
float:left;
display:inline;
position:relative;
clear:both;
width:415px;
}

.CNoticiaHome1_foto {
width:416px;
height:150px;
float:left;
overflow:hidden;
background-color:#eee;
}

.CNoticiaHome1_video {
width:416px;
height:150px;
float:left;
position:relative;
overflow:hidden;
background-color:#eee;
}

.ColumnHome2 {
width:245px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#fff;
}

.ColumnHome2Especial {
width:300px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#E4DDC9;
padding-bottom:10px;
}

.noticiaEspecial {
width:290px;
margin:5px;
}

.ColumnHome2-preHeader {
width:285px;
font-size:12px;
color:#004b8e;
text-transform:uppercase;
}

.ColumnHome2-author {
margin-bottom:10px;
width:100%;
float:left;
font-size:11px;
color:#004b8e;
}

.ColumnHome2_related_news {
margin-bottom:5px;
float:left;
display:inline;
position:relative;
margin-top:10px;
width:245px;
}

.ColumnHome2_participation {
margin-top:5px;
width:285px;
float:left;
position:relative;
display:inline;
font-size:10px;
color:#666;
}

.ColumnHome2_photo {
float:left;
overflow:hidden;
margin-bottom:5px;
margin-right:5px;
}

.ColumnHome2_video {
width:285px;
height:150px;
float:left;
position:relative;
overflow:hidden;
background-color:#eee;
}

.CCabeceraVideo {
left:0;
top:0;
width:34px;
height:150px;
position:absolute;
background:url(../images/home_noticias/video.gif) no-repeat;
}

.creditos {
width:415px;
font-size:10px;
text-align:right;
}

.CSigue {
font-size:10px;
color:#004b8e;
}

.CNoticiaRelacionada {
font-size:12px;
color:#004b8e;
}

.CContenedorParticipacion {
width:415px;
float:left;
position:relative;
display:inline;
font-size:10px;
color:#666;
}

.CComentarios {
float:left;
background:url(../images/noticia/comments.png) no-repeat scroll 0 0 transparent;
padding-left:25px;
height:20px;
}

.CVotos {
float:right;
text-align:right;
}

.contenedorFoto {
margin-right:5px;
margin-bottom:5px;
float:left;
display:inline;
position:relative;
}

.contenedorTexto {
margin-bottom:5px;
float:left;
display:inline;
position:relative;
}

.creditos2 {
clear:left;
font-size:10px;
text-align:left;
}

.CContenedorNoticiasRelacionadas2 {
margin-top:10px;
width:210px;
float:left;
}

.CContenedorNoticiasRelacionadasDestacada {
margin-top:10px;
width:500px;
float:left;
}

.CNoticiaDestacada {
margin-left:10px;
padding-left:30px;
padding-right:20px;
width:698px;
float:left;
position:relative;
display:inline;
background-color:#e4ddc9;
}

.CNoticiaDestacada h2 {
font-size:40px;
line-height:45px;
margin-top:5px;
clear:none;
}

.CContenedorFotoNoticiaDestacada {
margin-left:-30px;
margin-right:30px;
float:left;
position:relative;
display:inline;
padding:10px;
}

.CContenedorTextoNoticiaDestacada {
width:100%;
float:left;
position:relative;
display:inline;
overflow:hidden;
margin:10px;
}

.contenedorTextoNoticiaDestacada {
margin-left:25px;
margin-top:-5px;
width:420px;
float:left;
position:relative;
display:inline;
}

.contenedorTextoNoticiaDestacada h2 {
font-size:40px;
line-height:45px;
}

.contenedorTextoNoticiaDestacada h2 a,.contenedorTextoNoticiaDestacada h2 a:hover {
color:#333;
}

.CContenedorParticipacionNotaDestacada {
margin-left:25px;
}

.CComentariosNotaDestacada,.CComentariosNotaDestacada a,.CComentariosNotaDestacada a:hover {
margin-top:10px;
color:#333;
}

.CListaTagsNotaDestacada {
margin-left:10px;
margin-top:8px;
float:left;
position:relative;
display:inline;
}

.CTagNotaDestacada {
margin-left:15px;
float:left;
position:relative;
display:inline;
text-transform:uppercase;
color:#666;
font-weight:700;
}

.CTagNotaDestacada a,.CTagNotaDestacada a:hover {
color:#666;
}

.CMarcoNotaEspHomeVideoCol2 {
margin-left:6px;
padding-bottom:1px;
width:284px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#999;
}

.CNotaEspHomeVideoCol2 {
margin-left:1px;
margin-top:1px;
padding-bottom:10px;
width:282px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#fff;
}

.CContainerInfoNotaEspHomeVideo {
margin-top:10px;
margin-left:9px;
width:270px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.CContainerInfoNotaEspHomeVideo .firma {
width:270px;
float:left;
position:relative;
display:inline;
overflow:hidden;
font-size:10px;
}

.CNotaEspHomeVideoCol2 h2 {
font-size:16px;
}

.CTextoNotaEspHomeVideo {
font-size:12px;
}

.CNotaEsp_video {
width:282px;
height:150px;
float:left;
position:relative;
overflow:hidden;
background-color:#eee;
}

.CCabeceraSuperior {
left:0;
top:0;
width:282px;
height:19px;
position:relative;
background:url(../images/home_noticias/fondoCabeceraEspecial.gif) no-repeat;
}

.CTextoCabeceraSuperior {
margin-top:4px;
width:282px;
height:19px;
position:relative;
display:inline;
float:left;
text-align:center;
text-transform:uppercase;
color:#fff;
font-size:12px;
background:url(../images/home_noticias/fondoCabeceraEspecial.gif) no-repeat;
}

.CTextoCabeceraSuperior img {
margin-left:5px;
margin-right:5px;
}

.CNotaEsp_foto {
width:282px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#eee;
}

.CContainerEsp_foto {
position:relative;
display:inline;
overflow:hidden;
}

.CTitularNotaEsp {
width:270px;
float:left;
position:relative;
display:inline;
overflow:hidden;
font-size:16px;
font-weight:700;
}

.CComentariosNotaPiezaEsp {
width:150px;
position:relative;
display:inline;
float:left;
}

.CContenedorParticipacionPiezaEsp {
margin-top:10px;
width:265px;
float:left;
position:relative;
display:inline;
font-size:10px;
color:#666;
}

.CContendorSuplementos {
color:#004B8E;
display:inline;
float:left;
font-family:Arial;
font-size:14px;
margin-top:5px;
position:relative;
width:180px;
}

.firma_nombre,.ColumnHome2-author_name {
float:left;
font-weight:700;
}

.firma_destacado,.ColumnHome2-author_destacado {
margin-bottom:30px;
font-size:11px;
color:#004b8e;
}

.firma_diario,.firma_fecha,.firma_hora,.ColumnHome2-author_daily,.ColumnHome2-author_date,.ColumnHome2-author_hour {
float:left;
}

.separadorFirma,.ColumnHome2-authorSeparator {
margin-left:5px;
margin-right:5px;
width:1px;
height:14px;
float:left;
background-color:#004b8e;
}

.CNoticiaHome1_foto2,.ColumnHome2_photo2 {
float:left;
display:inline;
position:relative;
background-color:#eee;
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
}.menuCabeceraTexto {
width:976px;
height:27px;
position:relative;
display:inline;
float:left;
overflow:hidden;
}

.CSeparadorMenu {
margin-top:3px;
width:1px;
height:14px;
position:relative;
display:inline;
float:left;
overflow:hidden;
background-color:#004b8e;
}

p {
margin-top:6px;
margin-bottom:6px;
}

.menuCabeceraSecundario {
width:462px;
height:16px;
position:relative;
display:inline;
float:right;
}

.menuCabeceraSecundario ul {
list-style-type:none;
height:25px;
position:relative;
float:left;
}

.menuCabeceraSecundario ul li {
height:18px;
margin-left:14px;
position:relative;
float:left;
display:inline;
font-size:12px;
overflow:hidden;
}

.CSeparadorMenuFlecha {
margin-top:5px;
margin-right:3px;
width:4px;
height:10px;
position:relative;
float:left;
display:inline;
background-image:url(../images/flechitaMenu.gif);
background-repeat:no-repeat;
}

.menuCabeceraSecundario ul li a {
text-decoration:none;
color:gray;
}

.menuCabeceraSecundario ul li a:hover {
text-decoration:underline;
color:gray;
}.menuCabeceraTexto {
width:976px;
height:22px;
position:relative;
display:inline;
float:left;
overflow:hidden;
font-family:Arial;
color:#004b8e;
background:url(../images/menu/bgr_long_hornav.gif) repeat-x scroll left top transparent;
}

.opcion {
position:relative;
display:inline;
float:left;
font-family:Arial;
font-size:11px;
color:#004b8e;
font-weight:700;
}

.opcion a {
color:#004b8e;
text-decoration:none;
}

.menuCabeceraTexto ul li.menuselec a {
font-family:Arial;
font-size:12px;
font-weight:700;
text-decoration:none;
color:#FFF;
}

.CSeparadorMenu {
margin-top:3px;
width:1px;
height:14px;
position:relative;
display:inline;
float:left;
overflow:hidden;
}

.menuCabeceraTexto ul {
list-style-type:none;
height:24px;
position:relative;
float:left;
}

.menuCabeceraTexto ul li {
margin-left:0;
height:23px;
position:relative;
float:left;
display:inline;
overflow:hidden;
}

.menuCabeceraTexto ul li a {
padding-top:1px;
padding-left:13px;
padding-right:13px;
position:relative;
display:inline;
float:left;
color:#fff;
}

.menuCabeceraTexto ul li.menuselec {
position:relative;
display:inline;
float:left;
color:#fff;
background-color:#405D75;
}

p {
margin-top:6px;
margin-bottom:6px;
}

.menuCabeceraSecundario {
width:370px;
height:16px;
position:relative;
display:inline;
float:right;
}

.menuCabeceraSecundario ul {
list-style-type:none;
height:25px;
position:relative;
float:left;
}

.menuCabeceraSecundario ul li {
height:18px;
margin-left:10px;
position:relative;
float:left;
display:inline;
font-size:12px;
overflow:hidden;
}

.CSeparadorMenuFlecha {
margin-top:5px;
margin-right:3px;
width:4px;
height:10px;
position:relative;
float:left;
display:inline;
background-image:url(../images/fotoVideoDia/flechitaAzul.gif);
background-repeat:no-repeat;
}

.menuCabeceraSecundario ul li a {
font-weight:700;
text-decoration:none;
color:#004B8E;
}

.menuCabeceraSecundario ul li a:hover {
text-decoration:underline;
color:gray;
}

.zonaBusquedaBarraHoraSec {
width:412px;
}

.zonaHoraBusquedaSec {
background-color:#405D75;
color:#fff;
font-weight:700;
}

.elemMenuBarraFechaSec a,.elemMenuBarraFechaSec a:hover {
color:#fff;
}

.separadorElemMenuBarraFechaSec {
background-color:#bfd2e3;
}.CNoticia {
width:642px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#fff;
}

.CNoticiaMargen {
float:left;
position:relative;
display:inline;
overflow:hidden;
width:640px;
}

.CNoticia h1 {
font-size:30px;
margin-bottom:5px;
font-weight:700;
clear:left;
}

.CNoticia h2 {
font-size:30px;
}

.apertura_nota {
margin-bottom:10px;
width:572px;
float:left;
}

.antetitulo_nota {
float:left;
font-size:12px;
color:#004b8e;
text-transform:uppercase;
}

.firma_nota {
margin-top:1px;
float:left;
display:inline;
position:relative;
font-size:11px;
color:#004b8e;
}

.subtitulo_nota {
font-size:17px;
font:13pt arial;
}

.cuerpo_article {
font-size:15px;
}

.CNoticiaContenedorFoto {
margin-top:10px;
float:left;
position:relative;
clear:both;
width:285px;
}

.CNoticia_foto {
float:left;
display:inline;
position:relative;
overflow:hidden;
background-color:#eee;
}

.CCabeceraVideo {
left:6px;
top:0;
width:34px;
height:226px;
position:absolute;
background:url(../images/noticia/video.gif) no-repeat;
}

.CCabeceraFotogaleria {
left:6px;
top:0;
width:34px;
height:226px;
position:absolute;
background:url(../images/noticia/fotogaleria.gif) no-repeat;
}

.creditos_nota {
font-size:10px;
text-align:left;
}

.CContenedorMenuNota {
margin-top:10px;
margin-bottom:10px;
width:100%;
float:left;
}

.menu_nota {
float:left;
font-size:11px;
color:#004b8e;
}

.CVotos_nota {
float:right;
-x-system-font:none;
color:#004B8E;
font-family:arial;
font-size:8pt;
font-style:normal;
font-variant:normal;
font-weight:400;
line-height:normal;
margin:2px 0 0 5px;
padding:1px 0 0 20px;
}

.separadorVotos {
margin-left:5px;
margin-right:5px;
width:1px;
height:14px;
float:right;
background-color:#004b8e;
}

.CContenedorPaginado {
margin-top:5px;
width:572px;
float:left;
font-size:10px;
color:#666;
}

.CPaginas {
margin-left:10px;
float:left;
}

.COpina {
margin-top:30px;
padding-bottom:30px;
width:100%;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#E4DDC9;
}

.CContenedorOpina {
margin-top:20px;
margin-left:40px;
width:502px;
}

.CCabeceraOpina {
width:182px;
height:23px;
background:url(../images/noticia/cabeceraOpina.gif) no-repeat;
}

.CContenedorComentarios {
width:560px;
float:left;
display:block;
}

.CComentario {
margin-top:10px;
float:left;
display:block;
}

.CNumeroComentario {
margin-left:5px;
width:35px;
position:relative;
display:inline;
float:left;
color:#004b8e;
font-size:12px;
}

.CInfoComentario {
width:500px;
position:relative;
display:inline;
float:left;
overflow:hidden;
}

.CTitularComentario {
width:440px;
float:left;
font-family:Arial;
font-size:12px;
color:#000;
font-weight:700;
}

.CDatosComentario {
margin-top:3px;
width:440px;
float:left;
display:block;
font-family:Arial;
font-size:11px;
color:#004b8d;
}

.CNombreComentarista {
position:relative;
display:inline;
float:left;
}

.CTextoComentario {
padding-top:10px;
padding-bottom:10px;
width:490px;
float:left;
display:block;
font-family:Arial;
font-size:12px;
color:#000;
}

.CComentar {
margin-top:10px;
display:inline;
float:left;
width:530px;
}

.CColumna1Comentar {
width:280px;
float:left;
display:inline;
position:relative;
}

.CColumna2Comentar {
width:245px;
float:left;
display:inline;
position:relative;
overflow:hidden;
}

.CTextoComentar {
margin-bottom:3px;
display:block;
font-family:Arial;
font-size:11px;
color:#004b8e;
font-weight:700;
}

.CTextoCompartir {
float:left;
font-size:11px;
font-weight:700;
}

.share_right {
float:right;
}

.CImagenKaptcha {
margin-top:10px;
margin-bottom:10px;
margin-left:30px;
float:left;
display:inline;
position:relative;
}

.CContainerTextAreaYTexto {
width:257px;
display:block;
}

.CContainerZonaTextAreaComentario {
width:257px;
position:relative;
display:inline;
float:left;
overflow:hidden;
}

.CMarcoZonaTextAreaComentario {
width:242px;
height:152px;
float:left;
background-color:#000;
}

.CZonaTextAreaComentario {
margin-top:1px;
margin-left:1px;
width:240px;
height:150px;
float:left;
display:inline;
position:relative;
background-color:#fff;
}

.CTextoInfoKaptcha {
color:#333;
display:inline;
float:left;
font-family:Arial;
font-size:9px;
line-height:12px;
position:relative;
}

.CTextoEnviar {
margin-top:10px;
padding-right:1em;
font-size:1.4em;
font-weight:700;
color:#004b8d;
}

.CTextoEnviar a {
color:#004b8d;
text-decoration:none;
}

.CTextoEnviar a:hover {
color:#004b8d;
text-decoration:underline;
}

.CContainerZonaTextoNormasComent {
margin-left:15px;
margin-top:18px;
float:left;
display:inline;
position:relative;
}

.CCabeceraTextoNormasComent {
margin-top:6px;
width:228px;
float:left;
display:inline;
position:relative;
font-family:Arial;
font-size:12px;
color:#333;
}

.CRegistroOInicioSesion {
width:228px;
float:left;
display:inline;
position:relative;
color:#004b8e;
}

.CRegistroOInicioSesion a {
text-decoration:none;
color:#004b8e;
font-weight:700;
}

.CRegistroOInicioSesion a:hover {
text-decoration:underline;
color:#004b8e;
font-weight:700;
}

.CTextoNormasComent {
float:left;
display:inline;
position:relative;
font-family:Arial;
font-size:11px;
color:#333;
line-height:12px;
}

.textareaComentario {
width:240px;
height:149px;
border:0;
overflow:auto;
}

.separadorHorizontalNoticia {
background-color:#B3B3B3;
float:left;
display:inline;
position:relative;
height:1px;
margin-bottom:12px;
overflow:hidden;
width:257px;
}

.CContenedorDatoComentarista {
margin-top:10px;
width:267px;
float:left;
display:inline;
position:relative;
}

.CContainerDato {
width:242px;
height:22px;
background-color:#000;
float:left;
display:inline;
position:relative;
}

.CContainerDato input {
margin-top:1px;
margin-left:1px;
width:240px;
height:20px;
float:left;
display:inline;
position:relative;
border:0;
}

.CContenedorComentario {
float:left;
display:inline;
position:relative;
}

.CComent_Votos_nota {
float:left;
-x-system-font:none;
color:#004B8E;
font-family:arial;
font-size:8pt;
font-style:normal;
font-variant:normal;
font-weight:400;
line-height:normal;
margin:2px 0 0 5px;
padding:1px 0 0 20px;
}

.CHeaderArticle .superior {
border-bottom:1px solid #004B8E;
padding:0 5px 2px;
}

.CHeaderArticle .superior .authority {
font:8pt arial;
color:#004B8E;
float:left;
}

.CHeaderArticle .superior .share {
float:right;
margin-top:5px;
font:bold 8pt arial;
color:#004B8E;
}

.CHeaderArticle .superior .share a img {
width:16px;
margin:-5px 0 -3px 5px;
}

.CHeaderArticle .superior .author {
font-weight:700;
}

.CHeaderArticle .superior .author:after {
font:12pt arial;
content:' | ';
}

.CHeaderArticle .inferior .tools .comments {
background:url(../images/noticia/comment.gif) no-repeat;
font:8pt arial;
color:#004B8E;
position:absolute;
margin:2px 0 0 5px;
padding:1px 0 0 20px;
}

.CHeaderArticle .inferior .tools .icons {
margin-left:100px;
}

.CHeaderArticle .inferior .tools .icons a img {
color:#fff;
border:none;
background:#fff;
margin:1px 0 0 5px;
}

.CFooterArticle {
border-bottom:1px solid #004B8E;
margin:20px 0 50px;
padding:0 5px;
}

.CContenedorEnviarA {
float:left;
position:relative;
display:inline;
font-size:10px;
}

.CCabeceraEnviarA {
width:295px;
float:left;
position:relative;
display:inline;
color:#004b8e;
text-transform:uppercase;
font-weight:700;
}

.CCuerpoEnviarA {
width:295px;
float:left;
position:relative;
display:inline;
}

.CDestinoEnviarA {
margin-top:5px;
width:72px;
height:16px;
float:left;
position:relative;
display:inline;
}

.CLogoDestinoEnviarA {
float:left;
position:relative;
display:inline;
}

.CTextoDestinoEnviarA {
margin-top:2px;
margin-left:2px;
float:left;
position:relative;
display:inline;
}

.CTextoNotaEnviarA {
margin-top:8px;
float:left;
position:relative;
display:inline;
font-size:9px;
}

.CDestacado {
display:block;
color:#004b8e;
font-weight:700;
}

.CDestacado img {
margin-right:3px;
}

.CNewsDateUpdate {
white-space:nowrap;
}

.ampliar_foto {
width:35px;
height:35px;
background:red;
position:absolute;
margin:-40px 0 0 255px;
}

.CRelated {
width:190px;
border:1px solid #bbb;
float:left;
font:8pt arial;
background:#f1f1f1;
margin:0 10px 10px;
padding:10px;
}

.CRelated .item {
padding-left:20px;
margin-bottom:15px;
background:url(../images/noticia/nnrr.gif) no-repeat -3px 5px;
}

.CRelated .article {
background:url(../images/noticia/nnrr.gif) no-repeat 0 5px;
}

.CRelated .opinion {
background:url(../images/noticia/opinion.gif) no-repeat 0 5px;
}

.CRelated .image {
background:url(../images/noticia/galery.gif) no-repeat 0 5px;
}

.CRelated .file {
background:url(../images/noticia/doc.gif) no-repeat 0 5px;
}

.CRelated .item .headline {
color:#004B8E;
font-weight:700;
}

.CRelated .item .section:after {
font:11pt arial;
}

.cuerpo_article a,.cuerpo_article a:hover {
font-weight:400;
}

.menu_nota_enviar,.menu_nota_imprimir,.menu_nota_correccion,.numpagina_nota,.link_mas_nota,.CHeaderArticle .inferior .tools,.CFooterArticle .share {
float:left;
}

.CLugarComentario,.CFechaComentario,.CHoraComentario {
margin-left:10px;
padding-left:10px;
border-left:1px solid #004b8d;
position:relative;
display:inline;
float:left;
}

.CHeaderArticle,.barra_superior {
margin:10px 0;
}

.CHeaderArticle .inferior .votes,.CFooterArticle .votos_pie_noticia {
float:right;
font:8pt arial;
color:#888;
margin-right:5px;
}

.CHeaderArticle .inferior .votes .vote,.CHeaderArticle .inferior .votes .points,.CFooterArticle .votos_pie_noticia .votar,.CFooterArticle .votos_pie_noticia .puntuacion {
display:inline;
}

.CHeaderArticle .inferior .votes .vote:after,.CFooterArticle .votos_pie_noticia .votar:after {
font:12pt arial;
content:' | ';
margin:0 10px;
}.CContainerRelacionadas {
margin-top:15px;
margin-left:15px;
float:left;
display:inline;
position:relative;
border:1px solid #000;
padding:4px;
}

.CCabeceraRelacionadas {
width:500px;
float:left;
display:inline;
position:relative;
font-family:Arial;
font-size:18px;
color:#000;
font-weight:700;
}

.CListaRelacionadas {
margin-top:5px;
float:left;
display:inline;
position:relative;
}

.CRelacionadas {
margin-top:5px;
float:left;
display:inline;
position:relative;
overflow:hidden;
}

.CContainerIconoTextoRelacionadas {
float:left;
display:inline;
position:relative;
}

.textoRelacionadas {
margin-top:10px;
margin-left:5px;
margin-right:7px;
float:left;
display:inline;
position:relative;
font-family:Arial;
font-size:12px;
color:#333;
}

.fileteRelacionadas {
background-color:#B3B3B3;
width:600px;
height:1px;
float:left;
display:inline;
position:relative;
overflow:hidden;
}

.CContainerRecomendaciones {
margin-left:15px;
float:left;
display:inline;
position:relative;
}

.CCabeceraRecomendaciones {
width:280px;
float:left;
display:inline;
position:relative;
font-family:Arial;
font-size:18px;
color:#000;
font-weight:700;
}

.CListaRecomendaciones {
margin-top:5px;
width:280px;
float:left;
display:inline;
position:relative;
}

.CRecomendacion {
margin-top:5px;
width:269px;
float:left;
display:inline;
position:relative;
overflow:hidden;
}

.CContainerIconoTextoRecomendacion {
padding-bottom:10px;
float:left;
display:inline;
position:relative;
background-image:url(../images/noticiasRecomendadas/backgroundRecomendacion.gif);
}

.iconoRecomendacion {
margin-top:13px;
margin-left:8px;
width:5px;
height:18px;
float:left;
display:inline;
position:relative;
background-image:url(../images/noticiasRecomendadas/iconoRecomendacion.gif);
background-repeat:no-repeat;
}

.textoRecomendacion {
margin-top:10px;
margin-left:5px;
margin-right:7px;
width:230px;
float:left;
display:inline;
position:relative;
font-family:Arial;
font-size:12px;
color:#333;
}

.fileteRecomendacion {
margin-top:5px;
margin-left:18px;
width:240px;
height:1px;
float:left;
display:inline;
position:relative;
}

.textoRelacionadas a,.textoRelacionadas a:hover,.textoRecomendacion a,.textoRecomendacion a:hover {
color:#333;
font-weight:400;
}.containerOpinion {
width:300px;
float:left;
background-color:#fff;
}

#cabeceraOpinion {
width:266px;
height:43px;
float:left;
background-image:url(../images/opinion/logoOpinion.gif);
}

#cabeceraOpinion2 {
width:180px;
height:30px;
float:left;
background-image:url(../images/opinion/logoOpinion180.png);
}

.listaPiezasOpinion {
width:300px;
margin-top:0;
float:left;
}

.parPiezasOpinion {
margin-top:10px;
float:left;
}

.piezaOpinion {
width:140px;
height:115px;
float:left;
}

.piezaOpinionPrimeraFila {
width:140px;
height:150px;
float:left;
overflow:hidden;
}

.cabeceraPiezaOpinion {
width:135px;
float:left;
font-family:Arial;
font-size:14px;
color:#004B8E;
}

.cuerpoPiezaOpinion {
width:127px;
height:70px;
float:left;
background-image:url(../images/opinion/fondoFraseOpinion.gif);
background-repeat:no-repeat;
}

.cuerpoPiezaOpinionPrimera {
width:127px;
height:100px;
float:left;
background-image:url(../images/opinion/fondoFraseOpinionGrande.gif);
background-repeat:no-repeat;
}

.cuerpoPiezaOpinionEspecial {
width:130px;
height:102px;
float:left;
background-image:url(../images/opinion/imagenPrimeraPiezaOpinion.gif);
background-repeat:no-repeat;
}

.fotoPiezaOpinion {
width:135px;
height:70px;
float:left;
}

.textoPiezaOpinionPrimera {
margin-left:5px;
margin-right:5px;
margin-top:20px;
width:130px;
height:102px;
float:left;
font-family:Arial;
font-size:12px;
color:#000;
}

.textoPiezaOpinionPrimera img {
margin-right:4px;
}

.textoPiezaOpinion {
margin-top:3px;
margin-right:5px;
width:130px;
height:38px;
float:left;
font-family:Arial;
font-size:12px;
color:#000;
}

.textoPiezaOpinion img {
margin-right:3px;
}

.separadorVerticalOpinion {
margin-right:5px;
width:1px;
height:115px;
float:left;
overflow:hidden;
background-image:url(../images/opinion/separadorVerticalOpinion.gif);
}

.separadorVerticalDirectorOpinion {
margin-right:5px;
width:1px;
height:150px;
float:left;
overflow:hidden;
background-image:url(../images/opinion/separadorVerticalOpinion.gif);
}

.separadorHorizontalOpinion {
margin-top:5px;
margin-bottom:5px;
width:280px;
height:2px;
overflow:hidden;
float:left;
display:inline;
position:relative;
background-image:url(../images/opinion/separadorHorizontalOpinion.gif);
}

.cuerpoPiezaOpinionEconomia {
margin-top:10px;
margin-left:5px;
width:170px;
float:left;
background-color:#FFF;
}

.fotoPiezaEconomia {
width:170px;
height:150px;
float:left;
}

.textoPiezaOpinionEconomia {
margin-top:3px;
margin-right:10px;
width:170px;
height:38px;
float:left;
font-family:Arial;
font-size:12px;
color:#000;
}.CContenedorPaginado {
margin-top:10px;
margin-bottom:5px;
margin-left:50px;
float:left;
font-size:11px;
color:#666;
}

.CPaginas {
margin-left:10px;
float:left;
}

.separador_pag_paginador {
margin-left:5px;
margin-right:5px;
width:1px;
height:14px;
overflow:hidden;
float:left;
background-color:#004b8e;
}

.numpagina_paginador a,.numpagina_paginador a:hover {
color:#004b8e;
}

.link_paginador a,.link_paginador a:hover {
color:#666;
}

.link_paginador,.numpagina_paginador {
float:left;
}.zonaPestanyas {
background-image:url(../images/pestanyas/contPestanyas.gif);
}

.espacioInterPestanyas {
float:left;
display:inline;
height:20px;
width:10px;
}

.pestanyaSelecList {
height:32px;
display:inline;
float:left;
position:relative;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/pestanyaSelecList.gif);
background-repeat:repeat-x;
font-family:Arial;
font-size:12px;
text-transform:uppercase;
font-weight:700;
color:#004B8E;
}

.pestanyaNoSelecList {
display:inline;
float:left;
position:relative;
height:32px;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/pestanyaNoSelecList.gif);
font-family:Arial;
font-size:12px;
text-transform:uppercase;
font-weight:700;
color:#666;
}

.contInfoPestanyaGrande {
margin-left:5px;
margin-top:10px;
display:inline;
float:left;
position:relative;
}

.flechaPestanyaSelecList {
margin-top:4px;
margin-right:5px;
width:5px;
height:8px;
background-image:url(../images/pestanyas/pestanyasListados/flechaPestanyaOnList.gif);
display:inline;
float:left;
overflow:hidden;
}

.flechaPestanyaNoSelecList {
margin-top:4px;
margin-right:5px;
width:5px;
height:8px;
position:relative;
display:inline;
float:left;
background-image:url(../images/pestanyas/pestanyasListados/flechaPestanyaOffList.gif);
overflow:hidden;
vertical-align:top;
}

.textoPestanyaSelecList,.textoPestanyaNoSelecList {
padding-right:10px;
display:inline;
float:left;
position:relative;
}

.cierrePestanyaSelecList {
width:3px;
height:32px;
float:left;
display:inline;
position:relative;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/cierrePestanyaSelecList.gif);
}

.cierrePestanyaNoSelecList {
background-image:url(../images/pestanyas/pestanyasListados/cierrePestanyaNoSelecList.gif);
float:left;
display:inline;
height:32px;
width:3px;
overflow:hidden;
vertical-align:top;
}

.espacioInterPestanyasGrande {
float:left;
display:inline;
height:20px;
width:26px;
}

.pestanyaOFF .textoPestanya {
display:inline;
float:left;
position:relative;
cursor:pointer;
}

.pestanya,.pestanyaON,.pestanyaOFF {
height:20px;
float:left;
overflow:hidden;
vertical-align:top;
font-family:Arial;
font-size:12px;
}

.flechaPestanyaOn,.pestanyaON .flechaPestanya {
margin-top:6px;
margin-right:5px;
width:5px;
height:8px;
display:inline;
float:left;
position:relative;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/flechaPestanyaOn.gif);
}

.flechaPestanyaOff,.pestanyaOFF .flechaPestanya {
margin-top:6px;
margin-right:5px;
width:5px;
height:8px;
display:inline;
float:left;
position:relative;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/flechaPestanyaOff.gif);
}

.pestanyaSelect,.pestanyaON .pestanya {
padding-left:5px;
padding-right:5px;
height:20px;
float:left;
display:inline;
position:relative;
overflow:hidden;
vertical-align:top;
color:#333;
background-image:url(../images/pestanyas/pestanyaSelec.gif);
}

.pestanyaNoSelect,.pestanyaOFF .pestanya {
padding-left:5px;
padding-right:5px;
height:20px;
float:left;
position:relative;
display:inline;
overflow:hidden;
vertical-align:top;
color:#666;
background-image:url(../images/pestanyas/pestanyaNoSelec.gif);
}

.cierrePestanyaSelect,.pestanyaON .cierrePestanya {
width:3px;
height:20px;
float:left;
display:inline;
position:relative;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/cierrePestanyaSelec.gif);
}

.cierrePestanyaNoSelect,.pestanyaOFF .cierrePestanya {
width:3px;
height:20px;
float:left;
display:inline;
position:relative;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/cierrePestanyaNoSelec.gif);
}

.textoPestanyaSelec,.textoPestanyaNoSelec,.pestanyaON .textoPestanya {
display:inline;
float:left;
position:relative;
}

.textoPestanyaNoSelec,.textoPestanyaNoSelecList {
cursor:pointer;
}.CContainerZonaLinksFooter {
float:left;
position:relative;
display:inline;
}

.CLinksXornalGalileo {
width:340px;
float:left;
position:relative;
display:inline;
font-size:12px;
}

.CLinkXornal {
float:left;
position:relative;
display:inline;
font-weight:700;
margin-left:6px;
color:#004b8e;
}

.CSeparadorLinkXornal {
margin-left:10px;
margin-right:10px;
margin-top:2px;
width:1px;
height:16px;
overflow:hidden;
float:left;
position:relative;
display:inline;
background-color:#004b8e;
}

.CLinkXornal a,.CLinkXornal a:hover {
color:#004b8e;
}

.zonaSeccionesPie {
margin-top:0;
}.zonaEncuestasXornal {
width:760px;
display:inline;
float:left;
position:relative;
background-color:#FFF;
}

.containerZonaLogoEncuesta {
margin-left:30px;
margin-top:10px;
width:710px;
display:inline;
float:left;
position:relative;
}

.CContenedorResultEncuesta {
margin-top:8px;
width:160px;
float:left;
display:inline;
position:relative;
}

.containerLogoEncuestas {
width:273px;
height:70px;
display:inline;
float:left;
position:relative;
background-image:url(../images/encuestas/logoEncuestasXornal.gif);
background-repeat:no-repeat;
}

.containerEncuestaXornal {
margin-left:60px;
width:340px;
display:inline;
float:left;
position:relative;
}

.zonaPreguntaEncuesta {
width:340px;
display:inline;
float:left;
position:relative;
color:#004B8D;
font-family:Arial;
font-size:14px;
font-weight:700;
}

.CContainerGraficoBarrasPeque {
margin-top:10px;
margin-bottom:20px;
width:165px;
height:176px;
display:inline;
float:left;
position:relative;
background-image:url(../images/encuestas/backgroundEncuestaDegradado.gif);
background-repeat:no-repeat;
}

.CSeparadorCeroPeque {
left:10px;
bottom:30px;
width:150px;
height:1px;
background-color:#FFF;
overflow:hidden;
position:absolute;
}

.CSeparadorCincuentaPeque {
left:10px;
bottom:90px;
width:150px;
height:1px;
background-color:#FFF;
overflow:hidden;
position:absolute;
}

.CSeparadorCienPeque {
left:10px;
bottom:150px;
width:150px;
height:1px;
background-color:#FFF;
overflow:hidden;
position:absolute;
}

.CPercentCeroPeque {
left:6px;
bottom:33px;
color:#666;
font-size:9px;
position:absolute;
font-weight:700;
}

.CPercentCincuentaPeque {
left:6px;
bottom:92px;
color:#666;
font-size:9px;
position:absolute;
font-weight:700;
}

.CPercentCienPeque {
left:6px;
bottom:152px;
color:#666;
font-size:9px;
position:absolute;
font-weight:700;
}

.CContenedorBarra1Peque {
bottom:0;
left:35px;
width:20px;
position:absolute;
}

.CContenedorBarra2Peque {
bottom:0;
left:60px;
width:20px;
position:absolute;
}

.CContenedorBarra3Peque {
bottom:0;
left:85px;
width:30px;
position:absolute;
background-color:green;
}

.CContenedorBarra4Peque {
bottom:0;
left:110px;
width:30px;
position:absolute;
background-color:green;
}

.CContenedorBarra5Peque {
bottom:0;
left:135px;
width:30px;
position:absolute;
background-color:green;
}

.CBarra1Peque {
left:0;
bottom:30px;
width:30px;
height:96px;
position:absolute;
}

.CFondoBarra1Peque {
left:0;
top:-1px;
width:15px;
height:96px;
background-color:#0095dd;
border:1px solid #D1CCBD;
position:absolute;
overflow:hidden;
}

.CBarra2Peque {
left:0;
bottom:30px;
width:30px;
height:60px;
position:absolute;
}

.CFondoBarra2Peque {
left:0;
top:-1px;
width:15px;
height:60px;
background-color:#004b8d;
border:1px solid #D1CCBD;
position:absolute;
overflow:hidden;
}

.CBarra3Peque {
left:0;
bottom:30px;
width:30px;
height:120px;
position:absolute;
}

.CFondoBarra3Peque {
left:0;
top:-1px;
width:15px;
height:120px;
border:1px solid #D1CCBD;
background-color:#000;
position:absolute;
overflow:hidden;
}

.CBarra4Peque {
left:0;
bottom:30px;
width:30px;
height:20px;
position:absolute;
}

.CFondoBarra4Peque {
left:0;
top:-1px;
width:15px;
height:20px;
border:1px solid #D1CCBD;
background-color:#000;
position:absolute;
overflow:hidden;
}

.CBarra5Peque {
left:0;
bottom:30px;
width:30px;
height:80px;
position:absolute;
}

.CFondoBarra5Peque {
left:0;
top:-1px;
width:15px;
height:80px;
border:1px solid #D1CCBD;
background-color:#000;
position:absolute;
overflow:hidden;
}

.CPercentPeque {
margin-top:-18px;
margin-left:-5px;
width:25px;
display:inline;
float:left;
color:#666;
font-family:Arial;
font-size:12px;
font-weight:700;
position:relative;
text-align:center;
}

.CRespuestaEncPeque {
left:-5px;
bottom:-3px;
width:30px;
height:30px;
position:absolute;
color:#666;
font-family:Arial;
font-size:14px;
font-weight:700;
text-align:center;
}

.COtrosInfoMediaEncuestaPequeSB {
color:#004B8D;
display:inline;
float:left;
font-family:Arial;
font-size:14px;
margin-left:10px;
overflow:hidden;
padding-left:10px;
position:relative;
}

.COtrosInfoMediaEncuestaPeque {
margin-left:10px;
padding-left:10px;
border-left:1px solid #004B8D;
color:#004B8D;
display:inline;
float:left;
font-family:Arial;
font-size:14px;
overflow:hidden;
position:relative;
}

.COtrosInfoMediaEncuestaPeque a,.COtrosInfoMediaEncuestaPeque a:hover {
color:#004B8D;
}

.fileteHorizontalPC {
width:734px;
height:1px;
margin-top:10px;
display:inline;
float:left;
overflow:hidden;
position:relative;
background-image:url(../images/planConecta/portada/fileteHorizontalPC.gif);
background-repeat:no-repeat;
}.cuerpoVisorVideos {
margin-top:10px;
width:434px;
height:399px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-color:#e4ddc9;
}

.contVisorVideo {
background-color:#333;
overflow:hidden;
}

.contFlechaTextoGaleria {
margin-top:10px;
margin-left:36px;
width:360px;
height:100px;
display:inline;
float:left;
position:relative;
overflow:hidden;
text-align:left;
}

.textoVideoGaleria {
margin-left:5px;
width:348px;
display:inline;
float:left;
position:relative;
overflow:hidden;
font-family:Arial;
font-size:24px;
color:#333;
}

.flechaVideoGaleria {
margin-top:8px;
width:5px;
height:8px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.contNuestraSeleccion {
width:302px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.listaMediaSeleccionada {
margin-left:15px;
margin-top:23px;
width:269px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.elementoMediaSelec {
margin-top:6px;
width:266px;
height:75px;
position:relative;
overflow:hidden;
}

.fotoElemMedia {
width:75px;
height:88px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#7fcaee;
font-size:0;
}

.contTextoElemMedia {
width:190px;
height:75px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-image:url(../images/galeriaVideos/backgroundElemento.gif);
background-repeat:no-repeat;
font-family:Arial;
font-size:14px;
font-weight:700;
}

.textoElemMedia {
margin-top:10px;
margin-left:10px;
width:170px;
height:55px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.fondoContainerActualidad {
background-image:none;
}

.iconoPlay {
left:0;
top:0;
width:75px;
height:75px;
position:absolute;
background-image:url(../images/gifs_trans/iconoPlay.png);
background-repeat:no-repeat;
}

.listadoMedia {
width:760px;
margin-top:10px;
margin-left:72px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.elementoListadoMediaPag {
margin-top:6px;
width:686px;
height:96px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.fotoElemMediaListado {
margin-left:6px;
width:78px;
height:94px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-color:#7fcaee;
font-size:0;
}

.contSeccionFechaListado {
margin-left:14px;
width:550px;
float:left;
position:relative;
display:inline;
overflow:hidden;
font-family:Arial;
font-size:14px;
font-weight:700;
color:#004b8d;
}

.seccionMediaListado {
height:20px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.fechaMediaListado {
margin-left:10px;
padding-left:10px;
float:left;
position:relative;
display:inline;
overflow:hidden;
border-left:1px solid #004b8d;
font-weight:500;
}

.contTextoElemMediaListado {
width:590px;
height:70px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-image:url(../images/galeriaVideos/backgroundElementoLista.gif);
background-repeat:no-repeat;
}

.textoElemMediaListado {
margin-top:16px;
margin-left:10px;
width:570px;
float:left;
position:relative;
display:inline;
overflow:hidden;
}

.fileteIntraMedia {
width:750px;
height:2px;
float:left;
position:relative;
display:inline;
overflow:hidden;
background-image:url(../images/galeriaVideos/fileteIntraMedia.gif);
background-repeat:no-repeat;
}

.posPaginadorGaliciaTitulares {
width:572px;
margin-left:162px;
margin-top:15px;
float:left;
display:inline;
position:relative;
}

.contUnicaPestanyaGrande {
height:32px;
width:295px;
float:left;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/contPestanyasList.gif);
}

.zonaPestanyasMedia {
width:760px;
margin-top:22px;
float:left;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/contPestanyasList.gif);
}

.CContainerLogoPortadaConecta {
width:200px;
height:58px;
position:relative;
display:inline;
float:left;
overflow:hidden;
vertical-align:top;
background-image:url(../images/planConecta/portada/logoPlanConectaPortada.gif);
background-repeat:no-repeat;
}

.CContainerPestanyasConectaPC {
margin-left:6px;
width:550px;
float:left;
display:inline;
position:relative;
overflow:hidden;
vertical-align:top;
background-image:url(../images/pestanyas/pestanyasListados/contPestanyasList.gif);
}

.CContainerLogoConectaYPestanyasPortadaPC {
float:left;
height:62px;
width:760px;
}

.espacioInterPestanyasGrande_Conecta {
float:left;
display:inline;
height:20px;
width:20px;
}

.filaPortadaPC {
width:752px;
height:100px;
margin-top:23px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.elementoListadoMediaPagPortadaPC {
width:363px;
height:100px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.fotoElemOpinion {
width:100px;
height:100px;
overflow:hidden;
position:relative;
display:inline;
float:right;
}

.contSeccionFechaListadoPortadaPC {
margin-top:5px;
margin-left:14px;
width:198px;
overflow:hidden;
position:relative;
display:inline;
float:left;
color:#004B8D;
font-family:Arial;
font-size:14px;
font-weight:700;
}

.contSeccionFechaListadoOpinion {
margin-top:5px;
margin-left:14px;
width:220px;
overflow:hidden;
position:relative;
display:inline;
float:left;
color:#004B8D;
font-family:Arial;
font-size:14px;
font-weight:700;
}

.contSeccionFechaListado2PortadaPC {
margin-top:5px;
margin-left:5px;
width:300px;
overflow:hidden;
position:relative;
display:inline;
float:left;
color:#004B8D;
font-family:Arial;
font-size:14px;
font-weight:700;
}

.contTextoElemMediaListadoPortadaPC {
width:212px;
height:65px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/galeriaVideos/backgroundElementoLista.gif);
background-repeat:no-repeat;
margin-left:15px;
}

.contTextoElemMediaListadoOpinion {
width:230px;
height:65px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/galeriaVideos/backgroundElementoLista.gif);
background-repeat:no-repeat;
margin-left:15px;
}

.contTextoElemMediaListado2PortadaPC {
width:352px;
height:69px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/planConecta/portada/backgroundElemXornal.gif);
background-repeat:no-repeat;
}

.flechitaTextoPC {
margin-top:5px;
margin-right:3px;
width:5px;
height:8px;
display:inline;
float:left;
position:relative;
}

.textoElemMediaListadoPortadaPC {
margin-left:10px;
margin-top:5px;
width:210px;
height:56px;
display:inline;
float:left;
position:relative;
overflow:hidden;
color:#666;
font-family:Arial;
font-size:14px;
line-height:18px;
font-weight:700;
}

.textoElemMediaListado2PortadaPC {
margin-left:104px;
margin-top:15px;
width:200px;
display:inline;
float:left;
position:relative;
overflow:hidden;
color:#666;
font-family:Arial;
font-size:14px;
font-weight:700;
}

.textoElemMediaListadoPortadaPC a,.textoElemMediaListado2PortadaPC a {
color:#666;
font-family:Arial;
font-size:16px;
font-weight:700;
text-decoration:none;
}

.textoElemMediaListadoPortadaPC a:hover,.textoElemMediaListado2PortadaPC a:hover {
text-decoration:underline;
color:#666;
}

.fileteVerticalIntraMedia {
margin-left:12px;
margin-right:12px;
width:1px;
height:100px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/planConecta/portada/fileteVerticalPC.gif);
background-repeat:no-repeat;
}

.fileteHorizontalPC {
width:734px;
height:1px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/planConecta/portada/fileteHorizontalPC.gif);
background-repeat:no-repeat;
}

.zonaFAQ {
width:734px;
display:inline;
float:left;
position:relative;
}

.textoConectaTitle {
margin-top:20px;
margin-left:60px;
width:680px;
font-family:Arial;
font-size:20px;
text-decoration:none;
color:#004b8d;
display:inline;
float:left;
position:relative;
}

.textoFAQ {
margin-top:18px;
margin-left:60px;
width:680px;
font-family:Arial;
font-size:12px;
text-decoration:none;
font-weight:700;
color:#404040;
display:inline;
float:left;
position:relative;
}

.textoFAQ a,.textoFAQ a:hover {
color:#404040;
}

.texto2FAQ {
margin-left:60px;
width:680px;
display:inline;
float:left;
position:relative;
}

.texto2FAQ ul li {
margin-top:20px;
margin-left:30px;
list-style-type:none;
}

.enlacesZonaFAQ {
margin-top:40px;
margin-left:60px;
width:680px;
display:inline;
float:left;
position:relative;
}

.enlaces2ZonaFAQ {
margin-top:5px;
margin-bottom:10px;
margin-left:60px;
width:680px;
display:inline;
float:left;
position:relative;
}

.menuZonaFAQ {
margin-top:10px;
width:680px;
color:#004b8d;
display:inline;
float:left;
position:relative;
font-family:Arial;
font-size:11px;
}

.textoPestanyaNoSelecListPC {
color:#666;
cursor:pointer;
}

.textoPestanyaSelecListPC,.textoPestanyaNoSelecListPC {
font-family:Arial;
font-size:14px;
font-weight:700;
text-transform:uppercase;
display:inline;
float:left;
padding-right:10px;
position:relative;
}

.divListadoTitlesAuthor {
background-color:#FFF;
display:inline;
float:left;
overflow:hidden;
position:relative;
width:730px;
}

.ListadoTitlesAuthor {
margin-left:30px;
margin-top:10px;
border-bottom:1px dashed #666;
float:none;
position:relative;
overflow:hidden;
color:#666;
font-family:Arial;
font-size:16px;
font-weight:700;
text-decoration:none;
}

a.CNombreAuthorLink {
color:#000;
text-decoration:none;
}

.CFechaAuthorlist {
color:#666;
font-family:Arial;
font-size:13px;
float:none;
}

.CtextoAuthorlist {
color:#333;
font-family:Arial;
font-size:12px;
font-style:normal;
font-weight:400;
float:none;
}

.contTextoListadoOpinionTitulos {
width:310px;
height:60px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(../images/galeriaVideos/backgroundElementoLista.gif);
background-repeat:no-repeat;
margin-left:15px;
}

a.CAutorSigue {
color:#004B8E;
text-decoration:none;
font-size:10px;
}

.zonaVisorVideos,.cabeceraVisorVideos {
display:inline;
float:left;
position:relative;
}

.zonaClasificacionContenidosPortadaPC,.listadoEnlacesPlanConecta {
float:left;
display:inline;
position:relative;
}

a.contSeccionListadoPortadaPCAuthor,.contSeccionListadoPortadaPCAuthor a:hover {
color:#004B8D;
font-family:Arial;
font-size:14px;
}

.texto2FAQ a,.texto2FAQ a:hover,.menuZonaFAQ a,.menuZonaFAQ a:hover,.textoPestanyaSelecListPC {
color:#004B8E;
}.CNoticiaRelacionada a,.CNoticiaRelacionada a:hover,.CSigue a,.CSigue a:hover,.menu_nota a,.menu_nota a:hover,.menu_nota_enviar a,.menu_nota_enviar a:hover,.menu_nota_imprimir a,.menu_nota_imprimir a:hover,.menu_nota_correccion a,.menu_nota_correccion a:hover,.numpagina_nota a,.numpagina_nota a:hover {
color:#696057;
font-weight:700;
}

.CContenedorParticipacion a,.CContenedorParticipacion a:hover,.CVotos_nota a,.CVotos_nota a:hover,.link_mas_nota a,.link_mas_nota a:hover,.CContenedorParticipacionPiezaEsp a,.CContenedorParticipacionPiezaEsp a:hover {
color:#666;
}

div.intersticial {
position:absolute;
display:inline;
top:0;
left:0;
background-color:#FFF;
width:100%;
height:100%;
z-index:3333;
}

body > div.intersticial {
position:fixed;
background-color:#FFF;
display:block;
top:0;
left:0;
}

div.intersticial div.closeButton {
float:right;
color:#024687;
font-family:Verdana, Arial, sans-serif;
font-weight:700;
font-size:1.1em;
margin:2em 2em 0 0;
}

div.intersticial div.content {
background-color:#FFF;
height:100%;
cursor:pointer;
margin:0 auto;
padding:2em;
}

div.intersticial wrapper_intersticial {
padding-top:1em;
}.cabeceraTiempo {
margin-left:10px;
display:inline;
float:left;
position:relative;
}

.cuerpoTiempo {
margin-top:10px;
width:181px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.zonaMapaTiempo {
width:181px;
height:146px;
display:inline;
float:left;
position:relative;
text-align:center;
overflow:hidden;
}

.fuenteTiempo {
margin-top:8px;
width:181px;
display:inline;
float:left;
position:relative;
overflow:hidden;
font-family:Arial;
font-size:9px;
color:#004B8E;
}

.CTiempoMargen {
margin-left:60px;
display:inline;
float:left;
position:relative;
overflow:hidden;
}

.firma_tiempo {
margin-left:0;
margin-top:20px;
}

.CContenedorMapaYListado {
margin-left:8px;
margin-top:20px;
display:inline;
float:left;
position:relative;
width:auto;
}

.contMapaTiempo {
display:inline;
float:left;
position:relative;
background-image:url(/themes/xornal/images/tiempo/fondoTablaTiempoMapa.gif);
background-repeat:no-repeat;
background-position:top left;
text-align:center;
vertical-align:middle;
width:166px;
height:180px;
}

.listaEnlacesCiudades {
margin-top:3px;
display:inline;
float:left;
position:relative;
overflow:hidden;
background-image:url(/themes/xornal/images/tiempo/fondoTablaTiempoListado.gif);
background-repeat:no-repeat;
background-position:top left;
width:147px;
height:180px;
margin-left:-1px;
padding:20px;
}

.enlaceCiudad {
width:171px;
display:inline;
float:left;
position:relative;
color:#004B8E;
font-family:Arial;
font-size:14px;
padding-left:8px;
background-image:url(/themes/xornal/images/fotoVideoDia/flechitaAzul.gif);
background-position:left center;
background-repeat:no-repeat;
margin-bottom:2px;
}

.CTablaResultadoTiempo {
margin-top:0;
margin-left:0;
width:580px;
height:420px;
float:left;
display:inline;
position:relative;
background-image:url(../images/tiempo/fondoTablaTiempo.gif);
background-repeat:no-repeat;
overflow:hidden;
}

.CListaValoresSemana {
margin-left:-2px;
margin-top:0;
position:relative;
display:inline;
float:left;
}

.CValoresTiempoDia {
margin-left:9px;
margin-top:5px;
width:63px;
height:410px;
position:relative;
display:inline;
float:left;
}

.CIconoTiempo {
width:61px;
height:74px;
position:relative;
display:inline;
float:left;
}

.CIconoTiempo img {
margin-top:10px;
margin-left:6px;
}

.CCeldaCabecera {
width:61px;
height:25px;
position:relative;
display:inline;
float:left;
overflow:hidden;
font-size:16px;
font-weight:700;
text-align:center;
}

.CCeldaLateral {
width:61px;
height:45px;
display:inline;
float:left;
position:relative;
overflow:hidden;
text-align:left;
font-size:12px;
}

.CCeldaLateralIconoTiempo {
height:74px;
}

.CCeldaStandard {
width:61px;
height:45px;
display:inline;
float:left;
position:relative;
overflow:hidden;
text-align:center;
font-size:18px;
font-weight:700;
}

.CCeldaStandardImg {
width:61px;
height:45px;
display:inline;
float:left;
position:relative;
overflow:hidden;
text-align:center;
}

.CCeldaStandardSupImg {
width:61px;
height:55px;
display:inline;
float:left;
position:relative;
overflow:hidden;
text-align:center;
}

.CPosVertTextoTiempo {
margin-top:12px;
}

.CPosVertTextoLatTiempo1 {
margin-top:16px;
}

.CPosVertTextoLatTiempo2 {
margin-top:8px;
}

.CPosVertImgTiempo {
margin-top:6px;
}

.datoNegro {
color:#000;
}

.datoRojo {
color:#941b20;
}

.datoAzul {
color:#004b8d;
}

.contBannerPublicidadTiempo {
margin-left:5px;
overflow:hidden;
text-align:center;
width:567px;
}

.enlaceCiudatTiempo {
margin-top:2px;
width:171px;
display:inline;
float:left;
position:relative;
color:#004B8E;
font-family:Arial;
font-size:14px;
}

.enlaceCiudadTiempo img {
margin-right:5px;
}

.enlaceCiudadTiempo a {
color:#004B8E;
text-decoration:none;
}

.enlaceCiudadTiempo a:hover {
color:#666;
text-decoration:none;
}

.menu_derecha_tiempo {
float:right;
}

table.tabla_datos {
background-image:url(/media/weather/fondoTablaTiempo.gif);
background-repeat:no-repeat;
background-position:left top;
background-color:#FFF;
float:left;
width:579px;
margin:0;
padding:0;
}

table.tabla_datos a {
display:none;
}

table.tabla_datos thead th {
font-weight:700;
color:#000;
font-family:Arial;
text-align:center;
font-size:14px;
vertical-align:top;
padding-top:2px;
}

table.tabla_datos tbody th {
padding-left:4px;
font-size:12px;
text-align:left;
font-weight:400;
}

table.tabla_datos thead tr.cabecera_niv1 th {
max-width:70px;
width:70px;
overflow:hidden;
}

table.tabla_datos thead tr.cabecera_niv2 th {
padding-bottom:16px;
}

.tabla_datos td {
height:44px;
font-size:18px;
font-weight:700;
}

.tabla_datos td,.tabla_datos th {
text-align:center;
background:transparent;
}

span.texto_rojo {
color:#941B20;
font-size:18px;
font-weight:700;
text-align:center;
}

span.texto_azul {
color:#004B8D;
font-size:18px;
font-weight:700;
text-align:center;
}

.containerTempo {
margin-left:60px;
}

.containerTempo h2 {
font-weight:700;
margin-bottom:5px;
}

.raduv_pred_nivel1 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c1_pred.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_pred_nivel2 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c2_pred.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_pred_nivel3 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c3_pred.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_pred_nivel4 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c4_pred.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_pred_nivel5 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c5_pred.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel1 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c1.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel2 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c2.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel3 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c3.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel4 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c4.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel5 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c5.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel6 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c6.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel7 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c7.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel8 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c8.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel9 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c9.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel10 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c10.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.raduv_nivel11 {
background-image:url(/media/weather/aemet/radiacionuv/uvi_c11.gif);
background-repeat:no-repeat;
background-position:top left;
width:24px;
}

.CContenedorMapaYListado img {
margin-right:0;
border:0;
}

.contMapaTiempo img {
vertical-align:middle;
margin:auto;
padding:20px;
}

.CContenedorMapaYListado .fileteFotoVideoDia {
background-image:url(/themes/xornal/images/fotoVideoDia/fileteDashedDeportesXPress.gif);
display:inline;
float:left;
height:1px;
margin-top:2px;
overflow:hidden;
position:relative;
width:100px;
}

.menu_nota_derecha {
float:left;
margin:20px 20px 20px 440px;
}

.zonaMapaTiempo img,table.tabla_datos img {
border:0;
}#lightwindow_overlay {
display:none;
visibility:hidden;
position:absolute;
top:0;
left:0;
width:100%;
height:100px;
z-index:500;
}

#lightwindow {
display:none;
visibility:hidden;
position:absolute;
z-index:999;
line-height:0;
}

#lightwindow_container {
display:none;
visibility:hidden;
position:absolute;
margin:0;
padding:0;
}

* html #lightwindow_container {
overflow:hidden;
}

#lightwindow_contents {
overflow:hidden;
z-index:0;
position:relative;
border:10px solid #fff;
background-color:#fff;
}

#lightwindow_loading {
height:100%;
width:100%;
top:0;
left:0;
z-index:9999;
position:absolute;
background-color:#f0f0f0;
padding:10px;
}

#lightwindow_loading span {
font-size:12px;
line-height:32px;
color:#444;
float:left;
padding:0 10px 0 0;
}

#lightwindow_loading span a,#lightwindow_loading span a:link,#lightwindow_loading span a:visited {
color:#09F;
text-decoration:none;
cursor:pointer;
}

#lightwindow_loading span a:hover,#lightwindow_loading span a:active {
text-decoration:underline;
}

#lightwindow_loading img {
float:left;
margin:0 10px 0 0;
}

#lightwindow_navigation {
position:absolute;
top:0;
left:0;
display:none;
}

#lightwindow_navigation a,#lightwindow_navigation a:link,#lightwindow_navigation a:visited,#lightwindow_navigation a:hover,#lightwindow_navigation a:active {
outline:none;
}

#lightwindow_previous,#lightwindow_next {
width:49%;
height:100%;
background:transparent url(../images/lightwindow/blank.gif) no-repeat;
display:block;
}

#lightwindow_previous {
float:left;
left:0;
}

#lightwindow_next {
float:right;
right:0;
}

#lightwindow_previous:hover,#lightwindow_previous:active {
background:url(../images/lightwindow/prevlabel.gif) left 15% no-repeat;
}

#lightwindow_next:hover,#lightwindow_next:active {
background:url(../images/lightwindow/nextlabel.gif) right 15% no-repeat;
}

#lightwindow_galleries {
width:100%;
position:absolute;
z-index:50;
display:none;
overflow:hidden;
bottom:0;
left:0;
margin:0 0 0 10px;
}

#lightwindow_galleries_tab_container {
width:100%;
height:0;
overflow:hidden;
}

a#lightwindow_galleries_tab,a:link#lightwindow_galleries_tab,a:visited#lightwindow_galleries_tab {
display:block;
height:20px;
width:77px;
float:right;
line-height:22px;
text-decoration:none;
font-weight:700;
cursor:pointer;
font-size:11px;
color:#ffffbe;
background:url(../images/lightwindow/black-70.png) repeat 0 0 transparent;
}

a:hover#lightwindow_galleries_tab,a:active#lightwindow_galleries_tab {
color:#ffffbe;
}

#lightwindow_galleries_tab_span {
display:block;
height:20px;
width:63px;
padding:0 7px;
}

#lightwindow_galleries_tab .up {
background:url(../images/lightwindow/arrow-up.gif) no-repeat 60px 5px transparent;
}

#lightwindow_galleries_tab .down {
background:url(../images/lightwindow/arrow-down.gif) no-repeat 60px 6px transparent;
}

#lightwindow_galleries_list {
background:url(../images/lightwindow/black-70.png) repeat 0 0 transparent;
overflow:hidden;
height:0;
}

.lightwindow_galleries_list {
width:200px;
float:left;
margin:0 0 10px;
padding:10px;
}

.lightwindow_galleries_list h1 {
color:#09F;
text-decoration:none;
font-weight:700;
cursor:pointer;
font-size:16px;
padding:10px 0 5px;
}

.lightwindow_galleries_list li {
list-style-type:none;
margin:5px 0;
}

.lightwindow_galleries_list a,.lightwindow_galleries_list a:link,.lightwindow_galleries_list a:visited {
display:block;
line-height:22px;
color:#fff;
text-decoration:none;
font-weight:700;
cursor:pointer;
font-size:11px;
padding:0 0 0 10px;
}

.lightwindow_galleries_list a:hover,.lightwindow_galleries_list a:active {
background:#000;
color:#ffffbe;
border-left:3px solid #ffffbe;
padding:0 0 0 7px;
}

#lightwindow_data {
position:absolute;
}

#lightwindow_data_slide {
position:relative;
}

#lightwindow_data_slide_inner {
background-color:#fff;
padding:0 10px 10px;
}

#lightwindow_data_caption {
color:#666;
line-height:25px;
background-color:#fff;
clear:both;
padding:10px 0 0;
}

#lightwindow_data_details {
background-color:#f0f0f0;
height:20px;
padding:0 10px;
}

#lightwindow_data_author_container {
width:40%;
text-align:right;
color:#666;
font-style:italic;
font-size:10px;
line-height:20px;
float:right;
overflow:hidden;
}

#lightwindow_data_gallery_container {
font-size:10px;
width:40%;
text-align:left;
color:#666;
line-height:20px;
float:left;
overflow:hidden;
}

#lightwindow_title_bar {
height:38px;
overflow:visible;
margin-top:14px;
vertical-align:bottom;
display:block;
}

#lightwindow_title_bar_title {
color:#ffffbe;
font-size:13px;
line-height:14px;
text-align:left;
float:left;
}

a#lightwindow_title_bar_close_link,a:link#lightwindow_title_bar_close_link,a:visited#lightwindow_title_bar_close_link {
float:right;
text-align:right;
cursor:pointer;
color:#ffffbe;
line-height:8px;
margin:0;
padding:0;
}

a:hover#lightwindow_title_bar_close_link,a:active#lightwindow_title_bar_close_link {
color:#fff;
}

#lightwindow p {
color:#000;
padding-right:10px;
}

#lightwindow_loading_shim,#lightwindow_navigation_shim {
display:none;
left:0;
position:absolute;
top:0;
width:100%;
height:100%;
}

#lightwindow_previous_title,#lightwindow_next_title,.lightwindow_hidden {
display:none;
}

* html a#lightwindow_galleries_tab,* html a:link#lightwindow_galleries_tab,* html a:visited#lightwindow_galleries_tab,* html #lightwindow_galleries_list {
background:none;
background-color:#000;
opacity:.70;
filter:alpha(opacity=70);
}.carousel-container {
width:976px;
height:104px;
line-height:normal;
text-align:center;
font-size:12px;
font-family:Arial, verdana, sans-serif;
}

.carousel-container strong {
color:#024687;
font-weight:700;
text-transform:uppercase;
font-size:14px;
}

.carousel-pre {
width:125px;
height:96px;
float:left;
font-family:Arial;
font-size:12px;
}

.carousel-pre span {
color:#666;
font-size:12px;
font-weight:700;
}

.carousel-pre a {
color:#000;
text-decoration:underline;
font-family:Arial;
font-size:12px;
}

.carousel-pre .foto {
background-image:url(../images/carousel/fondo-carousel.png);
background-position:left top;
background-repeat:repeat-x;
text-align:center;
padding-top:5px;
}

.carousel-pre div {
text-align:center;
}

.carousel-pre a:hover {
color:#000;
text-decoration:underline;
}

.carousel-post {
background-image:url(../images/carousel/fondo-carousel.png);
background-repeat:repeat-x;
width:171px;
padding-top:5px;
text-align:left;
float:right;
}

.carousel-post select {
font-size:10px;
max-width:140px;
width:140px;
margin:0;
padding:0;
}

.carousel-post .editorial {
padding-left:20px;
height:60px;
}

.carousel-post .editorial strong {
margin-left:-10px;
height:60px;
}

.carousel-post .autores {
height:35px;
text-align:center;
}

.carousel-post a,.carousel-pre a {
color:#000;
text-decoration:none;
font-family:Arial, verdana, sans-serif;
font-size:12px;
}

.carousel-left {
width:20px;
height:60px;
float:left;
}

.carousel-left img,.carousel-right img {
margin-top:20px;
}

.carousel-right {
width:20px;
height:60px;
float:right;
}

.carousel-center {
background-color:transparent;
float:left;
width:640px;
height:60px;
border-bottom:2px solid #024687;
}

.carousel-message {
clear:both;
margin-top:0;
height:20px;
color:#333;
font-size:12px;
font-weight:400;
font-family:Arial, Verdana, sans-serif;
padding-top:4px;
text-align:center;
vertical-align:top;
}

.carousel-message a {
color:#333;
font-size:12px;
font-weight:700;
font-family:Arial, Verdana, sans-serif;
text-decoration:none;
}

.carousel {
width:680px;
height:100px;
float:left;
position:relative;
background-image:url(../images/carousel/fondo-carousel.png);
background-repeat:repeat-x;
padding-top:10px;
}

.carousel h2 {
margin-top:25px;
}

.carousel ul {
list-style:none;
background-color:transparent;
padding:0;
}

.carousel ul li {
float:left;
list-style:none;
display:inline;
width:60px;
height:60px;
overflow:hidden;
text-align:center;
vertical-align:middle;
margin:auto 2px;
padding:0;
}

.carousel ul li img {
border:0;
}

.autores {
margin-top:3px;
margin-left:-35px;
}

.carousel-post a:hover,.carousel-pre a:hover,.carousel-message a:hover {
text-decoration:underline;
}div.rss-box {
border:0 solid #CCC;
height:12px;
background-color:#FFF;
line-height:normal;
float:left;
display:block;
position:relative;
margin-left:10px;
margin-top:3px;
vertical-align:middle;
}

.rss-box img {
margin:0;
padding:0;
}

.rss-box a {
font-size:10px;
text-decoration:none;
color:#666;
display:block;
float:left;
margin:0;
}

.rss-box a:hover {
text-decoration:none;
color:#333;
}

.time-box {
float:left;
display:block;
position:relative;
}

.box-portada {
float:left;
display:block;
position:relative;
margin-left:10px;
}