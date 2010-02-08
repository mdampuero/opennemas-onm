<div class="CZonaRegistrarse">
    {if isset($message) }
       <div style="float:right;border:1px;background-color:#AAA;padding:10px;"><b>{$message}</b></div>
    {/if}
    
    <div class="textoConectaXornal">Suscripción al boletín de Xornal.com</div>
    <div class="contenedorZonaRegistro">
        <br/>
        <p>
            {if $subscription == 1}
                Ya estoy suscrito.<br />
                Si NO desea recibir el boletín de noticias de Xornal.com <label for="subscription">DESMARQUE</label> la casilla inferior.
            {else}
                Si desea recibir el boletín de noticias de Xornal.com <label for="subscription">MARQUE</label> la casilla inferior.                
            {/if}            
        </p>        
        <div class="fileteHorizontalRegistro"></div>
    </div>    
</div>
                        
<div class="CZonaDarseDeAlta" style="margin-bottom: 100px;">
	<form name="rexistro" id="rexistro" method="post" action="#" >
        
        <div class="CEntradasDAlta">
            
            <table class="registro" style="clear: both;">
                <tbody>                
                <tr>
                    <th></th>
                    <td>
                        <label><input type="checkbox" name="subscription" id="subscription"
                            value="1" {if $subscription == 1}checked="checked"{/if} />                        
                        Deseo suscribirme al boletín de Xornal.com y acepto las condiciones del presente aviso legal autorizando a Xornal.com a enviarme su boletín de noticias. </label>
                    </td>
                    <td colspan="2">&nbsp;</td>
                </tr>                
                <tr>
                    <td colspan="4" align="right">
                        <div style="clear: both; padding-top: 10px;"></div>
                        
                        <input type="image" src="{$params.IMAGE_DIR}envio_noticia/botonGuardar.gif" alt="Guardar"
                                style="cursor:pointer; float: right;" title="Actualizar " />
                    </td>
                </tr>
                </tbody>
            </table>
           
        </div>
	</form>


</div>
