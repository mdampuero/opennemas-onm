{include file="modulo_head.tpl"}
<body>
<div class="global_metacontainer">
    <div class="marco_metacontainer">
        <div class="metacontainer">
{include file="modulo_separadorbanners1.tpl"}
{include file="modulo_header.tpl"}
    	<div class="container">
    	    <div class="containerNoticias">
    	        <div class="column12">    	            
    	            <div class="containerCol12 fondoContainerActualidad">
                        {if preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "faq") }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_faq.tpl"}
                            {include file="conecta_CLinksConectaRegistro.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "signin") }
                            {include file="conecta_tabs.tpl"}
                            {if !isset($registered) || !$registered}
                                {include file="conecta_CZonaDarseDeAlta.tpl"}
                            {else}
                                {include file="conecta_CZonaRegistrado.tpl"}
                            {/if}
                            {include file="conecta_CLinksConectaRegistro.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "login") }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_CZonaRegistrarse.tpl"}
                            {include file="conecta_CLinksConectaRegistro.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "olvido") }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_CZonaFormularioPass.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "cambio") }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_CZonaCambioPass.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "perfil") }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_CZonaPerfil.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "boletin") }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_CZonaBoletin.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "send") }
                            {include file="conecta_CZonaEnvioNoticia.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list") }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_content.tpl"}
                            {include file="conecta_CLinksConectaRegistro.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "fotografias")  }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_CZonaVisionadoMedia_photos.tpl"}
                            {include file="conecta_CLinksConectaRegistro.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "videos")  }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_CZonaVisionadoMedia_videos.tpl"}
                            {include file="conecta_CLinksConectaRegistro.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "polls") }
                            {include file="conecta_tabs.tpl"}
                            {if $op eq 'votar'}
                                         {include file="conecta_CZonaEncuesta.tpl"}
                            {else}
                                {include file="conecta_CZonaVisionadoMedia.tpl"}
                            {/if}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "cartas") }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_CZonaVisionadoLetter.tpl"}
                            {include file="conecta_CLinksConectaRegistro.tpl"}

                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "opiniones") }
                            {include file="conecta_tabs.tpl"}
                            {include file="conecta_CZonaVisionadoOpinion.tpl"}
                            {include file="conecta_CLinksConectaRegistro.tpl"}
                        {/if}
                    </div>
                </div>
                {include file="conecta_column3.tpl"}
            </div>
            <div class="separadorHorizontal"></div>
            {include file="modulo_separadorbanners3.tpl"}
            <div class="separadorHorizontal"></div>
         </div>
        {include file="modulo_footer.tpl"}
        </div>
    </div>
</div>
{include file="modulo_analytics.tpl"}
</body>
</html>