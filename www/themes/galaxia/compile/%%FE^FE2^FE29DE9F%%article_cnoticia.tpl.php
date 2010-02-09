<?php /* Smarty version 2.6.18, created on 2010-01-20 17:39:27
         compiled from article_cnoticia.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'breadcrub', 'article_cnoticia.tpl', 9, false),array('function', 'articledate', 'article_cnoticia.tpl', 20, false),array('function', 'typecontent', 'article_cnoticia.tpl', 62, false),array('modifier', 'clearslash', 'article_cnoticia.tpl', 12, false),array('modifier', 'count_words', 'article_cnoticia.tpl', 19, false),array('modifier', 'escape', 'article_cnoticia.tpl', 69, false),array('modifier', 'truncate', 'article_cnoticia.tpl', 118, false),array('insert', 'numComments', 'article_cnoticia.tpl', 35, false),array('insert', 'rating', 'article_cnoticia.tpl', 47, false),)), $this); ?>
<div class="CNoticia">
    <div class="CNoticiaMargen">
        <?php if (! isset ( $this->_tpl_vars['breadcrub'] )): ?>
        <div class="apertura_nota">
            <div class="antetitulo_nota"><?php echo $this->_tpl_vars['category_name']; ?>
 <?php if (! empty ( $this->_tpl_vars['subcategory_name'] )): ?>> <?php echo $this->_tpl_vars['subcategory_name']; ?>
<?php endif; ?></div>
        </div>
        <?php else: ?>
        <div class="apertura_nota">
            <div class="CNoticiaRelacionada"><?php echo smarty_function_breadcrub(array('values' => $this->_tpl_vars['breadcrub']), $this);?>
</div>
        </div>
        <?php endif; ?>
        <h1><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</h1>
        <div class="subtitulo_nota"><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
    </div>
    <div class="clear"></div>
    <div class="CHeaderArticle">
        <div class="superior">
            <div class="authority">
                <span class="author"><?php if (((is_array($_tmp=$this->_tpl_vars['article']->agency)) ? $this->_run_mod_handler('count_words', true, $_tmp) : smarty_modifier_count_words($_tmp)) != '0'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->agency)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
<?php else: ?>Xornal de Galicia<?php endif; ?></span>
                <span class="date"><?php echo smarty_function_articledate(array('article' => $this->_tpl_vars['article'],'created' => $this->_tpl_vars['article']->created,'updated' => $this->_tpl_vars['article']->changed), $this);?>
</span>
            </div>
            <div class="share">
                COMPARTIR NOTICIA:
                <a href="http://chuza.org/submit.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Chuza!"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/chuza.gif" alt="Chuza!" /></a>
                <a href="http://www.facebook.com/share.php?u=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Facebook"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/facebook.png" alt="Facebook" /></a>
                <a href="http://www.twitter.com/home?status=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Twitter"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/twitter.png" alt="Twitter" /></a>
                <a href="http://meneame.net/submit.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Meneame"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/meneame.gif" alt="Meneame"/></a>
                <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Google"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/google.gif" alt="Google" /></a>
                <a href="http://www.digg.com/submit?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Digg"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/digg.gif" alt="Digg" /></a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="inferior">
            <div class="tools">
                <span class="comments"><?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'numComments', 'id' => $this->_tpl_vars['article']->id)), $this); ?>
 Comentarios</span>
                <span class="icons" id="articleButtons" style="display: none;">
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
            <a name=" COpina"></a>
            <?php if (( $_REQUEST['category_name'] == 'opinion' )): ?>                
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'rating', 'id' => $this->_tpl_vars['opinion']->id, 'page' => 'article', 'type' => 'vote')), $this); ?>

            <?php else: ?>                 
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'rating', 'id' => $this->_tpl_vars['article']->id, 'page' => 'article', 'type' => 'vote')), $this); ?>

            <?php endif; ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="CNoticiaMargen">
        <div class="CContenedorMenuNota"></div>
        <?php echo '<style>a {color: #004B8E; text-decoration:none;font-weight:700;} a:hover{color: #004B8E; text-decoration:underline;font-weight:700;}</style>'; ?>

        <?php if (! empty ( $this->_tpl_vars['relationed'] )): ?>
        <div class="CRelated">
            <?php unset($this->_sections['r']);
$this->_sections['r']['name'] = 'r';
$this->_sections['r']['loop'] = is_array($_loop=$this->_tpl_vars['relationed']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['r']['show'] = true;
$this->_sections['r']['max'] = $this->_sections['r']['loop'];
$this->_sections['r']['step'] = 1;
$this->_sections['r']['start'] = $this->_sections['r']['step'] > 0 ? 0 : $this->_sections['r']['loop']-1;
if ($this->_sections['r']['show']) {
    $this->_sections['r']['total'] = $this->_sections['r']['loop'];
    if ($this->_sections['r']['total'] == 0)
        $this->_sections['r']['show'] = false;
} else
    $this->_sections['r']['total'] = 0;
if ($this->_sections['r']['show']):

            for ($this->_sections['r']['index'] = $this->_sections['r']['start'], $this->_sections['r']['iteration'] = 1;
                 $this->_sections['r']['iteration'] <= $this->_sections['r']['total'];
                 $this->_sections['r']['index'] += $this->_sections['r']['step'], $this->_sections['r']['iteration']++):
$this->_sections['r']['rownum'] = $this->_sections['r']['iteration'];
$this->_sections['r']['index_prev'] = $this->_sections['r']['index'] - $this->_sections['r']['step'];
$this->_sections['r']['index_next'] = $this->_sections['r']['index'] + $this->_sections['r']['step'];
$this->_sections['r']['first']      = ($this->_sections['r']['iteration'] == 1);
$this->_sections['r']['last']       = ($this->_sections['r']['iteration'] == $this->_sections['r']['total']);
?>
                <?php if ($this->_tpl_vars['relationed'][$this->_sections['r']['index']]->pk_article != $this->_tpl_vars['article']->pk_article && $this->_tpl_vars['relationed'][$this->_sections['r']['index']]->title): ?>
                    <!-- TITULAR RECOMENDACION-->           
                     <?php echo smarty_function_typecontent(array('content' => $this->_tpl_vars['relationed'][$this->_sections['r']['index']],'view_date' => '1'), $this);?>

                <?php endif; ?>
             <?php endfor; endif; ?>
        </div>
        <?php endif; ?>
        <div class="cuerpo_article">            
            <?php if ($this->_tpl_vars['photoInt']->name): ?>
                <img style="display:none;" src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['photoInt']->path_file; ?>
<?php echo $this->_tpl_vars['photoInt']->name; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->img2_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
            <?php endif; ?>            
            <?php if ($this->_tpl_vars['photoExt']->name): ?>
                <img style="display:none;" src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['photoExt']->path_file; ?>
<?php echo $this->_tpl_vars['photoExt']->name; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->img1_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
            <?php endif; ?>
            <?php echo ((is_array($_tmp=$this->_tpl_vars['article']->body)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>

        </div>
	  <?php if ($this->_tpl_vars['videoInt']): ?>
	  <div style="text-align: center;">
        <object width="434" height="320">
        <param name="movie" value="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videoInt']; ?>
&hl=es&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
        <embed src="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videoInt']; ?>
&hl=es&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="434" height="320"></embed></object>
      </div><br />
	  <?php endif; ?>
    <div class="CFooterArticle">
        <div class="CTextoCompartir">Si te gusta Xornal.com, comp&aacute;rtenos con tus amigos.<br />Disfruta de la libertad de expresi&oacute;n.</div>
        <div class="share_right">
            <a href="http://chuza.org/submit.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Chuza!"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/chuza.gif" alt="Chuza!" /></a>
            <a href="http://www.facebook.com/share.php?u=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Facebook"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/facebook.png" alt="Facebook" /></a>
            <a href="http://www.twitter.com/home?status=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Twitter"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/twitter.png" alt="Twitter" /></a>
            <a href="http://meneame.net/submit.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Meneame"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/meneame.gif" alt="Meneame"/></a>
            <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Google"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/google.gif" alt="Google" /></a>
            <a href="http://www.digg.com/submit?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Digg"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/digg.gif" alt="Digg" /></a>
            <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Yahoo!"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/yahoo.gif" alt="Yahoo!"/></a>
            <a href="http://www.stumbleupon.com/refer.php?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Stumble"><img alt="Stumble" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/stumble.gif"/></a>
            <a href="http://del.icio.us/post?url=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="del.icio.us"><img alt="del.icio.us" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/delicious.gif"/></a>
            <a href="http://www.technorati.com/faves?add=http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="Technorati"><img alt="Technorati" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
enviarA/technorati.gif"/></a>
        </div>
                        <div class="clear"></div>
        <div class="CTextoNotaEnviarA">Nota: es posible que tengas que estar registrado y autentificado en estos servicios para poder anotar el contenido correctamente.</div>
     </div>
  </div>
</div>
<?php echo '
<script type="text/javascript">
/* <![CDATA[ */
document.observe(\'dom:loaded\', function() {
    if($(\'articleButtons\')) {
        var articleRef = $(\'articleButtons\').up(4);
        new OpenNeMas.ArticleButtons($(\'articleButtons\'), {'; ?>

            zoomAreas: articleRef.select('div.cuerpo_article, div.subtitulo_nota'),
            print_url: '<?php echo $this->_tpl_vars['print_url']; ?>
',
            sendform_url: '<?php echo $this->_tpl_vars['sendform_url']; ?>
',
            title: '<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 90, "...") : smarty_modifier_truncate($_tmp, 90, "...")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
'

        <?php echo '});

        // Show buttons
        /* $(\'articleButtons\').setStyle({display: \'inline\'}); */
    }
});
/* ]]> */
</script>
'; ?>