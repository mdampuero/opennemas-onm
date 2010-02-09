<?php /* Smarty version 2.6.18, created on 2010-01-14 01:41:18
         compiled from container_bolsa.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'remotecontent', 'container_bolsa.tpl', 7, false),array('modifier', 'default', 'container_bolsa.tpl', 16, false),array('modifier', 'clearslash', 'container_bolsa.tpl', 16, false),)), $this); ?>
<div class="containerLaBolsa">
    <div class="cabeceraLaBolsa">
        <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
bolsa/logoBolsa.gif" alt="La Bolsa" />
    </div>
    <div class="cuerpoLaBolsa">
                <?php echo smarty_function_remotecontent(array('url' => "http://www.infobolsa.es/mini-ficha/ibex35.htm",'onafter' => 'remotecontent_onafter_infobolsa','cache' => 'true','cachelife' => '30'), $this);?>

    </div>
    <div class="cuerpoPiezaOpinionEconomia">
        <div class="fotoPiezaOpinionEconomia">
            <a href="/opinions/opinions_do_autor/56/Vicente Martin.html" class="contSeccionListadoPortadaPCAuthor">
                <img alt="Vicente Martin" src="/themes/xornal/images/opinion/analisis_vicente_martin.gif"/>
            </a>
        </div>
        <div class="textoPiezaOpinionEconomia"><img alt="" src="/themes/xornal/images/flechitaMenu.gif"/>
            <a href="<?php echo ((is_array($_tmp=@$this->_tpl_vars['opinionVicenteMartin']->permalink)) ? $this->_run_mod_handler('default', true, $_tmp, "#") : smarty_modifier_default($_tmp, "#")); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['opinionVicenteMartin']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
        </div>
    </div>
</div>