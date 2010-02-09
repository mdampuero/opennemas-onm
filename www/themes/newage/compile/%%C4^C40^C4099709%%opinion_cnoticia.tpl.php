<?php /* Smarty version 2.6.18, created on 2010-01-29 23:16:26
         compiled from opinion_cnoticia.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'opinion_cnoticia.tpl', 13, false),array('modifier', 'truncate', 'opinion_cnoticia.tpl', 21, false),array('modifier', 'date_format', 'opinion_cnoticia.tpl', 23, false),array('modifier', 'escape', 'opinion_cnoticia.tpl', 101, false),array('function', 'img_tag', 'opinion_cnoticia.tpl', 36, false),array('insert', 'numComments', 'opinion_cnoticia.tpl', 47, false),array('insert', 'rating', 'opinion_cnoticia.tpl', 58, false),)), $this); ?>
<div class="CContainerCabeceraOpinion">
    <div class="CContainerFotoComentarista">
    <?php if ($this->_tpl_vars['opinion']->type_opinion != 1): ?>
        <?php if ($this->_tpl_vars['opinion']->path_img): ?>
            <img src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['opinion']->path_img; ?>
" width="110" alt="<?php echo $this->_tpl_vars['opinion']->name; ?>
"/>
        <?php endif; ?>
    <?php endif; ?> 
    </div>    
    <div class="CContainerDatosYTitularCabOpinion">
        <div class="CDatosCabOpinion">
        <?php if ($this->_tpl_vars['opinion']->type_opinion == 0): ?>
                        <div class="CNombreCabOpinion"> <a class="CNombreCabOpinionLink" href="/opinions/opinions_do_autor/<?php echo $this->_tpl_vars['opinion']->fk_author; ?>
/<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->name)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
.html"><?php echo $this->_tpl_vars['opinion']->name; ?>
 </a>  </div>
                        <div class="CSeparadorVAzulCabOpinion"></div>
        <?php elseif ($this->_tpl_vars['opinion']->type_opinion == 2): ?>
            <div class="CNombreCabOpinion">  <a class="CNombreCabOpinionLink" href="/opinions/opinions_do_autor/<?php echo $this->_tpl_vars['opinion']->fk_author; ?>
/<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->name)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
.html"> <?php echo $this->_tpl_vars['opinion']->name; ?>
</a> </div>
                        <div class="CSeparadorVAzulCabOpinion"></div>
        <?php endif; ?>
            <div class="CRolCabOpinion"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['opinion']->condition)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 34, "...", 'true') : smarty_modifier_truncate($_tmp, 34, "...", 'true')); ?>
</div>
            <div class="CSeparadorVAzulCabOpinion"></div>
            <div class="CFechaCabOpinion"><?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->changed)) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d-%m-%Y %H:%M") : smarty_modifier_date_format($_tmp, "%d-%m-%Y %H:%M")); ?>
</div>
        </div>
        
        <div class="CTitularCabOpinion">
            <h2><?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</h2>
        </div>
    </div>
</div>

    <div class="CHeaderArticle">
        <div class="superior">
            <div class="share">
                COMPARTIR OPINION:
                <a href="http://chuza.org/submit.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Chuza!"><?php echo smarty_function_img_tag(array('file' => "enviarA/chuza.gif",'baseurl' => $this->_tpl_vars['params']['IMAGE_DIR'],'alt' => "Chuza!"), $this);?>
</a>
                <a href="http://www.facebook.com/share.php?u=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Facebook"><?php echo smarty_function_img_tag(array('file' => "enviarA/facebook.png",'baseurl' => $this->_tpl_vars['params']['IMAGE_DIR'],'alt' => 'Facebook'), $this);?>
</a>
                <a href="http://www.twitter.com/home?status=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Twitter"><?php echo smarty_function_img_tag(array('file' => "enviarA/twitter.png",'baseurl' => $this->_tpl_vars['params']['IMAGE_DIR'],'alt' => 'Twitter'), $this);?>
</a>
                <a href="http://meneame.net/submit.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Meneame"><?php echo smarty_function_img_tag(array('file' => "enviarA/meneame.gif",'baseurl' => $this->_tpl_vars['params']['IMAGE_DIR'],'alt' => "Meneame.net"), $this);?>
</a>
                <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Google"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/google.gif" alt="Google" /></a>
                <a href="http://www.digg.com/submit?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Digg"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/digg.gif" alt="Digg" /></a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="inferior">
			<div class="tools">
				<span class="comments"><?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'numComments', 'id' => $this->_tpl_vars['opinion']->id)), $this); ?>
 Comentarios</span>
				<span class="icons" id="articleButtons" style="display:none;">
                    					<a href="#imprimir" title="Imprimir"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticia/print.gif" alt="Print" /></a>
					<a href="#enviar" title="Email"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticia/email.gif" alt="Email" /></a>
					<a href="#ampliar" title="Aumentar"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticia/fontIncrease.gif" alt="Aumentar" /></a>
                    					<a href="#reducir" title="Disminuir"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticia/fontDecrease.gif" alt="Disminuir" /></a>
				</span>
			</div>
            <?php if (( $_REQUEST['category_name'] == 'opinion' )): ?>
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'rating', 'id' => $this->_tpl_vars['opinion']->id, 'page' => 'article', 'type' => 'vote')), $this); ?>

            <?php else: ?>
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'rating', 'id' => $this->_tpl_vars['opinion']->id, 'page' => 'article', 'type' => 'vote')), $this); ?>

            <?php endif; ?>
            <div class="clear"></div>
        </div>
    </div>

    <div class="CNoticiaMargen">
        <div class="cuerpo_article">
            <div class="CTextoOpinion"><?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->body)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
        </div>
    </div>
    <div class="clear"></div>


    <div class="CFooterArticle">
        <div class="CTextoCompartir">Si te gusta Xornal.com, comp&aacute;rtenos con tus amigos.<br />Disfruta de la libertad de expresi&oacute;n.</div>
        <div class="share_right">
            <a eref="http://chuza.org/submit.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Chuza!"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/chuza.gif" alt="Chuza!" /></a>
            <a href="http://www.facebook.com/share.php?u=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Facebook"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/facebook.png" alt="Facebook" /></a>
            <a href="http://www.twitter.com/home?status=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Twitter"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/twitter.png" alt="Twitter" /></a>
            <a href="http://meneame.net/submit.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Meneame"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/meneame.gif" alt="Meneame"/></a>
            <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Google"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/google.gif" alt="Google" /></a>
            <a href="http://www.digg.com/submit?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Digg"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/digg.gif" alt="Digg" /></a>
            <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Yahoo!"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/yahoo.gif" alt="Yahoo!"/></a>
            <a href="http://www.stumbleupon.com/refer.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Stumble"><img alt="Stumble" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/stumble.gif"/></a>
            <a href="http://del.icio.us/post?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="del.icio.us"><img alt="del.icio.us" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/delicious.gif"/></a>
            <a href="http://www.technorati.com/faves?add=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Technorati"><img alt="Technorati" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/technorati.gif"/></a>
        </div>
        <div class="clear"></div>
        <div class="CTextoNotaEnviarA">Nota: es posible que tengas que estar registrado y autentificado en estos servicios para poder anotar el contenido correctamente.</div>
    </div>
<?php echo '
<script type="text/javascript">
/* <![CDATA[ */
document.observe(\'dom:loaded\', function() {
    if($(\'articleButtons\')) {
        var articleRef = $(\'articleButtons\').up(4);
        new OpenNeMas.ArticleButtons($(\'articleButtons\'), {'; ?>

            zoomAreas: articleRef.select('div.CTextoOpinion'),
            print_url: '<?php echo $this->_tpl_vars['print_url']; ?>
',
            sendform_url: '<?php echo $this->_tpl_vars['sendform_url']; ?>
',
            title: '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['opinion']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 90, "...") : smarty_modifier_truncate($_tmp, 90, "...")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
'

        <?php echo '});

        // Show buttons
        /* $(\'articleButtons\').setStyle({display: \'inline\'}); */
    }
});
/* ]]> */
</script>
'; ?>
