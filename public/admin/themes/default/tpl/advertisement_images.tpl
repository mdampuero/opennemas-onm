<tr>
    <td>
     </td>
	<td nowrap="nowrap"  align="center" valign="top">
     <div id="width_warning" style="color:red;text-align:left;">
          
      </div>
    </td>
     <td>
      </td>
</tr>
<tr>	
	<td nowrap="nowrap" colspan="2" align="center" valign="top">
	  <div id="div_img_publi"  {if $advertisement->with_script == 1} style="display:none;" {else} style="display:inline;" {/if} >
		  <div id="nifty" style="width:560px;">
      			<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
		 		{if $photo1->name} 	 		
					<table border="0" width="96%">
					<tr>
                        <td>
                            <h2 style="color:#2f6d9d;">Anuncio Publicitario:</h2>
                        </td>
						<td>
                            <input type="hidden" id="input_img1" name="img" title="Imagen" value="{$advertisement->img}" size="70" />
                        </td>
					</tr>
                    <tr>
                        <td align='left'> 					 				
                            <div id="droppable_div1">
                                {if strtolower($photo1->type_img)=='swf'}
								<object>
                                    <param name="movie" value="{$MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}"></param>
                                    <embed src="{$MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}" width="300" ></embed>
                                </object>
								{else}
								<img src="{$MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}" id="change1" border="0" width="300px" />
								{/if}
							</div>					
                        </td>
                        <td nowrap="nowrap" colspan="2">		
						    <div id="informa" style="display: inline; width:380px; height:30px;">							      
								<b>Archivo: {$photo1->name}</b> <br /><b>Dimensiones:</b> {$photo1->width} x {$photo1->height} (px)<br />
								<b>Peso:</b> {$photo1->size} Kb<br /><b>Fecha de creaci&oacute;n:</b> {$photo1->created}<br />
								<b>Descripci&oacute;n:</b> {$photo1->description} <br /><b>Tags:</b> {$photo1->metadata}<br />
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
                            <h2 style="color:#2f6d9d;">Anuncio Publicitario:</h2>
                        </td>
						<td>
                            <input type="hidden" id="input_img1" name="img" title="Imagen" value="{$advertisement->img}" size="70"/>
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
									<b>Archivo: default_img.jpg</b> <br /><b>Dimensiones:</b> 300 x 208 (px)<br />
									<b>Peso:</b> 4.48 Kb<br /><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br />
									<b>Descripcion:</b> Imagen por defecto. <br /><b>Tags:</b> Imagen<br />
							</div>		
							<div id="noimag" style="display: inline; width:380px; height:30px;"></div>
                            <div id="noinfor" style="display: none; width:100%;  height:30px;"></div>
                        </td>
                    </tr>
                    </table>
		    	{/if}
                
                <br />
                <b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
		    </div>
        </div>						
	</td>
    
    <td align="center">
        
        <div id="photos" class="photos" style="margin-top: 10px; width:400px; height:440px; border:3px double #333333; padding:1px;overflow:auto;{if $advertisement->with_script == 1}display:none;" {else} style="display:inline;" {/if} >
				<em>Pinche y arrastre los anuncios para seleccionarlos, para flash utilice el logo. </em><br><br>
				 {if $paginacion}
					<p align="center">Paginas: {$paginacion} </p>
					{/if}
					 <ul id='thelist'  class="gallery_list" style="width:360px;"> 
					    {assign var=num value='1'}
						{section name=n loop=$photos}
							{if $photos[n]->content_status eq 1}
							 	 <li style="height:75px;"> 
								    {if $photos[n]->type_img=='swf' || $photos[n]->type_img=='SWF'}							    											   	
	                                            <object style="z-index:3; cursor:default;"  title="Desc: {$photos[n]->description} Tags: {$photos[n]->metadata} ">
										   			<param name="movie" value="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}{$photos[n]->name}"> <param name="autoplay" value="false">  <param name="autoStart" value="0">
										   			<embed  width="68" height="40" style="cursor:default;" src="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}{$photos[n]->name}" name="{$photos[n]->pk_photo}" border="0" de:mas="{$photos[n]->name}" de:url="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:type_img="{$photos[n]->type_img}" de:description="{$photos[n]->description}" {* onmouseover="return escape('{$photos[n]->title}<br>{$photos[n]->description}');" *} title="{$photos[n]->title} - {$photos[n]->description}"></embed>
										   		</object>
										   		<span style="float:right;"><img id="draggable_img{$num}" class="draggable" src="themes/default/images/flash.gif" style="width:20px" name="{$photos[n]->pk_photo}" border="0" de:mas="{$photos[n]->name}" de:url="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:type_img="{$photos[n]->type_img}" de:description="{$photos[n]->description}" {* onmouseover="return escape('{$photos[n]->title}<br>{$photos[n]->description}');" *} title="Desc: {$photos[n]->description}  Tags: {$photos[n]->metadata}" /></span>							   	
								   	{else}  <div>                              
								    			<img style="vertical-align: middle;{cssimagescale resolution=68 photo=$photos[n]}" src="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}{$photos[n]->name}" id="draggable_img{$num}" class="draggable" name="{$photos[n]->pk_photo}" border="0" de:mas="{$photos[n]->name}" de:url="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:type_img="{$photos[n]->type_img}"  de:description="{$photos[n]->description}"  title="Desc: {$photos[n]->description} Tags: {$photos[n]->metadata} " />
								  	 		</div> 
								  	{/if}
							    </li>						
								{literal}
									<script type="text/javascript">							 
									  new Draggable('draggable_img{/literal}{$num}{literal}', { revert:true, scroll: window, ghosting:true }  );
									</script>
								{/literal}
								 {assign var=num value=$num+1}
							 {/if}
						{/section}
					 </ul>
					
				</div>        
        
    </td>
</tr>
	

{literal}

<script type="text/javascript">
 
  Droppables.add('droppable_div1', { 
    accept: 'draggable',
    hoverclass: 'hover',
    onDrop: function(element) { 		  
		    if((element.getAttribute('de:type_img')=='swf') || (element.getAttribute('de:type_img')=='SWF')){
	    		 var ancho=element.getAttribute('de:ancho');
		  		 if(element.getAttribute('de:ancho')>300) {ancho=300;}
		         $('droppable_div1').innerHTML='<object ><param name="movie" value="'+ element.getAttribute('de:url') +'/'+element.getAttribute('de:mas')+ '"><embed src="'+ element.getAttribute('de:url') +'/'+element.getAttribute('de:mas')+ '" width="'+ancho+'" ></embed></object>';
		         $('informa').innerHTML=' es un Flash';
		         $('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created');
		  		 $('input_img1').value=element.name;		   		 
		   
		    } else {
			   		var source=element.src;			   	
		   			if($('change1')){$('change1').src = source;}
			  		$('input_img1').value=element.name;
			  		var ancho=element.getAttribute('de:ancho');
			  		if(element.getAttribute('de:ancho')>300) {ancho=300;}
			   		$('droppable_div1').innerHTML='<img src="'+ element.getAttribute('de:url') +'/'+element.getAttribute('de:mas')+ '"  id="change1" border="0" width="'+ancho+'" >';
			   		$('input_img1').value=element.name;
			   		$('informa').innerHTML=' ';
			   		$('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created');
			}
	  }
  });
  

</script>
{/literal}
