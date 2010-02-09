<?php /* Smarty version 2.6.18, created on 2010-01-14 02:29:11
         compiled from conecta_CZonaVisionadoMedia.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'conecta_CZonaVisionadoMedia.tpl', 6, false),)), $this); ?>
<div class="CZonaVisionadoMedia">
    <div class="CVisorInfoMediaEncuesta">
        <div class="CContainerInfoMediaEncuesta">
        	<form name="enquisa" method="post" action="#" > 		        
	            <div class="CContainerSeccionVotosInfoMedia"><div class="CSeccionInfoMediaEncuesta"> <?php echo ((is_array($_tmp=$this->_tpl_vars['poll']->subtitle)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
	            		<div class="COtrosInfoMediaEncuesta">Votos: <?php echo $this->_tpl_vars['poll']->total_votes; ?>
</div>
	            		
	            </div>
                <div style="clear:both;"></div>
	            <div class="CTitularEncuesta"><?php echo ((is_array($_tmp=$this->_tpl_vars['poll']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
	            <div class="CZonaRespuestasEncuesta">           
	                 <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['items']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?> 
			                <div class="CRespuestaEncuesta">
			                   <div class="CRadioEncuesta"><input type="radio" value="<?php echo $this->_tpl_vars['items'][$this->_sections['i']['index']]['pk_item']; ?>
" name="respEncuesta" alt="<?php echo $this->_tpl_vars['items'][$this->_sections['i']['index']]['item']; ?>
"/></div>
			                   <div class="CTextoRespuestaEnc"><?php echo $this->_tpl_vars['items'][$this->_sections['i']['index']]['item']; ?>
</div>
			                </div>
			                <div class="separadorHorizontalRespuesta"></div>
	                <?php endfor; endif; ?>
	            </div>
	            
	            <div class="CBotonVotarEncuesta">
	               <input type="hidden" name="op" value='votar'/>          
	       		 <a onClick="document.enquisa.submit()" id="enquisa" >
	           		 <img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
encuestas/botonVotarEncuesta.gif"/>
	            </a></div>
            </form>
            <br />
            <img src="/conecta/enquisa/v<?php echo $this->_tpl_vars['poll']->id; ?>
.png" border="0" title="<?php echo $this->_tpl_vars['poll']->title; ?>
" />

        </div>                 
    </div>
</div>

<?php if ($this->_tpl_vars['poll']->with_comment == '1'): ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_copina.tpl", 'smarty_include_vars' => array('item' => $this->_tpl_vars['poll'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
                        
<div class="zonaClasificacionVideos">
    <div class="zonaPestanyasMedia">
        <!-- PESTANYA -->

        <div class="pestanyaSelecList">
            <div class="contInfoPestanyaGrande">
                <div class="flechaPestanyaSelecList"></div>
                <div class="textoPestanyaSelecList">MÁS ENCUESTAS</div>
            </div>
            <div class="cierrePestanyaSelecList"></div>
        </div>
    </div>
    <div class="listadoMedia" id="div_pc_<?php echo $this->_tpl_vars['accion']; ?>
">        
          <?php $this->assign('polls', $this->_tpl_vars['arrayPolls']); ?>        
          <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['polls']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		   		 <div class="elementoListadoMediaPag">
		            <div class="contSeccionFechaListado">
		                <div class="seccionMediaListado"><a href="/conecta/enquisa/<?php echo $this->_tpl_vars['arrayPolls'][$this->_sections['c']['index']]->id; ?>
.html" style="color:#004B8D;"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrayPolls'][$this->_sections['c']['index']]->subtitle)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
		                <div class="fechaMediaListado"> <?php echo $this->_tpl_vars['polls'][$this->_sections['c']['index']]->changed; ?>
</div>
		            </div>
		            <div class="contTextoElemMediaListado">
		                <div class="textoElemMediaListado">
		                    <a href="/conecta/enquisa/<?php echo $this->_tpl_vars['polls'][$this->_sections['c']['index']]->id; ?>
.html"> <?php echo ((is_array($_tmp=$this->_tpl_vars['polls'][$this->_sections['c']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
		                </div>
		            </div>
		            <div class="fileteIntraMedia"> </div>
		        </div>
         
		  <?php endfor; endif; ?>
    
	    	<div class="posPaginadorGaliciaTitulares">
				<div class="CContenedorPaginado">
					<div class="link_paginador">+ Encuestas</div>
					<div class="CPaginas">
					<?php echo $this->_tpl_vars['pages']->links; ?>
		
					</div>
				</div>
			</div>
	</div>
</div>