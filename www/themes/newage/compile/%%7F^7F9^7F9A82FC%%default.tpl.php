<?php /* Smarty version 2.6.18, created on 2010-01-25 21:39:48
         compiled from boxAuth/default.tpl */ ?>
<div class="CContenedorDatoComentarista">
    <div class="CTextoComentar">Nombre:</div>
    <div class="CContainerDato">
        <input type="text" id="nombre" name="nombre"/>
    </div>
</div>
<div class="CContenedorDatoComentarista">
    <div class="CTextoComentar">Email: (no se visualizará)</div>
    <div class="CContainerDato">
        <input type="text" id="email" name="email"/>
    </div>
</div>
<div class="CContenedorDatoComentarista">
    <div class="CTextoComentar">Introduce la palabra de la imagen</div>
    <div class="CTextoInfoKaptcha">C&oacute;digo de verificaci&oacute;n  para prevenir env&iacute;os autom&aacute;ticos.</div>
    
    <div class="CImagenKaptcha">
        <img alt="kaptcha" src="<?php echo @SITE_URL; ?>
captcha.php?action=captcha&cacheburst=<?php echo time(); ?>" />
    </div>
    
    <div class="CContainerDato"><input type="text" id="security_code" name="security_code"/></div>
</div>

<div class="CContenedorDatoComentarista" id="btnsAuth">
    <div class="CTextoComentar">Identificarse con:</div>
    <div class="CTextoInfoKaptcha">
        <a href="javascript:CommentForm.launchFormConecta();" title="Identificarse en Conect@">
            <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
buttons/social-conecta.gif" border="0" alt=""/></a>
        &nbsp;&nbsp;
        <fb:login-button onlogin="CommentForm.updateFbStatus();"></fb:login-button>
    </div>
</div>