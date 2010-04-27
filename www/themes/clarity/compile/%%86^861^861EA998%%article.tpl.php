<?php /* Smarty version 2.6.18, created on 2010-04-23 10:02:48
         compiled from article.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'intersticial', 'article.tpl', 17, false),array('modifier', 'clearslash', 'article.tpl', 35, false),array('modifier', 'escape', 'article.tpl', 57, false),array('function', 'articledate', 'article.tpl', 41, false),array('function', 'renderTypeRelated', 'article.tpl', 70, false),)), $this); ?>
 

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "module_head.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'intersticial', 'type' => '50')), $this); ?>


    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_top.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <div class="wrapper clearfix">
        <div class="container clearfix span-24">

            <div id="header" class="clearfix span-24">

                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "frontend_logo.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "frontend_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

            </div>

            <div id="main_content" class="single-article span-24">
                <div class="in-big-title span-24">
                    <?php if (! empty ( $this->_tpl_vars['article']->title_int )): ?>
                        <h1><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title_int)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</h1>
                    <?php else: ?>
                       <h1><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</h1>
                    <?php endif; ?>
                    <p class="in-subtitle"><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->subtitle)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</p>
                    <div class="info-new">
                        <span class="author"><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->agency)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</span> - <span class="place">Santiago de Compostela</span> - <span class="publish-date"><?php echo smarty_function_articledate(array('article' => $this->_tpl_vars['article'],'updated' => $this->_tpl_vars['article']->changed), $this);?>
</span>
                    </div>
                </div><!-- fin lastest-news -->

                <div class="span-24">
                    <div class="layout-column first-column span-16">

                            <div class="span-16 toolbar">
                                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ratings.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_utilities.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                            </div><!--fin toolbar -->
                            <div class="content-article">
                                <div class="main-photo">
                                     <?php if ($this->_tpl_vars['photoInt']->name): ?>
                                         <img src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
<?php echo $this->_tpl_vars['photoInt']->path_file; ?>
<?php echo $this->_tpl_vars['photoInt']->name; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->img2_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->img2_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                                    
                                        <div class="photo-subtitle">
                                               <span class="photo-autor"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->img2_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span>
                                        </div>
                                     <?php endif; ?>
                                </div>
                                  <?php if (! empty ( $this->_tpl_vars['relationed'] )): ?>                                
                                      <div class="related-news-embebed span-5">
                                         <p class="title">Noticias relacionadas:</p>
                                         <ul>
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
                                                <?php if ($this->_tpl_vars['relationed'][$this->_sections['r']['index']]->pk_article != $this->_tpl_vars['article']->pk_article): ?>
                                                  <li> <?php echo smarty_function_renderTypeRelated(array('content' => $this->_tpl_vars['relationed'][$this->_sections['r']['index']]), $this);?>
</li>
                                                <?php endif; ?>
                                            <?php endfor; endif; ?>
                                        </ul>
                                     </div>
                                <?php endif; ?>
                                <p><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->body)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>

                                </p>
                              
                               
                            </div><!-- /content-article -->

                            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ratings.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                            <hr class="new-separator"/>

                            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_utilities_bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                            <hr class="new-separator"/>
                            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_robapagina.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                             <hr class="new-separator"/>
                            <div class="more-news-bottom-article">                                                          
                                <?php if (! empty ( $this->_tpl_vars['suggested'] )): ?>
                                    <p class="title">Si le interesó esta noticia, eche un vistazo a estas:</p>
                                     <ul>
                                        <?php unset($this->_sections['r']);
$this->_sections['r']['name'] = 'r';
$this->_sections['r']['loop'] = is_array($_loop=$this->_tpl_vars['suggested']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                                            <?php if ($this->_tpl_vars['suggested'][$this->_sections['r']['index']]['pk_content'] != $this->_tpl_vars['article']->pk_article): ?>
                                               <li><a href="<?php echo $this->_tpl_vars['suggested'][$this->_sections['r']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['suggested'][$this->_sections['r']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></li>
                                            <?php endif; ?>
                                        <?php endfor; endif; ?>
                                    </ul>
                                <?php endif; ?>
                            </div><!--fin more-news-bottom-article -->

                           <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "module_comments.tpl", 'smarty_include_vars' => array('content' => $this->_tpl_vars['article'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        
                    </div>

                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "article_last_column.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                </div>
            </div><!-- fin #main_content -->
  
        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->

    <div class="wrapper clearfix">

        <div class="container clearfix span-24">
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "frontend_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

        </div><!-- fin .container -->

    </div>
   
    <?php echo '
        <script type="text/javascript">
            jQuery(document).ready(function(){
                $("#tabs").tabs();
                $lock=false;
			jQuery("div.share-actions").hover(
			  function () {
				if (!$lock){
				  $lock=true;
				  jQuery(this).children("ul").fadeIn("fast");
				}
				$lock=false;
			  },
			  function () {
				if (!$lock){
				  $lock=true;
				  jQuery(this).children("ul").fadeOut("fast");
				}
				$lock=false;
			  }
			);
            });
        </script>
    '; ?>

</body>
</html>