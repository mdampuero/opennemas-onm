<?php /* Smarty version 2.6.18, created on 2010-04-22 23:40:21
         compiled from frontpage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'intersticial', 'frontpage.tpl', 19, false),array('function', 'renderplaceholder', 'frontpage.tpl', 38, false),)), $this); ?>



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

            <div id="main_content" class="span-24">
                <div class="span-24">
                    <div class="layout-column first-column span-12">
                        <div>
                            <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article_head.tpl','placeholder' => 'placeholder_0_0'), $this);?>

                            <div class="span-12">
                                <div class="span-6">

                                    <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article_big.tpl','placeholder' => 'placeholder_0_1'), $this);?>


                                    <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article_little.tpl','placeholder' => 'placeholder_0_2'), $this);?>


                                </div>
                                <div class="span-6 last">
                                    <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article_big.tpl','placeholder' => 'placeholder_1_1'), $this);?>


                                    <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article_little.tpl','placeholder' => 'placeholder_1_2'), $this);?>


                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="layout-column middle-column span-5 border-dotted">
                        <div>
                            <div class="author-highlighted">
                                <h3 >Autores destacados</h3>
                                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "frontpage_article_author.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                            </div>

                            <hr class="new-separator" />

                            <div class="middle-column-frontpage">
                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article_middle_big.tpl','placeholder' => 'placeholder_2_1'), $this);?>


                                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_adsense.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article_middle_little.tpl','placeholder' => 'placeholder_2_2'), $this);?>

                                
                            </div>

                        </div>
                    </div><!-- fin -->
                    <div class="layout-column last-column last span-7">
                      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_search.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_frontend_video.tpl", 'smarty_include_vars' => array('video' => $this->_tpl_vars['video'][1])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                       
                      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_button.tpl", 'smarty_include_vars' => array('type' => '3')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_headlines.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    </div>

                </div>
 
                <hr class="new-separator">

                <div class="span-24">
                       <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_separator.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                </div>

                <div class="span-24">
                    <div class="layout-column first-column span-12">
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_gallery.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    </div>
                    <div class="layout-column last-column last span-12">
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_video.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    </div>

                </div>
                <?php if ($this->_tpl_vars['category_name'] == 'home'): ?>
                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "module_other_headlines.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                <?php endif; ?>
               
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
                 $("#tabs2").tabs();
            });
        </script>
    '; ?>

  </body>
</html>
	