<div id="formAuthConecta">
    
    <form name="login" id="login" method="post" action="#" >
        
        <h1><img src="{$params.IMAGE_DIR}planConecta/header_planconectafondo.gif" border="0" /></h1>
        <div id="auth_message"></div>
        <dl>
            <dt>
                <label for="email">E-mail:</label>
            </dt>
            <dd>
                <input type="text" name="email" class="required validate-email" value="{$smarty.post.email}" />
            </dd>
            
            <dt>
                <label for="password">Contraseña:</label>
            </dt>
            <dd>
                <input type="password" name="password" class="required" autocomplete="off" />
            </dd>
            
            <dt>&nbsp;</dt>
            <dd class="rightSide">
                <input type="button" onclick="CommentForm.loginConecta(this.form); return false;" value="Entrar en Conect@" />
            </dd>
        </dl>
        
        <input type="hidden" name="action" value="ajax-login" />
        <input type="hidden" name="category_name" value="conecta" />
    </form>
    
    <div class="clearer"></div>
</div>