<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:48
         compiled from advertisement.tpl */ ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>

<title>:: Publicidad Xornal.com ::</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />


<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
prototype.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
scriptaculous/effects.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
intersticial.js"></script>

<?php echo '
<style type="text/css">
body {
    background-color: transparent;
    margin: 0;
    border: 0;
    padding: 0;
}
</style>
'; ?>

</head>
<body>
<?php if ($this->_tpl_vars['banner']->with_script): ?>
    <?php echo $this->_tpl_vars['banner']->script; ?>

<?php endif; ?>

</body>
</html>