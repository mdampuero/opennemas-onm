<table align="center" style="border:1px solid #ccc;">
	<tr>
		<td colspan=2><h2>{t}Multimedia associated to this article:{/t}</h2></td>
	</tr>
	<tr>
		<td valign=top>
			<div id="img_portada" style="display:block; width:100%;">
				<table border='0' style="display:block; border-bottom:1px solid #ccc; background:#eee; padding:10px;">
					<tr>
						<td style="width:50%">
							<h2>{t}Frontpage image:{/t}</h2>
							<input type="hidden" id="input_video" name="fk_video" value="" size="70">
							<input type="hidden" id="input_img1" name="img1" title="Imagen" value="" size="70"/>
						</td>
						<td  align='right' style="width:50%">
							<a style="cursor:pointer;"  onclick="javascript:recuperar_eliminar('img1');">
								<img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/remove_image.png" id="remove_img1" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" />
							</a>
						</td>
					</tr>
					<tr>
						<td align='center' style="width:50%">
							<div id="droppable_div1">
								<img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_img.jpg" id="change1" name="default_img" border="0" width="300px" />
							</div>
							<div style="clear: both;"></div>
						</td>
						<td style="text-align:left;white-space:normal; style="width:50%"" colspan="2">
							<div id="informa" style="text-align:left;overflow:auto;width:260px; ">
								<p><strong>{t}File:{/t}</strong> default_img.jpg</p>
								<p><strong>{t}Size:{/t}</strong> 300 x 208 (px)</p>
								<p><strong>{t}Weight:{/t}</strong> 4.48 Kb<br><strong>Fecha de creaci&oacute;n:</strong> 11/06/2008</p>
								<p><strong>{t}Description:{/t}</strong>  Imagen por defecto.  <br><strong>Tags:</strong> Imagen</p>
							</div>
							<div id="noimag" style="display: inline; width:380px; height:30px;"></div>
							<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>
						</td>

					 </tr>
					<tr>
						<td colspan=2>
							<div id="footer_img_portada"> <label for="title">Pie imagen Portada:</label>
								<input type="text" id="img1_footer" name="img1_footer" title="Imagen" value=" " size="50" />
							</div>
						</td>
					</tr>
				</table>
			</div>
			<input type="hidden" id="posic" name="posic" value="0" />

			<br>
			<br>

			<div id="img_interior"  style="display:block;">
				<table border='0' style="width:100%; display:block; border-bottom:1px solid #ccc; background:#eee; padding:10px;">
					<tr>
						<td>
							<h2>Imagen Interior:</h2>
						</td>
						<td  align='right'>
							<a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img2');">
							<img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/remove_image.png" id="remove_img2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
						</td>
					</tr>
					<tr>
						 <td align='center'>
							<input type="hidden" id="input_img2" name="img2" value="" size="70">
							<div id="droppable_div2">
								<img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_img.jpg" id="change2" name="default_img" border="0" width="300px" />
							</div>
						</td>
						<td colspan="2" style="text-align:left;white-space:normal;">
							<div id="informa2" style="text-align:left;overflow:auto;width:260px; ">
								<p><strong>{t}File:{/t}</strong> default_img.jpg</p>
								<p><strong>{t}Size:{/t}</strong> 300 x 208 (px)</p>
								<p><strong>{t}Weight:{/t}</strong> 4.48 Kb<br><strong>Fecha de creaci&oacute;n:</strong> 11/06/2008</p>
								<p><strong>{t}Description:{/t}</strong>  Imagen por defecto.  <br><strong>Tags:</strong> Imagen</p>
							</div>
							<div id="noimag2" style="display: inline; width:380px; height:30px;">	</div>
							<div id="noinfor2" style="display: none; width:100%; height:30px;"></div>
						</td>
					</tr>
					<tr>
						<td colspan=2>
							<div id="footer_img_interior">
								<label for="title">Pie imagen Interior:</label>
								<input type="text" id="img2_footer" name="img2_footer" title="Imagen" value=" " size="50" />
							</div>
						</td>
					</tr>
				</table>

			</div>

			<br>
			<br>

			<div id="video_interior">
				<table style="width:100%; display:block; border-bottom:1px solid #ccc; background:#eee; padding:10px;">
					<tr>
						<td>
							<h2>{t}Inner video:{/t}</h2>
						</td>
						<td  align='right'>
							<a style="cursor:pointer;"  onclick="javascript:recuperar_eliminar('video2');">
							<img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/remove_image.png" id="remove_video2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
						</td>
					</tr>
					<tr>
						<td align='center'>
							<input type="hidden" id="input_video2" name="fk_video2" value="" size="70">
							<div id="droppable_div3">
								<img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_img.jpg" id="change3" name="default_img" border="0" width="300px" />
							</div>
						</td>
						<td colspan="2" style="text-align:left;white-space:normal;">
							<div id="informa3"  style="text-align:left;overflow:auto;width:260px; ">
								<strong>Archivo: default_img.jpg</strong> <br>
								<strong>Peso:</strong> 4.48 Kb<br><strong>Fecha de creaci&oacute;n:</strong> 11/06/2008<br>
								<strong>Descripcion:</strong>  Imagen por defecto.  <br><strong>Tags:</strong> Imagen<br>
							</div>
							<div id="noimag3" style="display: inline; width:380px; height:30px;">	</div>
							<div id="noinfor3" style="display: none; width:100%; height:30px;"></div>
						</td>
					</tr>
					<tr>
						<td colspan=2>
							<div id="video2_footer">
								<label for="title">Pie Video Interior:</label>
								<input type="text" id="footer_video2" name="footer_video2" title="video interior footer" value="" size="50" />
							</div>
						</td>
					</tr>
				</table>
			</div>

		</td>

		<td valign="top" align="left" rowspan="2" style="margin-right:10px;">

			<div onclick="new Effect.toggle($('photos_container'),'blind')"
				style="cursor:pointer; border:1px double #ccc; background-color:#EEE; padding:7px;">
				<strong>{t}Available images{/t}</strong>
			</div>
			<div id="photos_container" class="photos"
				 style="border:1px solid #ccc;  padding:7px;">
				<table>
					<tr>
						<td align="left">
							<div class="cajaBusqueda">
								<input id="stringImageSearch" name="stringImageSearch" type="text"
									   onkeypress="onImageKeyEnter(event, $('category_imag').options[$('category_imag').selectedIndex].value,encodeURIComponent($('stringImageSearch').value),1);"
									   onclick="this.select();" value="{t}Search images by title...{/t}" style="width:80%" />
							</div>
						</td>
						<td align="right">
						   <select id="category_imag" name="category_imag" class="required" onChange="get_images(this.options[this.selectedIndex].value,1,'list_by_category', 0);" style="width:100%">
							   <option value="0">GLOBAL</option>
								{section name=as loop=$allcategorys}
										<option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
										{section name=su loop=$subcat[as]}
											<option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
										{/section}
								  {/section}
							</select>
						</td>
					</tr>
				</table>
				<div id="photos" class="photos" style="height:460px; border:0px double #333333; margin:5px; overflow:auto;">
					<ul id='thelist'  class="gallery_list clearfix" style="width:100%; margin:0; padding:0">
					   {assign var=num value='1'}
					   {section name=n loop=$photos}
					   {if $photos[n]->content_status eq 1}
							<li>
								<div>
									<a>
										<img style="{cssimagescale resolution=67 photo=$photos[n]}"
											src="{$MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}140x100-{$photos[n]->name}"
											id="draggable_img{$num}" class="draggable" name="{$photos[n]->pk_photo}"
											border="0" de:mas="{$photos[n]->name}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:description="{$photos[n]->description|clearslash|escape:'html'}"
											de:tags="{$photos[n]->metadata}"
											 {* onmouseover="return escape('Desc:{$photos[n]->description|clearslash|escape:'html'}<br>Tags:{$photos[n]->metadata}');" *}
											title="Desc:{$photos[n]->description|clearslash|escape:'html'} - Tags:{$photos[n]->metadata}" />
									</a>
								</div>
								<script type="text/javascript">
									new Draggable('draggable_img{$num}', { revert:true, scroll: window, ghosting:true } );
								</script>
							</li>
							{assign var=num value=$num+1}
					   {/if}
					   {/section}
					</ul>
					{if $paginacion}
						<br>
						<div class="pagination" align="center"> {$paginacion} </div>
					{/if}
				</div>
			</div>

			<br>
			<br>


			<div  onclick="new Effect.toggle($('videos-container'),'blind')"
				  style="cursor:pointer;  border:1px double #ccc; background-color:#EEE; padding:7px;">
				<a style="cursor:pointer;" onclick="new Effect.toggle($('videos-container'),'blind')"> <strong>Videos</strong> </a>
			</div>
			<div id="videos-container" class="photos" style=" border:1px solid #ccc;  padding:7px;">
			   <table>
					<tr>
						<td>
							<div class="cajaBusqueda" style="width:100%;" align="left">
								<input class="textoABuscar" id="stringVideoSearch" name="stringVideoSearch" type="text"
									   onkeypress="onVideoKeyEnter(event, $('stringVideoSearch').value,1);"
									   onclick="this.select();" value="{t}Search video by title...{/t}"
									   align="left" style="width:100%"/>
							</div>
						</td>
					</tr>
			   </table>
			   <br>
			   <div id="videos" class="photos" style="width:100%; height:415x; border:0px double #333333; padding:0px;overflow:auto;">

				   <ul id='thelist'  class="gallery_list clearfix" style="width:100%; margin:0; padding:0">
					   {assign var=num value='1'}
					   {section name=n loop=$videos}
					   {if $videos[n]->content_status eq 1}
						   <li style="display:inline-block;">
							   <div>
								   <a>
									{if $videos[n]->author_name eq 'youtube'}
										<img class="video"  width="67" id="draggable_video{$num}" name="{$videos[n]->pk_video}" alt="{$videos[n]->title}" qlicon="{$videos[n]->videoid}" src="http://i4.ytimg.com/vi/{$videos[n]->videoid}/default.jpg" title="{$videos[n]->title} - {$videos[n]->author_name}" de:created="{$videos[n]->created}" de:description="{$videos[n]->description|clearslash|escape:'html'}" de:tags="{$videos[n]->metadata}"  {* onmouseover="return escape('Desc:{$videos[n]->description|clearslash|escape:'html'}<br>Tags:{$videos[n]->metadata}');" *} title="Desc:{$videos[n]->description|clearslash|escape:'html'} - Tags:{$videos[n]->metadata}" />
									{else}
										<img class="video"  width="67" id="draggable_video{$num}" name="{$videos[n]->pk_video}" alt="{$videos[n]->title}" qlicon="{$videos[n]->videoid}" src="{$videos[n]->thumbnail_medium}" title="{$videos[n]->title} - {$videos[n]->author_name}" de:created="{$videos[n]->created}" de:description="{$videos[n]->description|clearslash|escape:'html'}" de:tags="{$videos[n]->metadata}"  {* onmouseover="return escape('Desc:{$videos[n]->description|clearslash|escape:'html'}<br>Tags:{$videos[n]->metadata}');" *} title="Desc:{$videos[n]->description|clearslash|escape:'html'} - Tags:{$videos[n]->metadata}" />
									{/if}
									</a>
							   </div>
							   <script type="text/javascript">
								   new Draggable('draggable_video{$num}', {literal}{ revert:true, scroll: window, ghosting:true}{/literal}  );
							   </script>
						   </li>
						   {assign var=num value=$num+1}
					   {/if}
					   {/section}
				   </ul>
				   {if $paginacionV}
						<br>
						<div class="pagination" align="center" style="clear:both; width:100%"> {$paginacionV} </div>
				   {/if}
			   </div>
			</div>
		</td>
	</tr>
</table>

<style type="text/css">
	div.pagination {
		margin-top:10px;
		display:block;
	}
	.pagination a {
		border:1px solid #ccc;
		padding:5px;
		background:#fff;
	}
	.pagination a:hover {
		background:#dfdfdf;

	}
</style>

{literal}

<script type="text/javascript">

  Droppables.add('droppable_div1', {
    accept: ['draggable', 'video'],
    onDrop: function(element) {
                    recuperarOpacity('img1');
                    var source=element.src;
                     if($('change1')){
                            if(element.getAttribute('class')=='draggable'){
                                $('change1').src = source.replace( '140x100-','');
                            }else{
                                $('change1').src = source;
                            }
                            $('change1').name=element.name;
                            var ancho=element.getAttribute('de:ancho');
                             if(element.getAttribute('de:ancho')>300) {ancho=300;}
                             $('change1').setAttribute('width',ancho);
                            }
                    $('informa').innerHTML=' ';
                    if(element.getAttribute('class')=='draggable'){
                        $('input_img1').value=element.name;
                        $('informa').innerHTML="<strong>Archivo: </strong>"+element.getAttribute('de:mas') + "<br><strong>Dimensiones: </strong>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><strong>Peso: </strong>" + element.getAttribute('de:peso') + "Kb<br><strong>Fecha Creaci&oacute;n: </strong>" + element.getAttribute('de:created') + "<br><strong>Descripcion: </strong>" + element.getAttribute('de:description') +"<br><strong>Tags: </strong>" + element.getAttribute('de:tags');
                        $('img1_footer').value= element.getAttribute('de:description');
                          $('input_video').value='';
                    }else{
                       $('input_video').value=element.name;
                       $('informa').innerHTML="<strong>Codigo: </strong>"+element.getAttribute('title')  + "<br><strong>Fecha Creaci&oacute;n: </strong>" + element.getAttribute('de:created') + "<br><strong>Descripcion: </strong>" + element.getAttribute('de:description') +"<br><strong>Tags: </strong>" + element.getAttribute('de:tags');
                       $('img1_footer').value= element.getAttribute('de:description');
                       $('input_img1').value='';
                    }
                    // En firefox 2, precísase reescalar o div co alto da imaxe
                    if( /Firefox\/2/.test(navigator.userAgent) ) {
                        $('droppable_div1').style.height = $('change1').height + 'px';
                    }

        }
  });
  Droppables.add('droppable_div2', {
    accept: 'draggable',
    onDrop: function(element) {
    			recuperarOpacity('img2');
                        var source2=element.src;
                        //if($('change2')){ $('change2').src = source2;
                        if($('change2')){
                                $('change2').src = source2.replace( '140x100-','');
                                $('change2').name=element.name;
                                var ancho=element.getAttribute('de:ancho');
                                if(element.getAttribute('de:ancho')>300) {ancho=300;}
                                $('change2').setAttribute('width',ancho);
                         }
                        $('input_img2').value=element.name;
                        $('informa2').innerHTML=' ';
                        $('informa2').innerHTML="<strong>Archivo: </strong>"+element.getAttribute('de:mas') + "<br><strong>Dimensiones: </strong>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><strong>Peso: </strong>" + element.getAttribute('de:peso') + "Kb<br><strong>Fecha Creaci&oacute;n: </strong>" + element.getAttribute('de:created') + "<br><strong>Descripcion: </strong>" + element.getAttribute('de:description') +"<br><strong>Tags: </strong>" + element.getAttribute('de:tags');
                        $('img2_footer').value= element.getAttribute('de:description');

                // En firefox 2, precísase reescalar o div co alto da imaxe
                if( /Firefox\/2/.test(navigator.userAgent) ) {
                    $('droppable_div2').style.height = $('change2').height + 'px';
                }

		     }
  });

  Droppables.add('droppable_div3', {
    accept: 'video',
    onDrop: function(element) {
    			recuperarOpacity('video2');
                        var source3=element.src;
                        if($('change3')){
                            $('change3').src = source3;
                            $('change3').name=element.name;
                            $('change3').setAttribute('width',150);
                         }
                        $('input_video2').value=element.name;
                        $('informa3').innerHTML=' ';
                        $('informa3').innerHTML="<strong>Codigo: </strong>"+element.getAttribute('title')  + "<br><strong>Fecha Creaci&oacute;n: </strong>" + element.getAttribute('de:created') + "<br><strong>Descripcion: </strong>" + element.getAttribute('de:description') +"<br><strong>Tags: </strong>" + element.getAttribute('de:tags');
                        $('footer_video2').value= element.getAttribute('de:description');
	}
  });


</script>
{/literal}
