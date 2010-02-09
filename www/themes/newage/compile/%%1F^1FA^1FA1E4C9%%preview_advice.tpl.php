<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:47
         compiled from preview_advice.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'preview_advice.tpl', 27, false),)), $this); ?>
<?php if (isset ( $_SESSION['userid'] ) && ! empty ( $_SESSION['userid'] )): ?>
<link rel="stylesheet" type="text/css" href="./admin/themes/default/css/datepicker.css"/>

<script type="text/javascript" src="./admin/themes/default/js/prototype-date-extensions.js"></script>
<script type="text/javascript" src="./admin/themes/default/js/datepicker.js"></script>

<form id="preview_form" action="#" method="post">
    <div id="preview_layout" style="border:1px solid #969; padding:10px; background-color:#FFE9AF; color:#666; font-size:1.1em; font-weight:bold; width:500px; position:fixed; left:0; top:0;">
        <div>                        
            <button type="submit" title="Refrescar portada" style="float: right;">
                <img src="./admin/themes/default/images/template_manager/refresh16x16.png" border="0" align="absmiddle" />
                <strong>Refrescar</strong>
            </button>
            
            <div style="width: 170px; float:right;">
                <input type="text" id="preview_time" name="preview_time" value="<?php echo $_REQUEST['preview_time']; ?>
"
                           maxlength="20" style="width: 160px;"/>
            </div>
            
            <div style="style="float: right;">            
                <img src="./admin/themes/default/images/template_manager/messagebox_warning.png" border="0" align="absmiddle" />
                Modo previsualizaci&oacute;n.
            </div>
        </div>                
    </div>
    
    <input type="hidden" name="id" value="<?php echo ((is_array($_tmp=$_REQUEST['id'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
    <input type="hidden" name="category" value="<?php echo $_REQUEST['category']; ?>
" />
    
    <?php echo '
    <script type="text/javascript">
    /* <![CDATA[ */
    Control.DatePicker.Locale[\'es_ES\'].dateTimeFormat = \'yyyy-MM-dd HH:mm\';
    new Control.DatePicker($(\'preview_time\'), {
        icon: \'./admin/themes/default/images/template_manager/update16x16.png\',
        locale: \'es_ES\',
        timePicker: true,
        timePickerAdjacent: true
    });
    /* ]]> */
    </script>
    '; ?>

</form>
<?php endif; ?>