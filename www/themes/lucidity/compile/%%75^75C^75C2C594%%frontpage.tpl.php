<?php /* Smarty version 2.6.18, created on 2010-03-03 22:28:29
         compiled from frontpage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'renderplaceholder', 'frontpage.tpl', 41, false),)), $this); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "module_head.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <body>
        
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_top.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

        <div class="wrapper clearfix">
            <div class="container clearfix span-24">

                <div id="header" class="">

                   <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "frontend_header.tpl", 'smarty_include_vars' => array()));
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

                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_headlines.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                    <div class="span-24">
                        <div class="layout-column first-column span-8">
                            <div>
                            
                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['destaca'],'tpl' => 'frontpage_article_head.tpl','placeholder' => 'placeholder_0_0'), $this);?>


                                <hr class="new-separator"/>
                                
                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article.tpl','placeholder' => 'placeholder_0_1'), $this);?>


                                
                                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_button.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                                
                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article.tpl','placeholder' => 'placeholder_0_2'), $this);?>

                                
                            </div>
                        </div>
                        <div class="layout-column middle-column span-8">
                            <div class="border-dotted">

                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['destaca'],'tpl' => 'frontpage_article_head.tpl','placeholder' => 'placeholder_1_0'), $this);?>


                                <hr class="new-separator"/>

                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article.tpl','placeholder' => 'placeholder_1_1'), $this);?>


                                
                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article.tpl','placeholder' => 'placeholder_1_2'), $this);?>


                            </div>
                        </div>
                        <div class="layout-column last-column last span-8">
                            <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['destaca'],'tpl' => 'frontpage_article_head.tpl','placeholder' => 'placeholder_2_0'), $this);?>


                            <hr class="new-separator"/>

                            <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article.tpl','placeholder' => 'placeholder_2_1'), $this);?>


                            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_lateral.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                            <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article.tpl','placeholder' => 'placeholder_2_2'), $this);?>

                        </div>

                    </div>
                    <hr class="new-separator">
                    <div id="quick-news" class="span-24">

                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_social.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                    </div>

                    <hr class="news-separator">

                    <div class="span-24">
                        <div class="layout-column first-column span-8">
                            <div>

                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article.tpl','placeholder' => 'placeholder_0_3'), $this);?>


                                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_express.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                                
                            </div>

                        </div>
                        <div class="layout-column middle-column span-8">
                            <div class="border-dotted">
                                <div class="layout-column last-column last span-8">
                                    <div class="border-dotted">

                                        <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article.tpl','placeholder' => 'placeholder_1_3'), $this);?>


                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="layout-column last-column last span-8">
                            <div class="border-dotted">
                                <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'frontpage_article.tpl','placeholder' => 'placeholder_2_3'), $this);?>

                            </div>
                            <div id="facebook"> <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
/facebook.png" alt="También en Facebook" />
                                                          </div>
                        </div>

                    </div>
                    <hr class="news-separator" />

                    <div class="span-24">
                        <div class="layout-column first-column span-8">
                            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_video.tpl", 'smarty_include_vars' => array('type_video' => 'youtube')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        </div>
                        <div class="layout-column middle-column span-8">
                            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_galery.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

                            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_button.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        </div>
                        <div class="layout-column last-column last span-8">
                            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_video.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        </div>
                    </div>

                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "module_other_headlines.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                </div><!-- fin #main_content -->

            </div><!-- fin .container -->
        </div><!-- fin .wrapper -->

        <div class="wrapper clearfix">

            <div class="container clearfix span-24">
                <div id="footer" class="">

                </div><!-- fin .footer -->

            </div><!-- fin .container -->

        </div>
    </body>
</html>