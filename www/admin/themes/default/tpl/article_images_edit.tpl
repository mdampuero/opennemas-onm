<table align="center" cellpadding="15" cellspacing="0" border="0" width="1000">
<tr>	
	<td nowrap="nowrap" >
	  <div id="img_portada"  style="display:block;">
		  <div id="nifty" style="width:560px;display:block;">
  			<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
		 		{if $video1->videoid} 	 		
						<table border='0' width="550">
						<tr><td>
							<h2 style="color:#2f6d9d;">Imagen de Portada:</h2></td>
							<input type="hidden" id="input_video" name="fk_video" value="{$article->pk_video}" size="70">				 							
							<input type="hidden" id="input_img1" name="img1" title="Imagen" value="" size="70"/>
						  <td  align='right'>  
						    <a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img1');">
	                 		    <img style="cursor:pointer;" src="themes/default/images/remove_image.png" id="remove_img1" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
	         			</td>					  
						 </tr><tr>
						 <td align='left'> 							 				 				
							  <div id="droppable_div1">	 																							
									<img src="http://i4.ytimg.com/vi/{$video1->videoid}/default.jpg" id="change1" name="{$video1->pk_video}" border="0" width="120px" />
								</div>					
					  	</td><td colspan="2" style="text-align:left;white-space:normal;" >
							    <div id="informa" style=" width:260px; overflow:auto;">
										<b>Archivo: {$video1->videoid}</b> <br><b>Fecha de creaci&oacute;n:</b> {$video1->created}<br>
										<b>Descripcion: </b>{$video1->description|clearslash|escape:'html'} <br><b>Tags:</b> {$video1->metadata}<br>
								</div>		
								<div id="noimag" style="display: inline; width:380px; height:30px;"></div>																								
							<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>											
			    		</td></tr></table>		
		    	{else}
			    	  {if $photo1->name}
			    	  	<table border='0' style="width:550px;">
							<tr><td>
								<h2 style="color:#2f6d9d;">Imagen de Portada:</h2></td>
								<input type="hidden" id="input_video" name="fk_video" value="" size="70">				 
								<input type="hidden" id="input_img1" name="img1" title="Imagen" value="{$article->img1}" size="70"/>
							  <td  align='right'>  
							    <a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img1');">
		                 		    <img style="cursor:pointer;" src="themes/default/images/remove_image.png" id="remove_img1" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
		         			</td>					  
							 </tr><tr>
							 <td align='left'> 							 				 				
								  <div id="droppable_div1">	 																							
										<img src="{$MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}" id="change1" name="{$article->img1}" border="0" width="300px" />
                                        
									</div>					
						  	</td><td colspan="2" style="text-align:left;white-space:normal;">
								    <div id="informa" style="text-align:left; width:260px;overflow:auto;">
											<b>Archivo: {$photo1->name}</b> <br><b>Dimensiones:</b> {$photo1->width} x {$photo1->height} (px)<br>
											<b>Peso:</b> {$photo1->size} Kb<br><b>Fecha de creaci&oacute;n:</b> {$photo1->created}<br>
											<b>Descripcion:</b> {$photo1->description|clearslash|escape:'html'} <br><b>Tags:</b> {$photo1->metadata}<br>
									</div>		
									<div id="noimag" style="display: inline; width:380px; height:30px;"></div>																								
								<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>											
				    		</td></tr></table>		
			    		{else}
				    		<table border='0' width="96%">
							<tr><td>
								<h2 style="color:#2f6d9d;">Imagen de Portada:</h2></td>
							 	<input type="hidden" id="input_video" name="fk_video" value="" size="70">				 
								<input type="hidden" id="input_img1" name="img1" title="Imagen" value="" size="70"/>
							  <td  align='right'> 
							   <a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img1');">
			                 		    <img style="cursor:pointer;" src="themes/default/images/remove_image.png" id="remove_img1" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
			         			</td>
							  
							 </tr><tr>
							 <td align='left'> 					 				
								  <div id="droppable_div1">	 																							
										<img src="../media/images/default_img.jpg" id="change1" name="{$article->img1}" border="0" width="300px" />
                                        
									</div>					
						  	</td>
                            <td colspan="2" style="text-align:left;white-space:normal;">
								    <div id="informa" style="text-align:left;width:260px; overflow:auto;">
											<b>Archivo: default_img.jpg</b> <br><b>Dimensiones:</b> 300 x 208 (px)<br>
											<b>Peso:</b> 4.48 Kb<br><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br>
											<b>Descripcion: </b>Imagen por defecto.<br><b>Tags:</b> Imagen<br>											
									</div>		
									<div id="noimag" style="display: inline; width:380px; height:30px;"></div>																								
								<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>											
				    		</td></tr>
                        </table>
			    	{/if}			    
                {/if}
                                
				
                <div id="footer_img_portada"> <label for="title">Pie imagen Portada:</label>
					<input type="text" id="img1_footer" name="img1_footer" title="Imagen" value="{$article->img1_footer|clearslash|escape:"html"}" size="50" />
				</div>
          
                <br />
	        <b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
		    </div>
		   </div>
				<input type="hidden" id="posic" name="posic" value="0" />		
	
	</td>
	<td valign="top" align="right"  rowspan="3" >
			  <div  onclick="new Effect.toggle($('photos_container'),'blind')" style="cursor:pointer;width:438px;height:22px; border:1px double #333333;background-color:#EEE; padding:4px;overflow:none;">
			 	<a style="cursor:pointer;" onclick="new Effect.toggle($('photos_container'),'blind')"><b> Fotos </b></a>
			  </div>
			  <div id="photos_container" class="photos" style="width:440px; height:570px; border:3px double #333333; padding:1px;overflow:hidden;">
				   <br>
                    <table width="90%">
                        <tr>
                            <td align="left">
                                <div class="cajaBusqueda">
                                    <input id="stringImageSearch" name="stringImageSearch" type="text"   onkeypress="onImageKeyEnter(event, $('category_imag').options[$('category_imag').selectedIndex].value,encodeURIComponent($('stringImageSearch').value),1);" onclick="this.select();" value="Busqueda Imagenes..." />
                                </div>
                            </td>
                            <td align="right">
                               <select id="category_imag" name="category_imag" class="required" onChange="get_images(this.options[this.selectedIndex].value,1, 'list_by_category',0);">
                                    <option value="0">GLOBAL</option>
                                    {section name=as loop=$allcategorys}
										<option value="{$allcategorys[as]->pk_content_category}" {if $article->category  eq $allcategorys[as]->pk_content_category} selected {/if}>{$allcategorys[as]->title}</option>						
									 	{section name=su loop=$subcat[as]}
											<option value="{$subcat[as][su]->pk_content_category}" {if $article->category  eq $subcat[as][su]->pk_content_category} selected {/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>				  					   	     
							   	      	{/section}
                                    {/section}
                                </select>
                            </td>
                        </tr>
                    </table>

									
						<div id="photos" class="photos" style="width:440px; height:560px; border:0px double #333333; padding:1px;overflow:hidden;">											   
		 					{if $paginacion}
								<p align="center"> {$paginacion} </p>
							{/if}
							 <ul id="thelist"  class="gallery_list" style="width:400px;"> 
							    {assign var=num value='1'}
								{section name=n loop=$photos}
								{if $photos[n]->content_status eq 1 }                                                                
								  <li><div>
                                
                                  <a>
								    <img style="vertical-align:middle;{cssimagescale resolution=67 photo=$photos[n]}" src="{$MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}140x100-{$photos[n]->name}" id="draggable_img{$num}" class="draggable" name="{$photos[n]->pk_photo}" border="0" de:mas="{$photos[n]->name}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}" de:description="{$photos[n]->description|clearslash|escape:'html'}" de:tags="{$photos[n]->metadata}" {* onmouseover="return escape('Desc:{$photos[n]->description|clearslash|escape:'html'}<br>Tags:{$photos[n]->metadata}');" *} title="Desc:{$photos[n]->description|clearslash|escape:'html'} - Tags:{$photos[n]->metadata}" />
								    </a> </div></li>						
									{literal}
										<script type="text/javascript">							 
										  new Draggable('draggable_img{/literal}{$num}{literal}', { revert:true, scroll: window, ghosting:true }  );
										</script>
									{/literal}
									 {assign var=num value=`$num+1`}
								{/if}
								{/section}
							 </ul>
						</div>
				</div>		
				<div  onclick="new Effect.toggle($('videos-container'),'blind')" style="cursor:pointer;width:438px;height:22px; border:1px double #333333;background-color:#EEE;padding:4px;overflow:none;">
				 	<a style="cursor:pointer;" onclick="new Effect.toggle($('videos'),'blind')"> <b>Videos</b> </a>
				 </div>

				 <div id="videos-container" class="photos" style="width:440px; height:400px; border:3px double #333333; padding:1px;overflow:hidden;">
                    <br>
                    <table width="90%"><tr><td><div class="cajaBusqueda" style="width:100%;" align="left"><input class="textoABuscar" id="stringVideoSearch" name="stringVideoSearch" type="text"  onkeypress="onVideoKeyEnter(event, $('stringVideoSearch').value,1);" onclick="this.select();" value="Busqueda Videos..." align="left"/></div></td></tr></table>
                    <br>
                    <div id="videos" class="photos" style="width:440px; height:380px; border:0px double #333333; padding:1px;overflow:auto;">
                         {if $paginacionV}
                            <p align="center"> {$paginacionV} </p>
                        {/if}
                         <ul id='thelist'  class="gallery_list" style="width:440px;">
                            {assign var=num value='1'}
                            {section name=n loop=$videos}
                            {if $videos[n]->content_status eq 1 }
                              <li><div style="float: left;"> <a>
                                    <img class="video"  width="67" id="draggable_video{$num}" name="{$videos[n]->pk_video}" alt="{$videos[n]->title}" qlicon="{$videos[n]->videoid}" src="http://i4.ytimg.com/vi/{$videos[n]->videoid}/default.jpg" title="{$videos[n]->title}" de:created="{$videos[n]->created}" de:description="{$videos[n]->description|clearslash|escape:'html'}" de:tags="{$videos[n]->metadata}"  {* onmouseover="return escape('Desc:{$videos[n]->description|clearslash|escape:'html'}<br>Tags:{$videos[n]->metadata}');" *} title="Desc:{$videos[n]->description|clearslash|escape:'html'} - Tags:{$videos[n]->metadata}" />
                                </a> </div></li>
                                {literal}
                                    <script type="text/javascript">
                                      new Draggable('draggable_video{/literal}{$num}{literal}', { revert:true, scroll: window, ghosting:true }  );
                                    </script>
                                {/literal}
                                 {assign var=num value=`$num+1`}
                            {/if}
                            {/section}
                         </ul>
                    </div>
				</div>				
	</td>
</tr>
	
<tr>

	<td>
	   <div id="img_interior"  style="display:block;">
	       <div id="nifty" style="width:560px;display:block;">
  			<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>		 		
				{if $photo2->name} 	 
					  <table border='0' width="550">
						 <tr><td>
							<h2 style="color:#2f6d9d;">Imagen Interior:</h2></td>
						 <td align='right'> 
						 	<a onclick="javascript:recuperar_eliminar('img2');">
	                    	 <img src="themes/default/images/remove_image.png" id="remove_img2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
	         			</td>
						 </tr><tr>
						 <td align='left'> 						  
						    <input type="hidden" id="input_img2" name="img2" value="{$article->img2}" size="100">				 
						   <div id="droppable_div2">						 																			
							 <img src="{$MEDIA_IMG_PATH_WEB}{$photo2->path_file}{$photo2->name}" id="change2" name="{$article->img2}" border="0" width="300px" />
                             
							</div>
						</td>	<td colspan="2" style="text-align:left;white-space:normal;" width="400">
							  <div id="informa2" style="text-align:left;width:260px; overflow:auto;">
										<b>Archivo: {$photo2->name}</b> <br><b>Dimensiones:</b> {$photo2->width} x {$photo2->height} (px)<br>
										<b>Peso:</b> {$photo2->size} Kb<br><b>Fecha de creaci&oacute;n:</b> {$photo2->created}<br>
										<b>Descripcion: </b> {$photo2->description|clearslash|escape:'html'} <br><b>Tags:</b> {$photo2->metadata}<br>
										
								</div>
							 <div id="noimag2" style="display: inline; width:380px; height:30px;">	</div>
							 <div id="noinfor2" style="display: none; width:100%; height:30px;"></div>									
					</td></tr></table>
				{else}
			    		<table border='0' width="96%">
						<tr><td>
							<h2 style="color:#2f6d9d;">Imagen Interior:</h2></td>
							<input type="hidden" id="input_img2" name="img2" title="Imagen" value="{$article->img2}" size="70"/>
						  <td  align='right'> 	
							  <a onclick="javascript:recuperar_eliminar('img2');">
		                    	 <img src="themes/default/images/remove_image.png" id="remove_img2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>	         			
						  </td>
						  
						 </tr><tr>
						 <td align='left'> 					 				
							  <div id="droppable_div2">	 																							
									<img src="../media/images/default_img.jpg" id="change2" name="{$article->img2}" border="0" width="300px" />
                                    
								</div>					
					  	</td><td colspan="2" style="text-align:left;white-space:normal;" width="400">
							    <div id="informa2" style="text-align:left;width:260px; overflow:auto;">
										<b>Archivo: default_img.jpg</b> <br><b>Dimensiones:</b> 300 x 208 (px)<br>
										<b>Peso:</b> 4.48 Kb<br><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br>
										<b>Descripcion:</b>  Imagen por defecto.  <br><b>Tags:</b> Imagen<br>
										
								</div>		
								<div id="noimag2" style="display: inline; width:380px; height:30px;"></div>																								
							<div id="noinfor2" style="display: none; width:100%;  height:30px;"></div>											
			    		</td></tr></table>
		    	{/if}			
					
				<div id="footer_img_interior"> <label for="title">Pie imagen Interior:</label>
					<input type="text" id="img2_footer" name="img2_footer" title="Imagen" value="{$article->img2_footer|clearslash|escape:"html"}" size="50" />
				   </div> <br>
				<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
		    </div>
		   </div>
		   <br>
	</td>
</tr>

<tr>

	<td>
	   <div id="video_interior"  style="display:block;">
	       <div id="nifty" style="width:560px;display:block;">
  			<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>		 		
				 {if $video2->videoid} 	 		
						<table border='0' width="550"">
						<tr><td>
							<h2 style="color:#2f6d9d;">Video Interior:</h2></td>
							<input type="hidden" id="input_video2" name="fk_video2" title="Imagen" value="{$article->fk_video2}" size="70"/>
						  <td  align='right'>  
						    <a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('video2');">
	                 		    <img style="cursor:pointer;" src="themes/default/images/remove_image.png" id="remove_video2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
	         			</td>					  
						 </tr><tr>
						 <td align='left'> 							 				 				
							  <div id="droppable_div3">	 																							
									<img src="http://i4.ytimg.com/vi/{$video2->videoid}/default.jpg"  id="change3" name="{$article->fk_video2}" border="0" width="120px" />
                                    
								</div>					
					  	</td><td colspan="2" style="text-align:left;white-space:normal;">
							    <div id="informa3" style="text-align:left;width:260px; overflow:auto;">
										<b>Codigo: {$video2->videoid}</b> 
										<br><b>Fecha de creaci&oacute;n:</b> {$video2->created}<br>
										<b>Descripcion: </b> {$video2->description|clearslash|escape:'html'} <br><b>Tags:</b> {$video2->metadata}<br>
								</div>		
								<div id="noimag3" style="display: inline; width:380px; height:30px;"></div>																								
							<div id="noinfor3" style="display: none; width:100%;  height:30px;"></div>											
			    		</td></tr></table>		
		    	{else}
				  <table border='0' width="96%">
					 <tr><td>
					<h2 style="color:#2f6d9d;">Video Interior:</h2></td>
					 <td  align='right'>   
					 	<a style="cursor:pointer;"  onclick="javascript:recuperar_eliminar('video2');">
                     	<img src="themes/default/images/remove_image.png" id="remove_video2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
                        
         			 </td>
					 </tr><tr>
					 <td align='left'> 
					  
					    <input type="hidden" id="input_video2" name="fk_video2" value="" size="70">				 
					   <div id="droppable_div3">						 																			
						 <img src="../media/images/default_img.jpg" id="change3" name="default_img" border="0" width="300px" />						
						</div>
					</td>	<td colspan="2" style="text-align:left;white-space:normal;">
						 <div id="informa3"  style="text-align:left;overflow:auto;width:260px;>
							   <b>Archivo: default_img.jpg</b> <br> 
							   <b>Peso:</b> 4.48 Kb<br><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br>
							   <b>Descripcion: </b> Imagen por defecto. <br><b>Tags:</b> imagen<br>
						 </div>
						 <div id="noimag3" style="display: inline; width:380px; height:30px;">	</div>
						 <div id="noinfor3" style="display: none; width:100%; height:30px;"></div>									
					</td></tr></table>
						
				{/if}	
				<div id="video2_footer"> <label for="title">Pie Video Interior:</label>
					<input type="text" id="footer_video2" name="footer_video2" title="video interior footer" value="{$article->footer_video2|clearslash|escape:"html"}" size="50" />
				   </div> <br>
				<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
		    </div>
		    <br>
		   </div>
		   <br>
	</td>

</tr>
</table>

{if !$article->isClone()}
{literal}
<script type="text/javascript">
  Droppables.add('droppable_div1', { 
    accept: ['draggable', 'video'], 
    hoverclass: 'hover',
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
                                    $('change1').setAttribute('width', ancho);
		  		}
		   		
		   		$('informa').innerHTML=' ';
		   		if(element.getAttribute('class')=='draggable'){
		   		    $('input_img1').value=element.name;
		   		     $('input_video').value='';
			   		$('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') + "<br><b>Tags: </b>" + element.getAttribute('de:tags');
			   		$('img1_footer').value= element.getAttribute('de:description'); 
		   		}else{
		   		    $('input_video').value=element.name;
		   		    $('input_img1').value='';
		   			$('informa').innerHTML="<b>Codigo: </b>"+element.getAttribute('title')  + "<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') + "<br><b>Tags: </b>" + element.getAttribute('de:tags');
		   			$('img1_footer').value= element.getAttribute('de:description'); 
		   		}
                
                // En firefox 2, precísase reescalar o div co alto da imaxe
                if( /Firefox\/2/.test(navigator.userAgent) ) {
                    $('droppable_div1').style.height = $('change1').height + 'px';
                }
		     }
  });
  
  Droppables.add('droppable_div2', { 
    accept: 'draggable',
    hoverclass: 'hover',
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
		   		$('informa2').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') +"<br><b>Tags: </b>" + element.getAttribute('de:tags');
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
		   		$('informa3').innerHTML="<b>Codigo: </b>"+element.getAttribute('title')  +  "<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') + "<br><b>Tags: </b>" + element.getAttribute('de:tags');
		   		$('footer_video2').value= element.getAttribute('de:description'); 
		     }
  });
  

</script>
{/literal}
{/if}