{literal}
    <style type="text/css">
	

		#testWrap {
			width: 600px;
                        overflow:hidden;
			float: left;
			margin: 20px 0 0 50px; /* Just while testing, to make sure we return the correct positions for the image & not the window */
		}

		#previewArea {
			margin: 20px; 0 0 20px;
			float: left;
                        background:#F5F5F5 none repeat scroll 0 0;
                        border:2px double #333333;
		}		
	</style>

{/literal}


<tr>
    <td colspan="3">
        <br><label>{t}Album images (Double click to select and cut images){/t}</label><br>
	 <div id="scroll-album">
		 <ul class="gallery_list" id="album_list">
            {if $oldphoto}
		 		 {assign var=indi value='1'}					
				 {section name=n loop=$oldphoto}	
				 {if $oldphoto[n]->content_status eq 1}				
					<li value="{$MEDIA_IMG_URL}{$oldphoto[n]->path_file}{$oldphoto[n]->name}" de:pk_photo="{$oldphoto[n]->pk_photo}"  id="f{$indi}-{$oldphoto[n]->pk_photo}">
					    <a class="album" onClick="show_image('img{$oldphoto[n]->pk_photo}', 'f{$indi}-{$oldphoto[n]->pk_photo}')" title={t}"Show image"{/t}>
					     <img ondblclick="define_crop(this);"  style="{cssimagescale resolution=67 photo=$oldphoto[n]}" src="{$MEDIA_IMG_URL}{$oldphoto[n]->path_file}{$oldphoto[n]->name}"
                            class="draggable2" id="img{$oldphoto[n]->pk_photo}" de:pk_photo="{$oldphoto[n]->pk_photo}"  value="f{$indi}-{$oldphoto[n]->pk_photo}" name="{$oldphoto[n]->name}" de:mas="{$oldphoto[n]->name}"   de:path="{$oldphoto[n]->path_file}"  border="0" de:dimensions="{$oldphoto[n]->width} x {$oldphoto[n]->height} (px)" de:peso="{$oldphoto[n]->size}" de:created="{$oldphoto[n]->created}"  de:description="{$oldphoto[n]->description|escape:"html"}"  de:tags="{$oldphoto[n]->metadata}"   de:footer="{$oldphotos[n][2]|escape:"html"}"/><br>
					   </a></li>                                          
                                    {assign var=indi value=$indi+1}
                                {/if}
				 {/section}		
			{/if}
		</ul>
	</div>	
    </td>
 </tr> <tr>
    <td style="height:60px;" colspan="3">  <div id="album_msg" style="display:none;"></div>
    </td>
</tr> 

<tr>
    <td></td><td></td>
	<td valign="top" align="right" rowspan="2">	
		<br>
                  <table width="90%">
                        <tr>
                            <td align="left">
                                <div class="cajaBusqueda">
                                    <input id="stringImageSearch" name="stringImageSearch" type="text"   onkeypress="onImageKeyEnter(event, $('category_imag').options[$('category_imag').selectedIndex].value,$('stringImageSearch').value,1);" onclick="this.select();" value="Busqueda Imagenes..." />
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
                                     {section name=as loop=$othercategorys}
                                         <option value="{$othercategorys[as]->pk_content_category}" {if $article->category  eq $othercategorys[as]->pk_content_category} selected {/if}>{$othercategorys[as]->title}</option>
                                                {section name=su loop=$othersubcat[as]}
                                                        <option value="{$othersubcat[as][su]->pk_content_category}" {if $article->category  eq $othersubcat[as][su]->pk_content_category} selected {/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$othersubcat[as][su]->title}</option>
                                                {/section}
                                    {/section}

                                </select>
                            </td>
                        </tr>
                    </table>
                    
                <div class="photos" id="photos" style="width:430px; height:540px; border:3px double #333333; padding:1px;">
                        <em>{t}Drag and drop images to select{/t}</em>
                        {if count($photos) gt 0}
                                        <p align="center">{$paginacion}</p>
                        {/if}
                         <ul id='thelist'  class="gallery_list" style="width:400px;">
                            {assign var=num value='1'}
                                {section name=n loop=$photos}
                                 {if $photos[n]->content_status eq 1 && $photos[n]->media_type neq 'graphic'}
                                  <li><div style="float: left;"> <a>
                                     {if $photos[n]->type_img|lower eq 'jpg' || $photos[n]->type_img|lower eq 'jpeg'}
                                         <img style="{cssimagescale resolution=67 photo=$photos[n]}" de:width="{cssimagescale resolution=67 photo=$photos[n] getwidth=1}" src="{$MEDIA_IMG_URL}{$photos[n]->path_file}140x100-{$photos[n]->name}" id="draggable_img{$num}" class="draggable" name="{$photos[n]->pk_photo}" de:path="{$photos[n]->path_file}" border="0" de:mas="{$photos[n]->name}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}"  de:description="{$photos[n]->description}" de:tags="{$photos[n]->metadata}"  title="Desc: {$photos[n]->description} Tags: {$photos[n]->metadata} " alt="{$photos[n]->description}"/>
                                     {else}
                                          <img style="{cssimagescale resolution=67 photo=$photos[n]}" de:width="{cssimagescale resolution=67 photo=$photos[n] getwidth=1}" src="{$MEDIA_IMG_URL}{$photos[n]->path_file}{$photos[n]->name}" id="draggable_img{$num}" class="draggable" name="{$photos[n]->pk_photo}" de:path="{$photos[n]->path_file}" border="0" de:mas="{$photos[n]->name}" de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}" de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}"  de:description="{$photos[n]->description}" de:tags="{$photos[n]->metadata}"  title="Desc: {$photos[n]->description} Tags: {$photos[n]->metadata} " alt="{$photos[n]->description}"/>
                                     {/if}
                                    </a> </div></li>
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
<tr>
	<td colspan="2">
       
	<div id="portada" style="width:420px;display:inline;">           	
		<div id="imgdes">		  
		   <div id="nifty" style="width:580px;display:block;">
                       <b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
			   <table border="0">
                               <tr><td>
				   <h2 style="color:#2f6d9d;">
				   <img src="themes/default/images/tit_img.png" style="margin-bottom:-15px;"/> {t}Image{/t}
				   <hr style=" margin:0;border-style: solid; color:#2f6d9d; border-width: 2px;"></h2>
                                </td>
                                <td  align='right'>
                                    <a style="cursor:pointer;" id="remove" onclick="">
                                    <img src="themes/default/images/remove_image.png" id="remove_img" alt={t}"Delete"{/t} title={t}"Delete"{/t} border="0" align="absmiddle" /> </a>
                                </td>
                                </tr>
                                <tr><td colspan=2>
                                    <label for="title">{t}Image:{/t} </label>
                                    <input type="hidden" id="img_des" value="" size="60">
                                    </td></tr><tr><td  style="width:50%" nowrap>
                                     <img src="{$params.IMAGE_DIR}default_img.jpg" id="image_view" name="imag_view" width=300 border="0" />
                                    </td><td style="padding:10px;">
                                    <div id="informa" style="text-align:left;display: inline; width:380px; height:30px;">
                                            <b>{t}File:{/t} default_img.jpg</b> <br><b>{t}Dimensions:{/t}</b> 300 x 208 (px)<br>
                                            <b>{t}Weight:{/t}</b> 4.48 Kb<br><b>{t}Created:{/t}</b> 11/06/2008<br>
                                            <b>{t}Description:{/t}</b>  {t}Default image:{/t}  <br><b>{t}Tags:{/t}</b> {t}Image{/t}<br>
                                    </div>
                                    <br>
                                </td></tr>
                                <tr><td colspan=2>
                                    <label for="title">{t}Foot image:{/t}</label>
                                    <input type="text" id="img_footer" name="img_footer" title="Imagen" value="" size="60" />
                                </td></tr></table>
                         <b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
                    </div>
                </div>						
          

</div>	 
	   <input type="hidden" id="ordenAlbum" name="ordenAlbum" value=""></input>	   
</td>
</tr>

<tr>
    <td colspan="3">
        {t}Cut the image that is in frontpage view.(300x250 px){/t}

             <div id="testWrap">
                    <img src="{$params.IMAGE_DIR}default_img.jpg" alt={t}"test image"{/t} id="testImage"  width="300" height="208" />
            </div>
             <div id="previewArea"> {if !empty($album->cover)} <img id="crop_img" src="{$MEDIA_IMG_URL}{$album->cover}" alt={t}"Frontpage image"{/t}  style="maxWidth:600px;maxHeight:400px;" /> {/if} </div>
             <div id="results">
                      <input type="hidden" name="x1" id="x1" value=""/>
                 <br />
                      <input type="hidden" name="y1" id="y1" value=""/>

                      <input type="hidden" name="width" id="width" value="" />
                      <input type="hidden" name="height" id="height" value=""/>

                    <input type="hidden" name="path_img" id="path_img" value=""/>
                    <input type="hidden" name="name_img" id="name_img" value=""/>
               
             </div>
    </td>
</tr>

{literal}
<script type="text/javascript">

  	album_make_mov();
      
  function define_crop(element) {

              if(crop != null) {
                 crop.remove();
              }
                if($('crop_img')) {
                    $('crop_img').src='';
                }
                $$('#previewArea img').each(function(e){e.src='';}); //Para ocultar las de debajo
                $('testImage').src = '/media/images/'+element.getAttribute('de:path')+element.getAttribute('de:mas');
                $('path_img').value = element.getAttribute('de:path');
                $('name_img').value = element.getAttribute('de:mas');
                var a=element.getAttribute('de:dimensions');
                var b = a.split(' x ');
                var c = b[1].split(' (px)');
                  $('testImage').width=b[0];
                $('testImage').height=c[0];
                if(c[0]>b[0]){
                    if(c[0]>400){
                        var w=Math.floor( (b[0]*400) / c[0] );
                         $('testImage').setStyle({
                              height: '400px',
                             width :  w +"px"
                            });
                     }else{
                         $('testImage').setStyle({
                                  height: c[0]+'px',
                                 width :  b[0]+'px'
                                });
                    }
                     
                } else {
                    if(b[0]>600){
                           var h=Math.floor( (c[0]*600) / b[0] );
                            $('testImage').setStyle({
                              width: '600px',
                              height : h +"px"
                            });
                    }else{
                     $('testImage').setStyle({
                              height: c[0]+'px',
                             width :  b[0]+'px'
                            });
                    }
                }

                if(b[0]<300){alert('La foto escogida para portada no supera los 300px de ancho');
                }else{ if(c[0]<240){alert('La foto escogida para portada no supera los 240px de alto');}
                }
                cropcreate();
 }

Droppables.add('testWrap', {
    accept: 'draggable2',
    onDrop: function(element, droppable) {
        if(crop != null) {
            crop.remove();
        }
        $('testImage').src  = element.src;
        $('path_img').value = element.getAttribute('de:path');
        $('name_img').value = element.getAttribute('de:mas');

        var a = element.getAttribute('de:dimensions');
        var b = a.split(' x ');
        var c = b[1].split(' (px)');
        
        $('testImage').width  = b[0];
        $('testImage').height = c[0];

        cropcreate();
    }
});

//CROP
var crop = null;
function cropcreate() {
    crop = new Cropper.ImgWithPreview('testImage', {
        minWidth: 300,
        minHeight: 240,
        ratioDim: { x: 300, y: 240 },
        displayOnInit: true,
        onEndCrop: onEndCrop,
        previewWrap: 'previewArea'
    });
}

function onEndCrop( coords, dimensions ) {
    $( 'x1' ).value = coords.x1;
    $( 'y1' ).value = coords.y1;

    $( 'width' ).value  = dimensions.width;
    $( 'height' ).value = dimensions.height;
}
</script>
{/literal}
