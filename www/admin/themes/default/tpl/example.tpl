  
 <div id="nifty" style="width:820px;">  			 
  	<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>					       
		<table border=0 cellpadding="2" cellspacing="6">				
				<tr><td>
				    <h2 style="color:#2f6d9d;" width='80%'>
				    	<img src="themes/default/images/tit_img.png" style="margin-bottom:-15px;"/> 
				    	Imagen Ejemplo: <hr style="margin:0;border-style: solid; color:#2f6d9d; border-width: 2px">
				    </h2>
				   </td><td align='right' width='20%'> {* {dialogo category=$category field=$field value=$value}	*}
				</td></tr>
				<tr><td colspan=2>												
					<label>Imagen:</label> 
					<input type="text" id="imgdesta" readonly value="imagen de ejemplo" size="100">				
					</td>
				</tr>
				<tr><td nowrap width=40%>
					 		<img src="../media/images/default_img.jpg"  id="imagexample" name="Example" width="300px" border="0" />
					</td><td>
							<div id="informacion1" style="display: inline;width:400px; position:relative; float:left; margin:10px">
								<table width="370"  border="0" cellpadding="4"><tbody>
										<tr bgcolor="#ccffbb"><td> <b> Aceptada. </b> <br>El ancho es correcto.</td></tr>
										<tr><td> <b>Archivo: default_img.jpg</b> <br><b>Ancho:</b> 300px<br><b>Alto:</b> 208px<br>
										         <b>Peso:</b> 4.48 Kb<br><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br>
										</td></tr>
								</tbody></table>								
							</div>					
					</td>
				</tr>
				<tr><td colspan=2>
						<label for="title">Comentario:</label>				
						<input type="text" readonly id="i_ex" name="i_ex" title="Imagen" value="Imagen de ejemplo" size="100" />				 
				</td></tr>
		</table>			
	<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>		
 </div>						


{* {include file='example.tpl' field="img2" value=$article->img2 } *}

						