{if isset($smarty.session.email)}
<div class="profile">
    <div class="profile-pic">
        <img src="{gravatar email=$smarty.session.email default="/themes/xornal/images/planConecta/user-info80x80.png"}" />
    </div>

    <div class="profile-info">
        <a href="/conecta/perfil/" title="Editar perfil de usuario Conect@"><strong>{$smarty.session.nick}</strong></a>
        <br />
        
        {$smarty.session.name} {$smarty.session.firstname} {$smarty.session.lastname}<br />
    </div>
    <div class="clearer"></div>
    
    <div class="rightSide">
        <br />
        <a href="/conecta/logout/" onclick="CommentForm.logoutConecta(); return false;">
            <img src="{$params.IMAGE_DIR}planConecta/sair.gif" border="0" align="absmiddle" /> Cerrar sesión</a>
    </div>
</div>
{/if}