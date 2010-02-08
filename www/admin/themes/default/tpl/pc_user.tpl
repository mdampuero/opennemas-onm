{include file="pc_header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
{include file="pc_botonera_up.tpl"}

<table class="adminheading">
	<tr>
		<th>
            <label>
                Buscar: <input type="text" name="filters[text]" id="filters_text" value="{$smarty.request.filters.text}" />
            </label>
            
            <select name="filters[subscription]" id="filters_subscription">
                <option value="-1">--Suscripción boletín--</option>
                <option value="1"{if $smarty.request.filters.subscription==1} selected="selected"{/if}>SI</option>
                <option value="0"{if isset($smarty.request.filters.subscription) && $smarty.request.filters.subscription==0} selected="selected"{/if}>NO</option>
            </select>
            
            <select name="filters[status]" id="filters_status">
                <option value="-1">--Suscripción Conect@--</option>                
                <option value="0"{if isset($smarty.request.filters.status) && $smarty.request.filters.status==0} selected="selected"{/if}>Pendiente mail</option>
                <option value="1"{if $smarty.request.filters.status==1} selected="selected"{/if}>Aceptado Usuario</option>
                <option value="2"{if $smarty.request.filters.status==2} selected="selected"{/if}>Aceptado Administrador</option>
                <option value="3"{if $smarty.request.filters.status==3} selected="selected"{/if}>Deshabilitado</option>
            </select>
            
            <input type="hidden" name="page" id="filters_page"
                 value="{$smarty.request.page|default:'1'}" />
            
            <input type="button" onclick="filterList();"
                 value="Filtrar" />
            <input type="button" onclick="resetForm();"
                 value="Limpiar" />
        </th>
	</tr>	
</table>

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="98%">
<thead>
    <tr>
		<th style="padding:10px;" align="left">Apellidos, Nombre</th>
		<th style="padding:10px;" align="left">Nick</th>
		<th style="padding:10px;" align="left">Ciudad</th>
        <th style="padding:10px;" align="center">Boletín</th>
        
		<th style="padding:10px;" align="center">Estado</th>
		<th style="padding:10px;" align="center">Aceptar/Deshabilitar</th>
		<th style="padding:10px;" align="center">Editar</th>	
		<th style="padding:10px;" align="center">Eliminar</th>
	</tr>
</thead>

<tbody id="gridUsers">
	{section name=c loop=$users}
	<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
		<td style="padding:10px;">
            <input type="checkbox" class="minput" name="cid[]" value="{$users[c]->id}" style="cursor:pointer;" />
            
			{$users[c]->firstname}&nbsp;{$users[c]->lastname}, {$users[c]->name}
		</td>
		<td style="padding:10px;">
			{$users[c]->nick}
		</td>
		<td style="padding:10px;">
			{$users[c]->city}
		</td>
        <td style="padding:10px;" align="center">
            <a href="?action=subscribe&id={$users[c]->id}" class="newsletterFlag">	
			{if $users[c]->subscription eq 0}
                <img src="{$params.IMAGE_DIR}subscription_0-16x16.png" border="0" title="Suscribir" />
			{else}
                <img src="{$params.IMAGE_DIR}subscription_1-16x16.png" border="0" title="Anular suscripción" />
            {/if}
            </a>
	 	</td>
		<td style="padding:10px;" align="center"> 
			{if $users[c]->status eq 0} Mail enviado-falta aceptación 
			{elseif  $users[c]->status eq 1}  Aceptado por el usuario 
			{elseif  $users[c]->status eq 2}  Aceptado por el admin 
			{elseif  $users[c]->status eq 3}  Deshabilitado por el admin {/if}
	 	</td>
		<td style="padding:10px;" align="center"> 
			{if $users[c]->status eq 0 || $users[c]->status eq 3}  
				<a href="?id={$users[c]->id}&amp;action=change_status" class="newsletterFlag">
				<img src="{$params.IMAGE_DIR}publish_r.png" border="0" title="Habilitar" /></a>
			{else}
				<a href="?id={$users[c]->id}&amp;action=change_status" class="newsletterFlag">
				<img src="{$params.IMAGE_DIR}publish_g.png" border="0" title="Deshabilitar" /></a>
			 {/if}
		</td>
		<td style="padding:10px;width:75px;" align="center">
			<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$users[c]->id});" title="Modificar">
				<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
		</td>
		<td style="padding:10px;width:75px;" align="center">
			<a href="#" onClick="javascript:confirmar(this, {$users[c]->id});" title="Eliminar">
				<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
		</td>
	</tr>
	{sectionelse}
	<tr>
		<td align="center"><br><b>Ning&uacute;n usuario listado bajo estos criterios.</b></td>
	</tr>
	{/section}
</tbody>

<tfoot>	
	<tr>
	    <td colspan="8" align="center">{$pager->links}</td>
	</tr>
</tfoot>
    
</table>

<script type="text/javascript" language="javascript" src="{$params.JS_DIR}switcher_flag.js"></script>
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
{literal}
/* Utils filter functions */
function paginate(page) {                    
    $('filters_page').value = page;                    
    $('formulario').submit();                
}

function filterList() {
    $('filters_page').value = '1';
    
    $('action').value='list';
    $('formulario').submit();
}

function resetForm() {
    $('filters_text').value = '';    
    $('filters_status').value = '-1';    
    $('filters_subscription').value = '-1';
    
    $('filters_page').value = '1';
    $('action').value='list';
    
    $('formulario').submit();
}

/* Init list actions */
$('gridUsers').select('a.newsletterFlag').each(function(item){
	new SwitcherFlag(item);
});

document.observe('dom:loaded', function() {
    $$('a.checkall').each(function(lnk) {
        lnk.observe('click', function() {
            var items = $('gridUsers').select('input[type=checkbox][name^=cid]');
            var status = !!items[0].checked;
            items.each(function(item){
                if(!status) {
                    item.setAttribute('checked', 'checked');
                    item.checked = true;
                } else {
                    item.removeAttribute('checked');
                    item.checked = false;
                }                
            });
            
            if(!status) {
                this.select('span')[0].update('Deseleccionar');
            } else {
                this.select('span')[0].update('Seleccionar todo');
            }
        });
    });
    
    $$('a.subscribe').each(function(lnk) {
        lnk.observe('click', function() {
            var frm = $('formulario');
            $('action').value = 'msubscribe';
            frm.submit();
        });
    });

    $$('a.unsubscribe').each(function(lnk) {
        lnk.observe('click', function() {
            var frm = $('formulario');
            $('action').value = 'munsubscribe';
            frm.submit();
        });
    });
    
    $$('a.mdelete').each(function(lnk) {
        lnk.observe('click', function() {
            var frm = $('formulario');
            $('action').value = 'mdelete';
            frm.submit();
        });
    });
    
    // Setup this action by default
    $('action').value = 'list';
});
{/literal}
/* ]]> */
</script>

{/if}


{* FORMULARIO PARA ENGADIR  ************************************** *}
{if isset($smarty.request.action) && (($smarty.request.action eq "new") || ($smarty.request.action eq "read"))}
	{include file="pc_botonera_up.tpl"}
    
    {if isset($message)}<div style="border: 1px solid #963; background-color: #FEE; padding: 10px;">{$message}</div>{/if}
    
	<table  border="0" cellpadding="4" cellspacing="0" class="fuente_cuerpo">
	<tr>
        <td valign="top">
        
        <fieldset>
            <legend>Datos usuario</legend>
		
        	<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
			<tbody>
			<!-- Login -->
            <tr>
                <td style="padding:10px;" width="200" align="right">
                    <label>
                        <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Aceptar" />
                        Aceptado: <input type="radio" name="status" value="2"
                               {if $user->status eq 2 || $user->status eq 1 }checked="checked"{/if} />
                    </label>
                </td>
                <td style="padding:10px;">
                    <label>
                        <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Aceptar" />
                        Deshabilitado: <input type="radio" name="status" value="3"
                               {if $user->status eq 0 || $user->status eq 3 }checked="checked"{/if} />
                    </label>
                </td>                
            </tr>            
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="nickDA">Nick:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="nickDA" name="nickDA" title="nick del usuario"
						value="{$user->nick}" class="required validate-min-length validate-alpha{if ($smarty.request.action eq "new")} check-nick{/if}" />
				</td>
			</tr>
			<!-- Password -->
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="passDA">Password:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="password" id="passDA" name="passDA" title="Password"
						value="" {if ($smarty.request.action eq "new")}class="required"{/if}  />						
						
				</td>
			</tr>
			<!-- Email -->
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="emailDA">Correo Electr&oacute;nico:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="emailDA" name="emailDA" title="Correo Electr&oacute;nico"
						value="{$user->email}" class="required validate-email{if ($smarty.request.action eq "new")} check-email{/if}" />
				</td>
			</tr>
            <tr>
                <td valign="middle" align="right" style="padding:4px">
                    <label>
                        Suscripción a Boletín:                                                
                    </label>
                </td>
                <td style="padding:10px;">
                    <label>
                        <img src="{$params.IMAGE_DIR}subscription_1-16x16.png" border="0" title="Está suscrito al boletín" />
                        Si: <input type="radio" name="subscription" value="1"
                               {if !(isset($user->subscription)) || $user->subscription eq 1 }checked="checked"{/if} />
                    </label>
                               
                    <label>
                        <img src="{$params.IMAGE_DIR}subscription_0-16x16.png" border="0" title="No está suscrito al boletín" />
                        No: <input type="radio" name="subscription" value="0"
                               {if (isset($user->subscription)) && $user->subscription eq 0}checked="checked"{/if} />
                    </label>                
                </td>
            </tr>
			
			{*<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="email">Avatar:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="file" name="file_photo" id="photo" class="required" size="30" />
				</td>
			</tr> *}
		</table>
        
        </fieldset>
	</td>	
	<td>
        <fieldset>
            <legend>Datos personales</legend>
            
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
			<tbody>			
			
			<!-- Nome -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="200">
					<label for="nombreDA">Nombre:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="nombreDA" name="nombreDA" title="Nombre del usuario"
						value="{$user->name}" class="required validate-alpha" />
				</td>
			</tr>
			<!-- Primeiro apelido -->
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="apellidoDA">Primer Apellido:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="apellidoDA" name="apellidoDA" title="Primer apellido del usuario"
						value="{$user->firstname}" class="required validate-alpha" />
				</td>
			</tr>
			<!-- Segundo apelido -->
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="segApellidoDA">Segundo Apellido:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="segApellidoDA" name="segApellidoDA" title="Segundo apellido del usuario"
						class="validate-alpha" value="{$user->lastname}" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="dniDA">DNI:</label><br />
                    <sub>(99.999.999-L)</sub>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="dniDA" name="dniDA" title="DNI del usuario" class="validate-dni"
						value="{$user->dni}" />
				</td>
			</tr>
                
			<!-- Direccion -->
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="poblacionDA">Poblaci&oacute;n:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="poblacionDA" name="poblacionDA" title="Direcci&oacute;n del usuario"
						value="{$user->city}" class="validate-alpha" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="paisDA">Pa&iacute;s:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="paisDA" name="paisDA" title="Direcci&oacute;n del usuario"
						value="{$user->country}" class="validate-alpha" />
				</td>
			</tr>
				<!-- Fecha nacimiento -->
			<tr>
                <td valign="top" align="right" style="padding:4px;" >
                    <label for="fechaNacimientoDA">Fecha de Nacimiento:</label><br />
                    <sub>(dd/mm/yyyy)</sub>
                </td>
                <td style="padding:4px;" nowrap="nowrap">				
                    <input type="text" id="fechaNacimientoDA" name="fechaNacimientoDA" size="18" title="Fecha de nacimiento"
                            value="{if !preg_match('/^0000/', $user->date_nac)}{$user->date_nac|date_format:"%d/%m/%Y"}{/if}" class="validate-date-au" />{*<button id="triggerend">...</button>*}
                </td>
            </tr>
            
            <tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="movilDA">Tel&eacute;fono M&oacute;vil:</label><br />
                    <sub>(999-999-999)</sub>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="movilDA" name="movilDA" title="movil" class="validate-phone"
						value="{$user->movil}" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="telefDA">Tel&eacute;fono Fijo:</label><br />
                    <sub>(999-999-999)</sub>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="telefDA" name="telefDA" title="tfno" class="validate-phone"
						value="{$user->phone}"  />
				</td>
			</tr>
		{*	
			<!-- User_Group -->
			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="id_user_group">Grupo al que pertenece:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<select id="id_user_group" name="id_user_group" title="Grupo de usuario">
					{section name=user_group loop=$user_groups}
					{if $user_groups[user_group]->id == $user->id_user_group}
								<option  value = "{$user_groups[user_group]->id}" selected="selected">{$user_groups[user_group]->name}</OPTION>
			  		{else}
						<option  value = "{$user_groups[user_group]->id}">{$user_groups[user_group]->name}</OPTION>
					{/if}
					{/section}
					</selec>
				</td>
			</tr>
		*}
			</tbody>
			</table>
        </fieldset>
</td>
</tr>
</table>

{*
{ dhtml_calendar inputField="date_nac" button="triggerend" singleClick=true ifFormat="%Y-%m-%d" firstDay=1 align="CR"}
*}

<script type="text/javascript" language="javascript" src="{$params.JS_DIR}registro-validations.js?cacheburst=1260795256"></script>
<script language="javascript" type="text/javascript">
/*<![CDATA[*/         
    new Validation('formulario');  
/*]]>*/
</script>
{/if}

<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" value="{$id}" />
</form>


{include file="footer.noform.tpl"}