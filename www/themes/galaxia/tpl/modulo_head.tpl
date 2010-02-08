<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" lang="es" xml:lang="es">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="verify-v1" content="M/ky7vwO/6oj2mbgQg8Adte9oaBRaxevDuql58rvg8I=" />
<meta name="msvalidate.01" content="D3C9837B111A4C67190B0E2E862C8294" />
<meta name="y_key" content="70e190382d27ce40" />
<meta name="language" content="es" />
<meta name="generator" content="OpenNeMaS - Open Source News Management System" />
<meta name="author" content="Xornal de Galicia" />
<meta name="revisit-after" content="1 days" />
<meta name="locality" content="A Coruña, Galicia, España, U.E." />
<meta http-equiv="robots" content="index,follow" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="last-modified" content="0" />
{*********************** planconecta.php ***********************}
{if preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "send")}
<title>Plan Conecta - {$smarty.const.SITE_TITLE}</title>
<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}envio_noticia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}pieza_encuesta.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}planConecta.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}planConecta_encuesta.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}portada_conecta.css"/>
{*********************** planconecta.php ***********************}
{elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "polls")}
<title>Plan Conecta - {$smarty.const.SITE_TITLE}</title>
<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}planConecta.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}planConecta_encuesta.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}portada_conecta.css"/>
{*********************** planconecta.php ***********************}
{elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME)}
<title>Plan Conecta - {$smarty.const.SITE_TITLE}</title>
<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}registro.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}envio_noticia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}planConecta.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}portada_conecta.css"/>
{*********************** search.php ***********************}
{elseif  preg_match('/search\.php/',$smarty.server.SCRIPT_NAME)}
<title>Búsqueda - {$smarty.const.SITE_TITLE}</title>
<meta name="keywords" content="xornal, xornal.com, Xornal de Galicia, diario, periodico, prensa, press, daily, newspaper, noticias, news, breaking news, Galicia, Spain, España, Espana, internacional, titulares, headlines, urgente, albumes, videos, sociedad, cultura, extras, suplementos, opinion, ultimas noticias, deportes, deportivo, celta, sport, encuestas, gente, politica, tendencias, tiempo, weather, buscador, especiales" />
<meta name="description" content="Noticias de última hora sobre la actualidad gallega, nacional, economía, deportes, cultura, sociedad. Además Plan conecta, vídeos, fotos, gráficos, entrevistas y encuestas de opinión. Xornal.com, el periódico de Galicia." />
{*********************** opinion.php ***********************}
{elseif  preg_match('/opinion_index\.php/',$smarty.server.SCRIPT_NAME)}
<title>OPINION - {$smarty.const.SITE_TITLE}</title>
<meta name="description" content="Lista de firmas de Opinion en Xornal de Galicia" />
<meta name="keywords" content="opinions, opiniones, xornal, firmas, autores" />
<meta http-equiv="Refresh" content="900; url={$smarty.const.SITE_URL}seccion/opinion/" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}opinion.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}pagina_opinion.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}pieza_encuesta.css"/>
{elseif  preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME) || preg_match('/opinion_index\.php/',$smarty.server.SCRIPT_NAME)}
<title>{$opinion->title|strip_tags|clearslash} - OPINION - {$smarty.const.SITE_TITLE}</title>
<meta http-equiv="Refresh" content="900; url={$smarty.const.SITE_URL}seccion/opinion/" />
<meta name="description" content="{$opinion->body|strip_tags|truncate:300|clearslash|regex_replace:"/'/":"\'"|escape:'html'}">
<meta name="keywords" content="{$opinion->metadata}" />
<meta name="author" content="{$author|clearslash}"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}opinion.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}pagina_opinion.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}pieza_encuesta.css"/>
{*********************** album.php ***********************}
{elseif  preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME)}
{if ($smarty.request.action eq "video")}
    <title>{$video->title|clearslash|escape:'html'} - Galerías - {$smarty.const.SITE_TITLE}</title>
    <meta name="keywords" content="{$video->metadata|clearslash|escape:'html'}" />
    <meta name="description" content="{$video->description|clearslash|escape:'html'}" />
{else}
    <title>{$album->title|clearslash|escape:'html'} - Galerías - {$smarty.const.SITE_TITLE}</title>
    <meta name="keywords" content="{$album->metadata|clearslash|escape:'html'}" />
    <meta name="description" content="{$album->description|clearslash|escape:'html'}" />
{/if}
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}actualidadVideosFotos.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}deportesXPress.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}genteXornal.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}laBolsa.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}noticiasXPress.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}titularesDia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}ultimaHora.css"/>
{*********************** article.php ***********************}
{elseif  preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)  || preg_match('/preview_content\.php/',$smarty.server.SCRIPT_NAME)}
<title>{$article->title|strip_tags|clearslash} - NOTICIAS - {$smarty.const.SITE_TITLE}</title>
<meta name="description" content="{$article->title|strip_tags|clearslash|escape:'html'}. {$contentArticle->description|clearslash|strip_tags|truncate:100|regex_replace:"/'/":"\'"|escape:'html'}" />
<meta name="keywords" content="{$article->metadata|clearslash|escape:'html'}" />
<meta http-equiv="refresh" content="1800" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}galiciaTitulares.css"/>
{*********************** index.php ***********************}
{elseif  preg_match('/index\.php/',$smarty.server.SCRIPT_NAME) || preg_match('/preview\.php/',$smarty.server.SCRIPT_NAME)}
<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
{if isset($subcategory_real_name) && !empty($subcategory_real_name)}
<title>{$subcategory_real_name} - NOTICIAS - {$smarty.const.SITE_TITLE}</title>
<meta http-equiv="Refresh" content="900; url={$smarty.const.SITE_URL}seccion/{$subcategory_name}/" />
{elseif $category_name eq 'home'}
<title>PORTADA - {$smarty.const.SITE_TITLE}</title>
<meta http-equiv="Refresh" content="900; url={$smarty.const.SITE_URL}" />
{else}
<title>{$category_real_name} - NOTICIAS - {$smarty.const.SITE_TITLE}</title>
<meta http-equiv="Refresh" content="900; url={$smarty.const.SITE_URL}seccion/{$category_name}/" />
{/if}

{stylesection name="common" compress="1" cssfilename="common.css.php"}
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}actualidadVideosFotos.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}deportesXPress.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}genteXornal.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}laBolsa.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}noticiasXPress.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}titularesDia.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}ultimaHora.css"/>
{/stylesection}

{elseif  preg_match('/weather\.php/',$smarty.server.SCRIPT_NAME)}
<title>El tiempo - {$smarty.const.SITE_TITLE}</title>
<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
{elseif  preg_match('/statics\.php/',$smarty.server.SCRIPT_NAME)}
<title>{$category_real_name} - {$smarty.const.SITE_TITLE}</title>
<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
{else}
<title>{$smarty.const.SITE_TITLE}</title>
<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
{/if}
<link rel="alternate" href="/rss/" type="application/rss+xml" class="rss" title="Xornal" />
{*foreach item="it_rss" from=$categories}
<link rel="alternate" href="/rss/{$it_rss.name}" type="application/rss+xml" class="rss" title="{$it_rss.title}" />
{/foreach*}
 {if (!empty($category_name) && ($category_name neq 'home'))}
  <link rel="alternate" href="/rss/{$category_name}/" type="application/rss+xml" class="rss" title="{$category_name}" />
 {/if}
{stylesection name="head" compress="1" cssfilename="xornal.css.php"}
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}bannerPublicidad.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}default.css?cacheburst=1259263197"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}galeriaVideos.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}galeriaFotos.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}home_noticias.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}laBolsa.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}menu.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}menuSeccion.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}noticia.css?cacheburst=1259263061"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}noticiasRecomendadas.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}noticia_noticiasMasVistas.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}opinion.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}paginador.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}pestanyas.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}pie.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}pieza_encuesta.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}portada_opinion.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css?cacheburst=1254325528"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}tiempo.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}validation.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightwindow.css" media="screen" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}carousel.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}rss.css" />
{/stylesection}
{scriptsection name="head"}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/effects.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}validation.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}swfobject.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightwindow.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}article_buttons.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}carousel.js?cacheburst=1260780614"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}intersticial.js?cacheburst=1254916599"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}CommentFormClass.js?cacheburst=1259263068"></script>
{/scriptsection}
{literal}
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
function enviar(elto, trg, acc, id) {
    var parentEl = elto.parentNode;
	while(parentEl.nodeName != "FORM") {
		parentEl = parentEl.parentNode;
	}

	parentEl.target = trg;
	parentEl.action.value = acc;
	parentEl.id.value = id;

	if(objForm != null) {
		objForm.submit();
	} else {
		parentEl.submit();
	}
}

function sendFormValidate(elto, trg, acc, id, formID)
{
    var checkForm = new Validation(formID, {immediate:true, onSubmit:true});
    if(!checkForm.validate())
        return;

    enviar(elto, trg, acc, id);
}
/* ]]> */
</script>
{/literal}
</head>
