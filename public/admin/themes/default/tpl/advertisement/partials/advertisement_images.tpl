<table class="adminlist">
	

	<tr>
		<td valign=top colspan="2">
			<div id="div_img_publi"  style="{if $advertisement->with_script == 1} display:none;{else}display:block;{/if}">
				{if $photo1->name}
					<table border="0">
						<tr>
							<td>
								<h2>{t}Multimedia for this ad:{/t}</h2>
							</td>
							<td style="text-align:right;">
								<a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img');">
									<img style="cursor:pointer;" src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/remove_image.png" id="remove_img" alt="Eliminar" title="{t}Delete{/t}" border="0" align="absmiddle" /> </a>
								<input type="hidden" id="input_img" name="img" title="{t}Image{/t}" value="{$advertisement->img}" size="70" />
							</td>
						</tr>
						<tr>
							<td align='left'>
								<div id="droppable_div1">
									{if strtolower($photo1->type_img)=='swf'}
									<object id="change1"  name="{$advertisement->img}" >
										<param name="movie" value="{$MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}"></param>
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
	
		<td align="center">
	
			<div id="id" style="cursor:pointer; border:1px double #ccc; background-color:#EEE; padding:7px;">
					<strong>{t}Available multimedia for ads{/t}</strong>
			</div>
	
			<div id="photos" class="photos clearfix" style="border:1px solid #ccc; {if $advertisement->with_script == 1}display:none;" {else} display:block;" {/if} >
				<em>{t}Drag and drop the advertisements to select them, use the logo to use flash{/t}</em><br><br>
				{if $paginacion}
					<p align="center">{t}Pages:{/t}{$paginacion} </p>
				{/if}
				<ul id='thelist'  class="gallery_list">
				   {assign var=num value='1'}
				   {section name=n loop=$photos}
						{if $photos[n]->content_status eq 1}
							<li style="position:relative">
								{if $photos[n]->type_img=='swf' || $photos[n]->type_img=='SWF'}
									<object style="z-index:-3; cursor:default;"  title="Desc: {$photos[n]->description} Tags: {$photos[n]->metadata} ">
										<param name="movie" value="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}{$photos[n]->name}"> <param name="autoplay" value="false">  <param name="autoStart" value="0">
										<embed  width="68" height="40" style="cursor:default;" src="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}{$photos[n]->name}" name="{$photos[n]->pk_photo}" border="0" de:mas="{$photos[n]->name}" de:url="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:type_img="{$photos[n]->type_img}" de:description="{$photos[n]->description}" {* onmouseover="return escape('{$photos[n]->title}<br>{$photos[n]->description}');" *} title="{$photos[n]->title} - {$photos[n]->description}"></embed>
									</object>
									<span  style="float:right; clear:none;">
									<img id="draggable_img{$num}" class="draggable" src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif" style="width:20px" name="{$photos[n]->pk_photo}" border="0" de:mas="{$photos[n]->name}"
										 de:url="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:type_img="{$photos[n]->type_img}" de:description="{$photos[n]->description}" {* onmouseover="return escape('{$photos[n]->title}<br>{$photos[n]->description}');" *} title="Desc: {$photos[n]->description}  Tags: {$photos[n]->metadata}" />
								</span>
								{else}
									<div>
										<img style="vertical-align: middle;{cssphotoscale width=$photos[n]->width height=$photos[n]->height resolution=68 photo=$photos[n]}" src="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}{$photos[n]->name}"
											 id="draggable_img{$num}"
											 class="draggable"
											 name="{$photos[n]->pk_photo}"
											 border="0" de:mas="{$photos[n]->name}" de:url="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:type_img="{$photos[n]->type_img}"  de:description="{$photos[n]->description}"  title="Desc: {$photos[n]->description} Tags: {$photos[n]->metadata} " />
									</div>
								{/if}
							</li>
							<script defer="defer" type="text/javascript">
								 new Draggable('draggable_img{$num}', { revert:true, scroll: window, ghosting:true }  );
							</script>
							{assign var=num value=$num+1}
						{/if}
				   {/section}
				</ul>
	
			</div>
	
		</td>
	</tr>
</table>


<script defer="defer" type="text/javascript">

  Droppables.add('droppable_div1', {
    accept: 'draggable',
    hoverclass: 'hover',
    onDrop: function(element) {
		    if((element.getAttribute('de:type_img')=='swf') || (element.getAttribute('de:type_img')=='SWF')){
	    		 var ancho=element.getAttribute('de:ancho');
		  		 if(element.getAttribute('de:ancho')>300) { ancho=300; }
		         $('droppable_div1').innerHTML='<object ><param name="movie" value="'+ element.getAttribute('de:url') +'/'+element.getAttribute('de:mas')+ '"><embed src="'+ element.getAttribute('de:url') +'/'+element.getAttribute('de:mas')+ '" width="'+ancho+'" ></embed></object>';
		         $('informa').innerHTML=' es un Flash';
		         $('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created');
		  		 $('input_img').value=element.name;

		    } else {
			   		var source=element.src;
		   			if($('change1')){ $('change1').src = source; }
			  		$('input_img').value=element.name;
			  		var ancho=element.getAttribute('de:ancho');
			  		if(element.getAttribute('de:ancho')>300) { ancho=300; }
			   		$('droppable_div1').innerHTML='<img src="'+ element.getAttribute('de:url') +'/'+element.getAttribute('de:mas')+ '"  id="change1" border="0" width="'+ancho+'" >';
			   		$('input_img').value=element.name;
			   		$('informa').innerHTML=' ';
			   		$('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created');
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