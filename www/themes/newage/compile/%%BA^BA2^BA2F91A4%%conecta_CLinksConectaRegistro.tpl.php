<?php /* Smarty version 2.6.18, created on 2010-01-26 01:11:30
         compiled from conecta_CLinksConectaRegistro.tpl */ ?>
<div class="CLinksConectaRegistro">
    <div class="fileteHorizontalPC"></div>
    <div class="menuZonaRegistro">
    <?php if (! isset ( $_SESSION['pc_user'] )): ?>
        <a href="/conecta/faq/" title="Preguntas frecuentes sobre Conect@">Qué es Conect@</a> | 
        <a href="/conecta/" title="Entrar en Conect@">Ver Portada Conect@</a> |
        <a href="/conecta/login/" title="Entrar en Conect@">Participar en Conect@</a> |
        <a href="/conecta/rexistro/" title="Registrarse como miembro de Conect@">Registrarse</a>
    <?php else: ?>
        <a href="/conecta/faq/" title="Preguntas frecuentes sobre Conect@">Qué es Conect@</a> |
        <a href="/conecta/envio/" title="Salir de Conect@">Participar Conect@</a> |
        <a href="/conecta/boletin/" title="Salir de Conect@">Subscripci&oacute;n Boletín</a> |
        <a href="/conecta/perfil/" title="Salir de Conect@">Perfil de Conect@</a> |
        <a href="/conecta/logout/" title="Salir de Conect@">Salir de Conect@</a>
    <?php endif; ?>
    </div>
    <div class="fileteHorizontalPC"></div>
</div>