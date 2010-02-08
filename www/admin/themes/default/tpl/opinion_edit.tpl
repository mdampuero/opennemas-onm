
{* FORMULARIO PARA ACTUALIZAR *********************************** *}

    {include file="botonera_up.tpl"}
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="900">
    <tbody>
    <tr>
        <td> </td><td > </td>
        <td rowspan="6" valign="top" style="padding:4px;border:1px solid #CCCCCC"><b>
            <div align='center'>
                    <table style='background-color:#F5F5F5; padding:18px; width:60%;' border='0' cellpadding="8">
                        <tr>
                            <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                <label for="title"> Disponible: </label>
                            </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                    <select name="available" id="available" class="required">
                                        <option value="0" {if $opinion->available eq 0} selected {/if}>No</option>
                                        <option value="1"  {if $opinion->available eq 1} selected {/if}>Si</option>
                                    </select>
                            </td>
                            <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                    <label for="title"> En Home: </label>
                            </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                <select name="in_home" id="in_home" class="required">
                                    <option value="1"  {if $opinion->in_home eq 1} selected {/if}>Si</option>
                                    <option value="0"  {if $opinion->in_home eq 0} selected {/if}>No</option>
                                </select>
                            </td>
			</tr> <tr>
                            <td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">
			    	<label for="title"> Comentarios: </label>  
                            </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
			    	<select name="with_comment" id="with_comment" class="required">
                                    <option value="0"  {if $opinion->with_comment eq 0} selected {/if}>No</option>
                                    <option value="1" {if $opinion->with_comment eq 1} selected {/if}>Si</option>
                                </select>
                            </td>
			</tr>
			<tr>
                            <td valign="top" align="right" style="padding:4px;">
                                    <label for="title">Nº Palabras t&iacute;tulo:</label>
                            </td>
                            <td style="padding:4px;" nowrap="nowrap" >
				<input type="text" id="counter_title" name="counter_title" title="counter_title"
						value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title'),this)"/>
                            </td>
			</tr>			
			<tr>
                            <td valign="top" align="right" style="padding:4px;">
                                    <label for="title">Nº Palabras cuerpo:</label>
                            </td>
                            <td style="padding:4px;" nowrap="nowrap" >
                                    <input type="text" id="counter_body" name="counter_body" title="counter_body"
                                            value="0" class="required" size="5"  onkeyup="counttiny(document.getElementById('counter_body'));"/>
                            </td>
			</tr>
                    </table>
                </div>
                <table border='0'>
                    <tr>
			<td valign="top" colspan="2" style="padding:4px;border:1px solid #CCCCCC">
                            <b>Fotos autor:</b> <br>
                            <div id="photos" name="photos" class="photos" style="width:480px; padding:2px;">
                                 <ul id='thelist'  class="gallery_list" style="width:470px;">
                                        {section name=as loop=$photos}
                                            <li> <img src="{$MEDIA_IMG_PATH_URL}{$photos[as]->path_img}" id="{$photos[as]->pk_img}"  border="1" /></li>
                                             {literal}
                                                  <script type="text/javascript">
                                                      new Draggable( {/literal}'{$photos[as]->pk_img}'{literal} ,{ revert:true } );
                                                  </script>
                                             {/literal}
                                        {/section}
                                 </ul>
                            </div>
			 </td>
                    </tr>
                    <tr>
			<td valign="top" style="padding:4px;border:1px solid #CCCCCC">
                            <div id="sel" style="width:220px; padding:8px;min-height:70px; background-color:#eee">
				<b>Opini&oacute;n Interior:</b> <br />
				<img src="{$MEDIA_IMG_PATH_URL}{$foto->path_img}" id="seleccionada" name="seleccionada"  border="1" align="top" />
				<input type="hidden" id="fk_author_img" name="fk_author_img" value="{$opinion->fk_author_img}" />
                            </div>
			</td> 			
			<td valign="top" style="padding:4px;border:1px solid #CCCCCC;min-height:80px;">
                            <div id="div_widget" style="width:220px;min-height:70px; padding:8px; background-color:#bbb">
                                <b>Widget Opini&oacute;n:</b><br />
                                <img src="{$MEDIA_IMG_PATH_URL}{$fotowidget->path_img}" id="widget" name="widget"  border="1" align="top" />
                                <input type="hidden" id="fk_author_img_widget" name="fk_author_img_widget" value="{$opinion->fk_author_img_widget}" />
                            </div>
			</td>
                    </tr>
		</table>
                {literal}
                    <script type="text/javascript">
                      Droppables.add('div_widget', {
                          onDrop: function(element) {
                              if(element.width == 60){
                                  $('widget').src=element.src;
                                  $('fk_author_img_widget').value=element.id;
                              }else{
                                 alert('Ancho no permitido, necesita foto de 60px');
                              }
                          }
                      });
                      Droppables.add('sel', {
                          onDrop: function(element) {
                             $('fk_author_img').value=element.id;
                             $('seleccionada').src=element.src;
                          }
                      });

                    </script>
                {/literal}
        </td>
    </tr>
    <tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Titulo de la opinion" onkeyup="countWords(this,document.getElementById('counter_title'))"
			value="{$opinion->title|clearslash|escape:"html"}" class="required" size="80" onBlur="javascript:get_metadata(this.value);" />
		<input type="hidden" id="category" name="category" title="opinion" value="opinion" />
		
	</td>
    </tr>
    <tr>
	<td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">			
                <label for="title"> Tipo: </label>
        </td>
        <td valign="top" style="padding:4px;" nowrap="nowrap">
            <select name="type_opinion" id="type_opinion" class="validate-selection"  onChange='show_authors(this.options[this.selectedIndex].value);'>
                <option value="-1"></option>
                <option value="0" {if $opinion->type_opinion eq 0} selected {/if}>Opini&oacute;n de autor</option>
                <option value="1" {if $opinion->type_opinion eq 1} selected {/if}>Editorial</option>
                <option value="2" {if $opinion->type_opinion eq 2} selected {/if}>Carta del Director</option>
            </select>
        </td>
    </tr>
    <tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
            <div id="div_author1"  {if $opinion->type_opinion eq 0} style="display:inline;" {else} style="display:none;"{/if} > 	<label for="title">Autor:</label> </div>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		 <div id="div_author2" {if $opinion->type_opinion eq 0} style="display:inline;" {else} style="display:none;"{/if}>
                    <select id="fk_author" name="fk_author" class="validate-selection" onChange='changePhotos(this.options[this.selectedIndex].value);'>
                        <option value="0" {if $author eq "0"}selected{/if}> </option>
                        {section name=as loop=$todos}
                                <option value="{$todos[as]->pk_author}" {if $opinion->fk_author eq $todos[as]->pk_author}selected{/if}>{$todos[as]->name}</option>
                        {/section}
		    </select>
                 </div>
                 <input type="hidden" id="fk_user_last_editor" name="fk_user_last_editor" title="publisher" value="{$publisher}"  size="60" />
	</td>
    </tr>
    <tr>
	<td valign="top" align="right" style="padding:4px;">
		<label for="metadata">Palabras clave: </label>
	</td>
	<td style="padding:4px;" nowrap="nowrap"">
		<input type="text" id="metadata" name="metadata" size="80" title="Metadatos" value="{$opinion->metadata|clearslash}" />
	</td>
    </tr>
    <tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="body">Cuerpo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<textarea name="body" id="body"
			title="Opinion" style="width:90%; height:20em;">{$opinion->body|clearslash}</textarea>
	</td>
    </tr>
</tbody>
</table>

    {literal}
            <script>
                    countWords(document.getElementById('title'), document.getElementById('counter_title'));
                    countWords(document.getElementById('body'), document.getElementById('counter_body'));
            </script>
    {/literal}

