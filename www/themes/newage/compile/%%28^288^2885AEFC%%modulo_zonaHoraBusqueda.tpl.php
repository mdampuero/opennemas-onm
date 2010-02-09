<?php /* Smarty version 2.6.18, created on 2010-01-26 21:22:22
         compiled from modulo_zonaHoraBusqueda.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'modulo_zonaHoraBusqueda.tpl', 12, false),array('insert', 'time', 'modulo_zonaHoraBusqueda.tpl', 43, false),)), $this); ?>
<?php if (! empty ( $this->_tpl_vars['categories'][$this->_tpl_vars['posmenu']]['subcategories'] )): ?>
    <div class="zonaHoraBusqueda zonaHoraBusquedaSec">
        <div class="zonaHoraFecha">
            <div class="time-box">
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_categoriesMenu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            </div>          
            <div class="rss-box">
                <a href="/rss/<?php if (( ! empty ( $this->_tpl_vars['category_name'] ) && $this->_tpl_vars['category_name'] != 'home' )): ?><?php echo $this->_tpl_vars['category_name']; ?>
/<?php endif; ?><?php if (! empty ( $this->_tpl_vars['subcategory_name'] )): ?><?php echo $this->_tpl_vars['subcategory_name']; ?>
/<?php endif; ?>"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
rss-icon.gif" alt="RSS" /></a>
                
                <?php if (( $this->_tpl_vars['category_name'] != 'home' ) && ! isset ( $this->_tpl_vars['author_name'] )): ?>
                    <a href="/rss/<?php echo $this->_tpl_vars['category_name']; ?>
/<?php if (! empty ( $this->_tpl_vars['subcategory_name'] )): ?><?php echo $this->_tpl_vars['subcategory_name']; ?>
/<?php endif; ?>">
                        &nbsp;<?php echo ((is_array($_tmp=@$this->_tpl_vars['subcategory_real_name'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['category_real_name']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['category_real_name'])); ?>
&nbsp;</a>
                <?php elseif (isset ( $this->_tpl_vars['author_name'] )): ?>
                    <a href="/rss/<?php echo $this->_tpl_vars['category_name']; ?>
/<?php echo $this->_tpl_vars['author_id']; ?>
/">&nbsp;<?php echo $this->_tpl_vars['author_name']; ?>
&nbsp;</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="zonaBusquedaBarraHora zonaBusquedaBarraHoraSec">
	    <div style="float:right" class="containerBusqueda">
            <form action="/search.php" id="cse-search-box">
                <input type="hidden" name="cx" value="partner-pub-4524925515449269:kfaqom-99at" />
                <input type="hidden" name="cof" value="FORID:10" />
                <input type="hidden" name="ie" value="UTF-8" />
                <div class="elemMenuBarraFecha">Buscar en:</div>
                <div class="cajaBusqueda"><input class="textoABuscar" name="q" type="text" /></div>
                <div class="destinoBusqueda">
                    <div class="radioBusqueda"><input type="radio" name="destino" value="xornal" checked="checked" onclick="cx.value='partner-pub-4524925515449269:kfaqom-99at'" /></div>
                    <div class="dondeBuscar">OpenNemas</div>
                </div>
                <div class="destinoBusqueda">
                    <div class="radioBusqueda"><input type="radio" name="destino" value="google" onclick="cx.value='partner-pub-4524925515449269:l5xds69cix0'" /></div>
                    <div class="dondeBuscar">Google&nbsp;</div>
                </div>
                <script type="text/javascript" src=" http://www.google.es/coop/cse/brand?form=cse-search-box&lang=es"></script>
            </form>
	    </div>
        </div>
    </div>
<?php else: ?>
    <div class="zonaHoraBusqueda">
        <div class="zonaHoraFecha">
            <div class="time-box">
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'time')), $this); ?>

            </div>
                        
            <?php if ($this->_tpl_vars['category_name'] != 'kiosko'): ?>
                <div class="rss-box">
                    <a href="/rss/<?php if (( ! empty ( $this->_tpl_vars['category_name'] ) && $this->_tpl_vars['category_name'] != 'home' )): ?><?php echo $this->_tpl_vars['category_name']; ?>
/<?php endif; ?><?php if (! empty ( $this->_tpl_vars['subcategory_name'] )): ?><?php echo $this->_tpl_vars['subcategory_name']; ?>
/<?php endif; ?>"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
rss-icon.gif" alt="RSS" /></a>

                    <?php if (( $this->_tpl_vars['category_name'] != 'home' ) && ! isset ( $this->_tpl_vars['author_name'] )): ?>
                        <a href="/rss/<?php echo $this->_tpl_vars['category_name']; ?>
/<?php if (! empty ( $this->_tpl_vars['subcategory_name'] )): ?><?php echo $this->_tpl_vars['subcategory_name']; ?>
/<?php endif; ?>">
                            &nbsp;<?php echo ((is_array($_tmp=@$this->_tpl_vars['subcategory_real_name'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['category_real_name']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['category_real_name'])); ?>
&nbsp;</a>
                    <?php elseif (isset ( $this->_tpl_vars['author_name'] )): ?>
                        <a href="/rss/<?php echo $this->_tpl_vars['category_name']; ?>
/<?php echo $this->_tpl_vars['author_id']; ?>
/">&nbsp;<?php echo $this->_tpl_vars['author_name']; ?>
&nbsp;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if (! is_null ( $this->_tpl_vars['frontpage_newspaper_pdf'] )): ?>
            <div class="box-portada">
                <strong><a href="<?php echo @SITE_URL; ?>
media/files/kiosko/<?php echo $this->_tpl_vars['frontpage_newspaper_pdf']; ?>
" target="_blank" title="Primera página de la versión impresa">Portada de la versión impresa</a></strong>
            </div>
            <?php endif; ?>                
        </div>
        <div class="zonaBusquedaBarraHora" style="width: 440px;">
            <div style="float:right" class="containerBusqueda">
                <form action="/search.php" id="cse-search-box">
                    <input type="hidden" name="cx" value="partner-pub-4524925515449269:kfaqom-99at" />
                    <input type="hidden" name="cof" value="FORID:10" />
                    <input type="hidden" name="ie" value="UTF-8" />
                    <div class="elemMenuBarraFecha">Buscar en:</div>
                    <div class="cajaBusqueda"><input class="textoABuscar" name="q" type="text" /></div>
                    <div class="destinoBusqueda">
                        <div class="radioBusqueda"><input type="radio" name="destino" value="xornal" checked="checked" onclick="cx.value='partner-pub-4524925515449269:kfaqom-99at'" /></div>
                        <div class="dondeBuscar">OpenNemas</div>
                    </div>
                    <div class="destinoBusqueda">
                        <div class="radioBusqueda"><input type="radio" name="destino" value="google" onclick="cx.value='partner-pub-4524925515449269:l5xds69cix0'" /></div>
                        <div class="dondeBuscar">Google&nbsp;</div>
                    </div>
                </form>
            </div>
        </div>  
    </div>
<?php endif; ?>