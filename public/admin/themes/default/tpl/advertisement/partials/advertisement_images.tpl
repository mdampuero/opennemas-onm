<tr>
    <td>
    </td>
	<td nowrap="nowrap"  align="center" valign="top">
		<div id="width_warning" style="color:red;text-align:left;"> </div>
    </td>
    <td>
    </td>
</tr>
<tr>
	<td nowrap="nowrap" colspan="2">
		<div id="div_img_publi"  style="{if $advertisement->with_script == 1} display:none;{else}display:inline;{/if}">
			{if $photo1->name}
				<table border="0" width="96%">
				<tr>
					<td>
						<h2>{t}Advertisement:{/t}</h2>
					</td>
					<td style="text-align:right;">
						<a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img');">
							<img style="cursor:pointer;" src="{$smarty.const.SITE_ADMIN_URL}{$params.IMAGE_DIR}remove_image.png" id="remove_img" alt="Eliminar" title="{t}Delete{/t}" border="0" align="absmiddle" /> </a>
						<input type="hidden" id="input_img" name="img" title="{t}Image{/t}" value="{$advertisement->img}" size="70" />
					</td>
				</tr>
				<tr>
					<td align='left'>
						<div id="droppable_div1">
							{if strtolower($photo1->type_img)=='swf'}
							<object id="change1"  name="{$advertisement->img}" >
								<param name="movie" value="{$MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}"></param>
								<embed src="{$MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}" width="300" ></embed>
							</object>
							{else}
							<img src="{$smarty.const.SITE_ADMIN_URL}{$params.IMAGE_DIR}{$photo1->path_file}{$photo1->name}" name="{$advertisement->img}" id="change1" border="0" width="300px" />
							{/if}
						</div>
					</td>
					<td nowrap="nowrap" colspan="2">
						<div id="informa" style="display: inline; width:380px; height:30px;">
							<b>{t}File:{/t} {$photo1->name}</b> <br /><b>{t}Dimensions:{/t}</b> {$photo1->width} x {$photo1->height} (px)<br />
							<b>{t}Weight:{/t}</b> {$photo1->size} Kb<br /><b>{t}Created:{/t}</b> {$photo1->created}<br />
							<b>{t}Description:{/t}</b> {$photo1->description} <br /><b>{t}Tags:{/t}</b> {$photo1->metadata}<br />
						</div>
						<div id="noimag" style="display: inline; width:380px; height:30px;"></div>
						<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>
					</td>
				</tr>
				</table>
			{else}
				<table border="0" width="96%">
				<tr>
					<td>
						<h2 style="color:#2f6d9d;">{t}Advertisement:{/t}</h2>
					</td>
					<td>
						<input type="hidden" id="input_img" name="img" title="{t}Image{/t}" value="{$advertisement->img}" size="70"/>
					</td>
				</tr>
				<tr>
					<td align="left">
						<div id="droppable_div1">
							<img src="../media/images/default_img.jpg" id="change1" border="0" width="300px" />
						</div>
					</td>
					<td nowrap="nowrap" colspan="2">
						<div id="informa" style="display: inline; width:380px; height:30px;">
								<b>{t}File:{/t} default_img.jpg</b> <br /><b>{t}Dimensions:{/t}</b> 300 x 208 (px)<br />
								<b>{t}Weight:{/t}</b> 4.48 Kb<br /><b>{t}Create:{/t}</b> 11/06/2008<br />
								<b>{t}Description{/t}</b> {t}Default image:{/t}<br /><b>{t}Tags:{/t}</b>{t}Image:{/t}<br />
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

        <div id="photos" class="photos" style="border:1px solid #ccc; {if $advertisement->with_script == 1}display:none;" {else} display:block;" {/if} >
			<em>{t}Drag and drop the advertisements to select them, use the logo to use flash{/t}</em><br><br>
			{if $paginacion}
				<p align="center">{t}Pages:{/t}{$paginacion} </p>
			{/if}
			<ul id='thelist'  class="gallery_list">
			   {assign var=num value='1'}
			   {section name=n loop=$photos}
					{if $photos[n]->content_status eq 1}
						<li>
							{if $photos[n]->type_img=='swf' || $photos[n]->type_img=='SWF'}
								<object style="z-index:3; cursor:default;"  title="Desc: {$photos[n]->description} Tags: {$photos[n]->metadata} ">
									<param name="movie" value="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}{$photos[n]->name}"> <param name="autoplay" value="false">  <param name="autoStart" value="0">
									<embed  width="68" height="40" style="cursor:default;" src="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}{$photos[n]->name}" name="{$photos[n]->pk_photo}" border="0" de:mas="{$photos[n]->name}" de:url="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:type_img="{$photos[n]->type_img}" de:description="{$photos[n]->description}" {* onmouseover="return escape('{$photos[n]->title}<br>{$photos[n]->description}');" *} title="{$photos[n]->title} - {$photos[n]->description}"></embed>
								</object>
								<span style="float:right;">
								<img id="draggable_img{$num}" class="draggable" src="{$smarty.const.SITE_ADMIN_URL}themes/default/images/flash.gif" style="width:20px" name="{$photos[n]->pk_photo}" border="0" de:mas="{$photos[n]->name}"
									 de:url="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:type_img="{$photos[n]->type_img}" de:description="{$photos[n]->description}" {* onmouseover="return escape('{$photos[n]->title}<br>{$photos[n]->description}');" *} title="Desc: {$photos[n]->description}  Tags: {$photos[n]->metadata}" /></span>
							{else}
								<div>
									<img style="vertical-align: middle;{cssimagescale resolution=68 photo=$photos[n]}" src="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}{$photos[n]->name}"
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
