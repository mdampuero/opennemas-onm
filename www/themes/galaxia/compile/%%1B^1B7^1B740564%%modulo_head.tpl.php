<?php /* Smarty version 2.6.18, created on 2010-01-14 00:57:24
         compiled from modulo_head.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strip_tags', 'modulo_head.tpl', 62, false),array('modifier', 'clearslash', 'modulo_head.tpl', 62, false),array('modifier', 'truncate', 'modulo_head.tpl', 64, false),array('modifier', 'regex_replace', 'modulo_head.tpl', 64, false),array('modifier', 'escape', 'modulo_head.tpl', 64, false),array('block', 'stylesection', 'modulo_head.tpl', 111, false),array('block', 'scriptsection', 'modulo_head.tpl', 167, false),)), $this); ?>
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
<?php if (preg_match ( '/planconecta\.php/' , $_SERVER['SCRIPT_NAME'] ) && ( $_REQUEST['action'] == 'send' )): ?>
<title>Plan Conecta - <?php echo @SITE_TITLE; ?>
</title>
<meta name="keywords" content="<?php echo @SITE_KEYWORDS; ?>
" />
<meta name="description" content="<?php echo @SITE_DESCRIPTION; ?>
" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
envio_noticia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
pieza_encuesta.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
planConecta.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
planConecta_encuesta.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
portada_conecta.css"/>
<?php elseif (preg_match ( '/planconecta\.php/' , $_SERVER['SCRIPT_NAME'] ) && ( $_REQUEST['action'] == 'polls' )): ?>
<title>Plan Conecta - <?php echo @SITE_TITLE; ?>
</title>
<meta name="keywords" content="<?php echo @SITE_KEYWORDS; ?>
" />
<meta name="description" content="<?php echo @SITE_DESCRIPTION; ?>
" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
planConecta.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
planConecta_encuesta.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
portada_conecta.css"/>
<?php elseif (preg_match ( '/planconecta\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
<title>Plan Conecta - <?php echo @SITE_TITLE; ?>
</title>
<meta name="keywords" content="<?php echo @SITE_KEYWORDS; ?>
" />
<meta name="description" content="<?php echo @SITE_DESCRIPTION; ?>
" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
registro.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
envio_noticia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
planConecta.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
portada_conecta.css"/>
<?php elseif (preg_match ( '/search\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
<title>Búsqueda - <?php echo @SITE_TITLE; ?>
</title>
<meta name="keywords" content="xornal, xornal.com, Xornal de Galicia, diario, periodico, prensa, press, daily, newspaper, noticias, news, breaking news, Galicia, Spain, España, Espana, internacional, titulares, headlines, urgente, albumes, videos, sociedad, cultura, extras, suplementos, opinion, ultimas noticias, deportes, deportivo, celta, sport, encuestas, gente, politica, tendencias, tiempo, weather, buscador, especiales" />
<meta name="description" content="Noticias de última hora sobre la actualidad gallega, nacional, economía, deportes, cultura, sociedad. Además Plan conecta, vídeos, fotos, gráficos, entrevistas y encuestas de opinión. Xornal.com, el periódico de Galicia." />
<?php elseif (preg_match ( '/opinion_index\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
<title>OPINION - <?php echo @SITE_TITLE; ?>
</title>
<meta name="description" content="Lista de firmas de Opinion en Xornal de Galicia" />
<meta name="keywords" content="opinions, opiniones, xornal, firmas, autores" />
<meta http-equiv="Refresh" content="900; url=<?php echo @SITE_URL; ?>
seccion/opinion/" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
opinion.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
pagina_opinion.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
pieza_encuesta.css"/>
<?php elseif (preg_match ( '/opinion\.php/' , $_SERVER['SCRIPT_NAME'] ) || preg_match ( '/opinion_index\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
<title><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['opinion']->title)) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
 - OPINION - <?php echo @SITE_TITLE; ?>
</title>
<meta http-equiv="Refresh" content="900; url=<?php echo @SITE_URL; ?>
seccion/opinion/" />
<meta name="description" content="<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['opinion']->body)) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 300) : smarty_modifier_truncate($_tmp, 300)))) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/'/", "\'") : smarty_modifier_regex_replace($_tmp, "/'/", "\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
<meta name="keywords" content="<?php echo $this->_tpl_vars['opinion']->metadata; ?>
" />
<meta name="author" content="<?php echo ((is_array($_tmp=$this->_tpl_vars['author'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
opinion.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
pagina_opinion.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
pieza_encuesta.css"/>
<?php elseif (preg_match ( '/gallery\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
<?php if (( $_REQUEST['action'] == 'video' )): ?>
    <title><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['video']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 - Galerías - <?php echo @SITE_TITLE; ?>
</title>
    <meta name="keywords" content="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['video']->metadata)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
    <meta name="description" content="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['video']->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
<?php else: ?>
    <title><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['album']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 - Galerías - <?php echo @SITE_TITLE; ?>
</title>
    <meta name="keywords" content="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['album']->metadata)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
    <meta name="description" content="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['album']->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
<?php endif; ?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
actualidadVideosFotos.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
deportesXPress.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
genteXornal.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
laBolsa.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
noticiasXPress.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
titularesDia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
ultimaHora.css"/>
<?php elseif (preg_match ( '/article\.php/' , $_SERVER['SCRIPT_NAME'] ) || preg_match ( '/preview_content\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
<title><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
 - NOTICIAS - <?php echo @SITE_TITLE; ?>
</title>
<meta name="description" content="<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
. <?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['contentArticle']->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 100) : smarty_modifier_truncate($_tmp, 100)))) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/'/", "\'") : smarty_modifier_regex_replace($_tmp, "/'/", "\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
<meta name="keywords" content="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->metadata)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
<meta http-equiv="refresh" content="1800" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
galiciaTitulares.css"/>
<?php elseif (preg_match ( '/index\.php/' , $_SERVER['SCRIPT_NAME'] ) || preg_match ( '/preview\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
<meta name="keywords" content="<?php echo @SITE_KEYWORDS; ?>
" />
<meta name="description" content="<?php echo @SITE_DESCRIPTION; ?>
" />
<?php if (isset ( $this->_tpl_vars['subcategory_real_name'] ) && ! empty ( $this->_tpl_vars['subcategory_real_name'] )): ?>
<title><?php echo $this->_tpl_vars['subcategory_real_name']; ?>
 - NOTICIAS - <?php echo @SITE_TITLE; ?>
</title>
<meta http-equiv="Refresh" content="900; url=<?php echo @SITE_URL; ?>
seccion/<?php echo $this->_tpl_vars['subcategory_name']; ?>
/" />
<?php elseif ($this->_tpl_vars['category_name'] == 'home'): ?>
<title>PORTADA - <?php echo @SITE_TITLE; ?>
</title>
<meta http-equiv="Refresh" content="900; url=<?php echo @SITE_URL; ?>
" />
<?php else: ?>
<title><?php echo $this->_tpl_vars['category_real_name']; ?>
 - NOTICIAS - <?php echo @SITE_TITLE; ?>
</title>
<meta http-equiv="Refresh" content="900; url=<?php echo @SITE_URL; ?>
seccion/<?php echo $this->_tpl_vars['category_name']; ?>
/" />
<?php endif; ?>

<?php $this->_tag_stack[] = array('stylesection', array('name' => 'common','compress' => '1','cssfilename' => "common.css.php")); $_block_repeat=true;smarty_block_stylesection($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
actualidadVideosFotos.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
deportesXPress.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
fotoVideoDia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
genteXornal.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
laBolsa.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
noticiasXPress.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
titularesDia.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
ultimaHora.css"/>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_stylesection($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<?php elseif (preg_match ( '/weather\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
<title>El tiempo - <?php echo @SITE_TITLE; ?>
</title>
<meta name="keywords" content="<?php echo @SITE_KEYWORDS; ?>
" />
<meta name="description" content="<?php echo @SITE_DESCRIPTION; ?>
" />
<?php elseif (preg_match ( '/statics\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
<title><?php echo $this->_tpl_vars['category_real_name']; ?>
 - <?php echo @SITE_TITLE; ?>
</title>
<meta name="keywords" content="<?php echo @SITE_KEYWORDS; ?>
" />
<meta name="description" content="<?php echo @SITE_DESCRIPTION; ?>
" />
<?php else: ?>
<title><?php echo @SITE_TITLE; ?>
</title>
<meta name="keywords" content="<?php echo @SITE_KEYWORDS; ?>
" />
<meta name="description" content="<?php echo @SITE_DESCRIPTION; ?>
" />
<?php endif; ?>
<link rel="alternate" href="/rss/" type="application/rss+xml" class="rss" title="Xornal" />
 <?php if (( ! empty ( $this->_tpl_vars['category_name'] ) && ( $this->_tpl_vars['category_name'] != 'home' ) )): ?>
  <link rel="alternate" href="/rss/<?php echo $this->_tpl_vars['category_name']; ?>
/" type="application/rss+xml" class="rss" title="<?php echo $this->_tpl_vars['category_name']; ?>
" />
 <?php endif; ?>
<?php $this->_tag_stack[] = array('stylesection', array('name' => 'head','compress' => '1','cssfilename' => "xornal.css.php")); $_block_repeat=true;smarty_block_stylesection($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
bannerPublicidad.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
default.css?cacheburst=1259263197"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
galeriaVideos.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
galeriaFotos.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
home_noticias.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
laBolsa.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
menu.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
menuSeccion.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
noticia.css?cacheburst=1259263061"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
noticiasRecomendadas.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
noticia_noticiasMasVistas.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
opinion.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
paginador.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
pestanyas.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
pie.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
pieza_encuesta.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
portada_opinion.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
style.css?cacheburst=1254325528"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
tiempo.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
validation.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
lightwindow.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
carousel.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
rss.css" />
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_stylesection($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php $this->_tag_stack[] = array('scriptsection', array('name' => 'head')); $_block_repeat=true;smarty_block_scriptsection($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
utils.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
prototype.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
scriptaculous/effects.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
validation.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
swfobject.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
lightwindow.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
article_buttons.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
carousel.js?cacheburst=1260780614"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
intersticial.js?cacheburst=1254916599"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
CommentFormClass.js?cacheburst=1259263068"></script>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_scriptsection($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php echo '
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
'; ?>

</head>