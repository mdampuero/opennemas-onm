<?php /* Smarty version 2.6.18, created on 2010-04-22 13:00:14
         compiled from module_head.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'module_head.tpl', 38, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
screen.css" type="text/css" media="screen, projection">
        <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
print.css" type="text/css" media="print">
        <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
advertisement.css" type="text/css" media="screen,projection">
        <!--[if lt IE 8]><link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
ie.css" type="text/css" media="screen, projection"><![endif]-->

        <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
architecture-v1.css" type="text/css" media="screen,projection">
        <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection">

        <script type="text/javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
jquery-1.4.1.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
jquery-ui.js"></script>
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
functions.js"></script>
        <?php echo '
            <style type="text/css">
                    #main_menu, div.toolbar-bottom a, div.utilities a, .transparent-logo {
                            background-color:#aa0000;
                    }
                    #main_menu{
                      background-color:Black;
                    }
            </style>
        '; ?>

                <meta name="description" content="También arquitectura - su portal de noticias sobre arquitectura, interiorismo y decoración." />
        
        <?php if (preg_match ( '/article\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
            <title><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['category_data']['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
 - <?php echo @SITE_TITLE; ?>
 </title>


            <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
parts/article.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
parts/comments.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
parts/utilities.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
parts/widgets.css" type="text/css" media="screen,projection">


            <script type="text/javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
cufon-yui.js"></script>
            <script type="text/javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
droid.js"></script>
            <?php echo '
                <script type="text/javascript">
                  Cufon.replace(\'#description-category ul li\', {
                  });

                </script>

                <style type="text/css">

                        #logo{
                          height:165px !important;
                          min-height:165px;
                          background-position:top left;
                        }
                        #description-category ul li{

                          color:#016e95; /* cambiar segundo a categoría*/
                        }
                </style>
            '; ?>

 
        <?php endif; ?>


                    </head>
    <body> 