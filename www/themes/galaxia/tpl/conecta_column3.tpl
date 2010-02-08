{literal}
<style type="text/css">
.usuario-conecta {
    width: 180px; 
}

.usuario-conecta ul {
    list-style: disc url({/literal}{$params.IMAGE_DIR}{literal}fotoVideoDia/flechitaAzul.gif) inside;
    margin-left: 10px;
    width: 163px;
}

.usuario-conecta li {
    margin-bottom: 2px;
    padding: 4px 0;
    /* border-bottom: 1px dashed #A0BCD4; */
    background-image: url({/literal}{$params.IMAGE_DIR}{literal}fotoVideoDia/fileteDashedDeportesXPress.gif);
    background-position: bottom left;
    background-repeat: no-repeat;
}

.usuario-conecta ul a {
    font-size: 14px;
    color: #004B8E;
}

.usuario-conecta .cabeceraConectaUsuario {
    margin-top: 8px;
    text-align: left;
    font-size: 12px;
}

.usuario-conecta .logoutContainer {
    margin-top: 10px;
    text-align: right;
}

.usuario-conecta .logoutContainer a {
    color: #004B8E;
}
</style>
{/literal}

<div class="column3">
    
    {if isset($smarty.session.pc_user)}
        <div class="usuario-conecta">
            
            <img src="{$params.IMAGE_DIR}planConecta/header_planconectafondo.gif" border="0" />                
            
            <p class="cabeceraConectaUsuario">
                Sesión iniciada con nick: <a href="/conecta/perfil/" title="Ver mi perfíl">{$smarty.session.nameuser}</a>
            </p>
            
            <ul>                    
                <li><a href="/conecta/perfil/" title="Ver Perfíl">Editar perfíl</a></li>
                 <li><a href="/conecta/envio/" title="Participar">Partipar en Conect@</a></li>
                {* <li><a href="/conecta/envio/" title="Enviar Noticia">Enviar noticia</a></li> *}
                <li><a href="/conecta/cambio/" title="Cambiar contraseña">Cambiar contraseña</a></li>
                <li><a href="/conecta/boletin/" title="Suscripción boletín">Suscripción boletín</a></li>
            </ul>
            
            <div class="logoutContainer">[ <a href="/conecta/logout/" title="Salir de Conect@">Cerrar Sesión</a> ]</div>
            
            {* <img src="{$params.IMAGE_DIR}planConecta/sair.png" border="0" align="right" /> *}
                        
        </div>
        
        <!-- SEPARADOR HORIZONTAL -->
        <div class="separadorHorizontal"></div>
    {/if}
    
    <!-- ****************** FOTO/VIDEO DIA **************** -->
    {include file="modulo_column3_containerFotoVideoDiaMasListado.tpl"}
</div>
