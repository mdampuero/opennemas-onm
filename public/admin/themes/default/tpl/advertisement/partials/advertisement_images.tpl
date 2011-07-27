<table class="adminlist">
	<tr>
		<td valign="top">
			<div id="div_img_publi"  style="{if $advertisement->with_script == 1} display:none;{else}display:block;{/if}">
				{if $photo1->name}
					<table border="0">
						<tr>
							<td>
								<h2>{t}Multimedia for this ad:{/t}</h2>
							</td>
							<td style="text-align:right;">
								<a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img');">
									<img style="cursor:pointer;" src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_img" alt="Eliminar" title="{t}Delete{/t}" border="0" align="absmiddle" /> </a>
								<input type="hidden" id="input_img" name="img" title="{t}Image{/t}" value="{$advertisement->img}" size="70" />
							</td>
						</tr>
						<tr>
							<td align='left'>
								<div id="droppable_div1">
									{if strtolower($photo1->type_img)=='swf'}
									<object id="change1"  name="{$advertisement->img}" >
										<param name="movie" value="{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}"></param>
										<embed src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}" width="300" ></embed>
									</object>
									{else}
									<img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}" name="{$advertisement->img}" id="change1" border="0" width="300px" />
									{/if}
								</div>
							</td>
							<td nowrap="nowrap" colspan="2">
								<div id="informa2" style="text-align:left;overflow:auto;width:260px; ">
									</div>
									<div id="noimag2" style="display: inline; width:380px; height:30px;">	</div>
								<div id="noinfor2" style="display: none; width:100%; height:30px;"></div>
		
								<div id="informa" style="display: inline; width:380px; height:30px;">
										<p><strong>{t}File:{/t}</strong> {$photo1->name}</p>
										<p><strong>{t}Size:{/t}</strong> {$photo1->width} x {$photo1->height}</p>
										<p><strong>{t}Weight:{/t}</strong> {$photo1->size} Kb<br></p>
										<p><strong>{t}Creation date{/t}</strong> {$photo1->created}</p>
										<p><strong>{t}Description:{/t}</strong>  {$photo1->description}  <br></p>
										<p><strong>{t}Tags:{/t}</strong> {$photo1->metadata}</p>
								</div>
								<div id="noimag" style="display: inline; width:380px; height:30px;"></div>
								<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>
							</td>
						</tr>
					</table>
				{else}
					<table border="0">
						<tr>
							<td>
								<h2>{t}Advertisement:{/t}</h2>
							</td>
							<td>
								<input type="hidden" id="input_img" name="img" title="{t}Image{/t}" value="{$advertisement->img}" size="70"/>
							</td>
						</tr>
						<tr>
							<td align="left">
								<div id="droppable_div1">
									<img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_img.jpg" id="change1" border="0" width="300px" />
								</div>
							</td>
							<td nowrap="nowrap" colspan="2">
								<div id="informa" style="display: inline; width:380px; height:30px;">
										<p><strong>{t}File:{/t}</strong> {t}default_img.jpg{/t}</p>
										<p><strong>{t}Size:{/t}</strong> {t}XX x XX px{/t}</p>
										<p><strong>{t}Weight:{/t}</strong> {t}XX Kb{/t}<br></p>
										<p><strong>{t}Creation date{/t}</strong> {t}11/06/2008{/t}</p>
										<p><strong>{t}Description:{/t}</strong>  {t}Example image{/t}  <br></p>
										<p><strong>{t}Tags:{/t}</strong> {t}some, words, separated, by, commas{/t}</p>
								</div>
								<div id="noimag" style="display: inline; width:380px; height:30px;"></div>
								<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>
							</td>
						</tr>
					</table>
				{/if}
			</div>
		</td>
	
		<td style="max-width:400px; text-align: center;">
	
			<div id="id" style="cursor:pointer; border:1px double #ccc; background-color:#EEE; padding:7px;">
					<strong>{t}Available multimedia for ads{/t}</strong>
			</div>
	
			<div id="photos" class="photos clearfix" 
                 style="border:1px solid #ccc; {if $advertisement->with_script == 1}display:none;
                                                {else} display:block; {/if}" >
 
			</div>
	
		</td>
	</tr>
</table>


<script defer="defer" type="text/javascript">
 document.observe('dom:loaded', function() {
     getGalleryImages('listByCategory','2','','1');
    });
  Droppables.add('droppable_div1', {
    accept: 'draggable',
    hoverclass: 'hover',
    onDrop: function(element) {
		    if((element.getAttribute('de:type_img')=='swf') || (element.getAttribute('de:type_img')=='SWF')){
	    		 var ancho=element.getAttribute('de:ancho');
		  		 if(element.getAttribute('de:ancho')>300) { ancho=300; }
		         $('droppable_div1').innerHTML='<object ><param name="movie" value="'+
                                                element.getAttribute('de:url') +'/'+ element.getAttribute('de:mas')
                                                + '"><embed src="'+ element.getAttribute('de:url')
                                                +'/'+element.getAttribute('de:mas')+ '" width="'+ancho+'" ></embed></object>';
		         $('informa').innerHTML=' es un Flash';
		         $('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created');
		  		 $('input_img').value=element.name;

		    } else {
			   		var source=element.src;
                    $('input_img').value=element.name;

                    if ($('change1')) {
                        $('change1').src = source.replace( '140-100-','');
                        $('change1').name=element.name;
                        var ancho=element.getAttribute('de:ancho');
                        if(element.getAttribute('de:ancho')>300) { ancho=300; }
                        $('change1').setAttribute('width',ancho);
                    } else {
                        $('droppable_div1').innerHTML= '<img src="'+ source.replace( '140-100-','') + '"  id="change1" border="0" width="'+ancho+'" >';
                    }
			   		$('informa').innerHTML=' ';
                    $('informa').innerHTML= " <p><strong>{t}File name:{/t}</strong> " + element.getAttribute('de:mas') + "</p>"+
                                            "<p><strong>{t}Size:{/t}:</strong> "+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + "(px)</p>"+
                                            "<p><strong>{t}File size:{/t}</strong> " + element.getAttribute('de:peso') + " Kb</p>"+
                                            "<p><strong>{t}File creation date{/t}:</strong> " + element.getAttribute('de:created') + "</p>"+
                                            "<p><strong>{t}Description:{/t}</strong> " + element.getAttribute('de:description') +"</p>"+
                                            "<p><strong>Tags:</strong> "+ element.getAttribute('de:tags')+"</p> ";
                                            
			}
	  }
  });


</script>
<style type="text/css">
	#div_img_publi table,
	#div_img_publi tr,
	#div_img_publi td {
		background:none !important;
		border:none;
	}
	#div_img_publi {
		border-bottom: 1px solid #ccc;
		background:#EEEEEE;
		padding:10px;
	}
	#photos {
		min-height:250px;
	}
	
</style>