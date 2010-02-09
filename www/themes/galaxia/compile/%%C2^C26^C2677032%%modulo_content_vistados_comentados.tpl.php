<?php /* Smarty version 2.6.18, created on 2010-01-20 17:39:27
         compiled from modulo_content_vistados_comentados.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'modulo_content_vistados_comentados.tpl', 38, false),)), $this); ?>
    <!-- ********************* PESTAÑAS ************************* -->
    <div class="containerNoticiasMasVistasYValoradas">
        <!-- *************** PESTANYAS **************** -->
        <div class="zonaPestanyas">
            <div class="pestanyaON" id="pestanha0">
                <div class="pestanya">
                    <div class="flechaPestanya"></div>
                    <?php if (preg_match ( '/opinion\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
                        <div class="textoPestanya" onclick="<?php echo 'get_plus_content(\'Opinion\',{container:\'pestanha0\'});'; ?>
">Opiniones + vistas</div>
                    <?php else: ?>
                        <div class="textoPestanya" onclick="<?php echo 'get_plus_content(\'Article\',{container:\'pestanha0\',category:\''; ?>
<?php echo $this->_tpl_vars['article']->category; ?>
<?php echo '\'});'; ?>
">Noticias + vistas</div>
                    <?php endif; ?>
                </div>
                <div class="cierrePestanya"></div>
            </div>

            <div class="espacioInterPestanyas"></div>

            <div class="pestanyaOFF" id="pestanha1">
                <div class="pestanya">
                    <div class="flechaPestanya"></div>
                    <?php if (preg_match ( '/opinion\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
                        <div class="textoPestanya" onclick="<?php echo 'get_plus_content(\'Opinion\',{container:\'pestanha1\',author:\''; ?>
<?php echo $this->_tpl_vars['opinion']->fk_author; ?>
<?php echo '\'});'; ?>
">+ vistas del autor</div>
                    <?php else: ?>
                        <div class="textoPestanya" onclick="<?php echo 'get_plus_content(\'Comment\',{container:\'pestanha1\',category:\''; ?>
<?php echo $this->_tpl_vars['article']->pk_fk_content_category; ?>
<?php echo '\'});'; ?>
">Noticias + comentadas</div>
                    <?php endif; ?>
                </div>
                <div class="cierrePestanya"></div>
            </div>
        </div>
        <!-- ************* LISTA DE NOTICIAS ********** -->
        <div id="div_articles_viewed" class="CListaNoticiasMas">
            <!-- TITULAR RECOMENDACION-->
            <?php unset($this->_sections['view']);
$this->_sections['view']['name'] = 'view';
$this->_sections['view']['loop'] = is_array($_loop=$this->_tpl_vars['articles_viewed']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['view']['show'] = true;
$this->_sections['view']['max'] = $this->_sections['view']['loop'];
$this->_sections['view']['step'] = 1;
$this->_sections['view']['start'] = $this->_sections['view']['step'] > 0 ? 0 : $this->_sections['view']['loop']-1;
if ($this->_sections['view']['show']) {
    $this->_sections['view']['total'] = $this->_sections['view']['loop'];
    if ($this->_sections['view']['total'] == 0)
        $this->_sections['view']['show'] = false;
} else
    $this->_sections['view']['total'] = 0;
if ($this->_sections['view']['show']):

            for ($this->_sections['view']['index'] = $this->_sections['view']['start'], $this->_sections['view']['iteration'] = 1;
                 $this->_sections['view']['iteration'] <= $this->_sections['view']['total'];
                 $this->_sections['view']['index'] += $this->_sections['view']['step'], $this->_sections['view']['iteration']++):
$this->_sections['view']['rownum'] = $this->_sections['view']['iteration'];
$this->_sections['view']['index_prev'] = $this->_sections['view']['index'] - $this->_sections['view']['step'];
$this->_sections['view']['index_next'] = $this->_sections['view']['index'] + $this->_sections['view']['step'];
$this->_sections['view']['first']      = ($this->_sections['view']['iteration'] == 1);
$this->_sections['view']['last']       = ($this->_sections['view']['iteration'] == $this->_sections['view']['total']);
?>
            <div class="CNoticiaMas">
                <div class="CContainerIconoTextoNoticiaMas">
                    <div class="iconoNoticiaMas"></div>
                    <div class="textoNoticiaMas"><a href="<?php echo $this->_tpl_vars['articles_viewed'][$this->_sections['view']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['articles_viewed'][$this->_sections['view']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
                </div>
                <div class="fileteNoticiaMas"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>
            </div>
            <?php endfor; endif; ?>
        </div>
    <!-- ************ PAGINADO ************** -->
    </div>