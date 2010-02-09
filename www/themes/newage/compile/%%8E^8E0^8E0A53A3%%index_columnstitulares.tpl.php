<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:47
         compiled from index_columnstitulares.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'index_columnstitulares.tpl', 13, false),array('modifier', 'clearslash', 'index_columnstitulares.tpl', 14, false),)), $this); ?>
<div class="col1Titulares">
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Pol&iacute;tica
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['titulares_polItica']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_polItica'][$this->_sections['c']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
h | </span>&nbsp;
              <span class="textoTitularDia"><a href="<?php echo $this->_tpl_vars['titulares_polItica'][$this->_sections['c']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_polItica'][$this->_sections['c']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></span>
              </div>
          </div>
        <?php endfor; endif; ?>
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Galicia
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['titulares_galicia']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_galicia'][$this->_sections['c']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
h | </span>&nbsp;
              <span class="textoTitularDia"><a href="<?php echo $this->_tpl_vars['titulares_galicia'][$this->_sections['c']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_galicia'][$this->_sections['c']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></span>
              </div>
          </div>
        <?php endfor; endif; ?>
        </div>
    </div>
<br/>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Mundo
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['titulares_mundo']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_mundo'][$this->_sections['c']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
h | </span>&nbsp;
              <span class="textoTitularDia"><a href="<?php echo $this->_tpl_vars['titulares_mundo'][$this->_sections['c']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_mundo'][$this->_sections['c']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></span>
              </div>
          </div>
        <?php endfor; endif; ?>
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>gente
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=4) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_gente'][$this->_sections['c']['index']]->created)) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
h | </span>&nbsp;
              <span class="textoTitularDia"><a href="<?php echo $this->_tpl_vars['titulares_gente'][$this->_sections['c']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_gente'][$this->_sections['c']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></span>
              </div>
          </div>
        <?php endfor; endif; ?>
        </div>
    </div>
</div>
<div class="col2Titulares">
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Espa&ntilde;a
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['titulares_espana']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_espana'][$this->_sections['c']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
h | </span>&nbsp;
              <span class="textoTitularDia"><a href="<?php echo $this->_tpl_vars['titulares_espana'][$this->_sections['c']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_espana'][$this->_sections['c']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></span>
              </div>
          </div>
        <?php endfor; endif; ?>
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>econom&iacute;a
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['titulares_economia']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_economia'][$this->_sections['c']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
h | </span>&nbsp;
              <span class="textoTitularDia"><a href="<?php echo $this->_tpl_vars['titulares_economia'][$this->_sections['c']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_economia'][$this->_sections['c']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></span>
              </div>
          </div>
        <?php endfor; endif; ?>
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>cultura
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['titulares_cultura']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_cultura'][$this->_sections['c']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
h | </span>&nbsp;
              <span class="textoTitularDia"><a href="<?php echo $this->_tpl_vars['titulares_cultura'][$this->_sections['c']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_cultura'][$this->_sections['c']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></span>
              </div>
          </div>
        <?php endfor; endif; ?>
        </div>
    </div>
    <div class="grupoTitularesDia">
        <div class="contPestanyaTitularesDia">
            <div class="pestanyaTitulares">
            <div class="flechaPestanyaTitulares"></div>Sociedad
            </div>
            <div class="cierrePestanyaTitulares"></div>
        </div>
        <div class="listaTitularesDia">
        <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=4) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
          <div class="contTitularDia">
              <div class="contHoraLugarTitularDia">
              <span class="horaTitularDia"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_sociedad'][$this->_sections['c']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
h | </span>&nbsp;
              <span class="textoTitularDia"><a href="<?php echo $this->_tpl_vars['titulares_sociedad'][$this->_sections['c']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_sociedad'][$this->_sections['c']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></span>
              </div>
          </div>
        <?php endfor; endif; ?>
        </div>
    </div>
    </div>