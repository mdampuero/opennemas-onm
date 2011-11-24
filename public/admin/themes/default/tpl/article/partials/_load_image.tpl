<div id="imgHomePortada" style=" display:block; border-bottom:1px solid #ccc; background:#eee; padding:10px">
    <table style="width:100%;">
        <tr>
            <td>
                <label>{t}Image for home frontpage:{/t}</label>
                <input type="hidden" id="inputVideo" name="params[videoHome]" value="" size="70">
            </td>
            <td  align='right'>
                <a style="cursor:pointer;"  onclick="javascript:recuperar_eliminar('imgHome');">
                    <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_imgHome" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" />
                </a>
            </td>
        </tr>
        <input type="hidden" id="input_imgHome" name="params[imgHome]" title="Imagen" value="{$article->params['imgHome']|default:""}" size="70"/>
        <tr>
            <td align='center'>
                <div id="droppableHome_div">
                    {if isset($photoHome) && $photoHome->name}
                        {if strtolower($photoHome->type_img)=='swf'}
                            <object id="changeHome"  name="{$article->params['imgHome']}" >
                                <param name="movie" value="{$smarty.const.MEDIA_IMG_PATH_URL}{$photoHome->path_file}{$photoHome->name}"></param>
                                <embed src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photoHome->path_file}{$photoHome->name}" width="300" ></embed>
                            </object>
                        {else}
                            <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoHome->path_file}{$photoHome->name}" id="changeHome" name="{$article->params['imgHome']}" border="0" width="300px" />
                        {/if}
                    {else}
                        <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_img.jpg" id="changeHome" name="default_img" border="0" width="300px" />
                    {/if}
                </div>
            </td>
            <td colspan="1" style="text-align:left;white-space:normal;">
                <div id="informaHome" style="text-align:left; width:260px;overflow:auto;">
                    <p><strong>{t}File name:{/t}</strong><br/> {$photoHome->name|default:'default_img.jpg'}</p>
                    <p><strong>{t}Size:{/t}</strong><br/> {$photoHome->width|default:0} x {$photoHome->height|default:0} (px)</p>
                    <p><strong>{t}File size:{/t}</strong><br/> {$photoHome->size|default:0} Kb</p>
                    <p><strong>{t}File creation date{/t}:</strong><br/> {$photoHome->created|default:""}</p>
                    <p><strong>{t}Description:{/t}</strong><br/> {$photoHome->description|default:""|clearslash|escape:'html'}</p>
                    <p><strong>Tags:</strong><br/> {$photoHome->metadata|default:""}</p>
                </div>
                <div id="noimagHome" style="display: inline; width:100%; height:30px;"></div>
                <div id="noinforHome" style="display: none; width:100%;  height:30px;"></div>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div id="footerImgHome">
                    <label for="title">{t}Footer text for frontpage image:{/t}</label>
                    <input type="text" id="imgHomeFooter" name="params[imgHomeFooter]" title="Imagen" value="{$article->params['imgHomeFooter']|clearslash|escape:'html'}" size="50" />
                </div>
            </td>
        </tr>
    </table>
</div>

<script type="text/javascript" language="javascript">
function makeDroppable() {   
 
Droppables.add('droppableHome_div', {
    accept: ['draggable'],
    onDrop: function(element) {
 
            if((element.getAttribute('de:type_img')=='swf') || (element.getAttribute('de:type_img')=='SWF')){
                var ancho=element.getAttribute('de:ancho');
                if(element.getAttribute('de:ancho')>300) { ancho=300; }
                $('droppableHome_div').innerHTML='<object id="changeHome"><param name="movie" value="'+
                                               element.getAttribute('de:url') +'/'+ element.getAttribute('de:mas')
                                               + '"><embed src="'+ element.getAttribute('de:url')
                                               +'/'+element.getAttribute('de:mas')+ '" width="'+ancho+'" ></embed></object>';
                $('informaHome').innerHTML=' es un Flash';
                $('informaHome').innerHTML="<strong>Archivo: </strong><br/>"+element.getAttribute('de:mas') + "<br><strong>Dimensiones: </strong><br/>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><strong>Peso: </strong><br/>" + element.getAttribute('de:peso') + "Kb<br><strong>Fecha Creaci&oacute;n: </strong><br/>" + element.getAttribute('de:created');
                $('input_imgHome').value=element.name;

           } else {
               var source=element.src;
               if($('changeHome').src){
                   recuperarOpacity('imgHome');

                   if(element.getAttribute('class')=='draggable'){
                       $('changeHome').src = source.replace( '140-100-','');
                   }else{
                       $('changeHome').src = source;
                   }
                   $('changeHome').name=element.name;
                   var ancho=element.getAttribute('de:ancho');
                   if(element.getAttribute('de:ancho')>300) { ancho=300; }
                   $('changeHome').setAttribute('width',ancho);
               }else{
                    var ancho=element.getAttribute('de:ancho');
                    $('droppableHome_div').innerHTML= '<img src="'+ source.replace( '140-100-','') + '"  id="changeHome" border="0" style="max-width: 300px;" width="'+ancho+'" >';
               }
               $('informa').innerHTML=' ';
               if(element.getAttribute('class')=='draggable'){
                   $('input_imgHome').value=element.name;
                   $('informa').innerHTML= " <p><strong>{t}File name:{/t}</strong><br/> " + element.getAttribute('de:mas') + "</p>"+
                       "<p><strong>{t}Size:{/t}:</strong><br/> "+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + "(px)</p>"+
                       "<p><strong>{t}File size:{/t}</strong><br/> " + element.getAttribute('de:peso') + " Kb</p>"+
                       "<p><strong>{t}File creation date{/t}:</strong><br/> " + element.getAttribute('de:created') + "</p>"+
                       "<p><strong>{t}Description:{/t}</strong><br/> " + element.getAttribute('de:description') +"</p>"+
                       "<p><strong>Tags:</strong><br/> "+ element.getAttribute('de:tags')+"</p> ";
                   $('img1_footer').value= element.getAttribute('de:description');
                   $('input_video').value='';
                 //  galleryPutMini(element.id);
               }else{
                  $('inputVideoHome').value=element.name;
                  $('informaHome').innerHTML="<strong>Codigo: </strong><br/>"+element.getAttribute('title')  + "<br><strong>Fecha Creaci&oacute;n: </strong><br/>" + element.getAttribute('de:created') + "<br><strong>Descripcion: </strong><br/>" + element.getAttribute('de:description') +"<br><strong>Tags: </strong><br/>" + element.getAttribute('de:tags');
                  $('imgHomeFooter').value= element.getAttribute('de:description');
                  $('input_imgHome').value='';
               }
               // En firefox 2, precísase reescalar o div co alto da imaxe
               if( /Firefox\/2/.test(navigator.userAgent) ) {
                   $('droppableHome_div').style.height = $('changeHome').height + 'px';
               }
           }

        }
  });  
}
 // JavaScript Document
var num=0
  
  function galleryPutMini(id){
	
	var ul = $('album_list');
	Nodes = $('album_list').select('img');
    for (var i=0;i < Nodes.length;i++) {
        if(Nodes[i].getAttribute('de:mas')==$(id).getAttribute('de:mas')){
        
         
         showMsgContainer( { 'warn':['La imagen ya esta en el album ' ] },'inline','album_msg');
             return false;
        }
    }
    hideMsgContainer('album_msg');
    num++;
	var pkfoto=$(id).name;
    var imag=$(id).src;
	 
	var del='n'+num+'-'+pkfoto;
	li= document.createElement('li');  		
	li.setAttribute('id', del);	
	li.setAttribute('de:pk_photo', pkfoto);				
	li.setAttribute('value', imag);			
	li.setAttribute('class', 'family');
	li.setAttribute('style', 'cursor: move; list-style-type: none;');		

	a = document.createElement('A');		 
	a.title='Mostrar imagen';
	var funcion='show_image("img'+pkfoto+'","'+del+'")';
	a.setAttribute('onclick', funcion);
	a.className = 'album';
	a.setAttribute('class', 'album');
	min = document.createElement('img');
        min.id= 'img'+pkfoto;
        min.border=1;
        min.src= imag;
        min.setAttribute('name', $(id).name);
        min.setAttribute('de:pk_photo', pkfoto);
        min.setAttribute('value', 'n'+num+'-'+pkfoto);
        min.setAttribute('de:created', $(id).getAttribute('de:created'));
        min.setAttribute('de:peso', $(id).getAttribute('de:peso'));
        min.setAttribute('de:dimensions', $(id).getAttribute('de:ancho') + " x " +$(id).getAttribute('de:alto') );
        min.setAttribute('de:description', $(id).getAttribute('de:description'));
        min.setAttribute('de:footer', $(id).getAttribute('de:description'));
        min.setAttribute('de:tags', $(id).getAttribute('de:tags'));
        min.setAttribute('de:path', $(id).getAttribute('de:path'));
        min.setAttribute('de:mas', $(id).getAttribute('de:mas'));
        min.setAttribute('class', 'draggable2');
        min.setAttribute('ondblclick', 'define_crop(this)');
        

        if(($(id).getAttribute('de:type_img')=='swf') || ($(id).getAttribute('de:type_img')=='SWF')){
            min.setAttribute('style','width:16px; height:16px;');
            min.setAttribute('de:url', $(id).getAttribute('de:url'));
            min.setAttribute('de:type_img', $(id).getAttribute('de:type_img'));
            min.setAttribute('ondblclick', 'return false;');
            span = document.createElement('span');
            span.setAttribute('style','float: right; clear: none;');
            span.appendChild(min);
            div = document.createElement('div');
            div.innerHTML='<object id="image_view" de:type_img="'+ $(id).getAttribute('de:type_img')+">"
                                                    +'<param name="movie" value="'+ $(id).getAttribute('de:url') +'/'+ $(id).getAttribute('de:mas')
                                                    + '"><embed src="'+ $(id).getAttribute('de:url')
                                                    +'/'+$(id).getAttribute('de:mas')+ '" width="68" height="50" ></embed></object>';

            div.appendChild(span);
            a.appendChild(div);
        }else{
            min.setAttribute('width', $(id).getAttribute('de:width'));
            a.appendChild(min);
        }
        li.appendChild(a);

        ul.appendChild(li);
        //Definimos la papelera
        funcion='del_img("'+del+'")';
         $('remove_img').setAttribute('onclick', funcion);
        //Lo hacemos movible.
        Sortable.create('album_list',{ constraint: 'false', scroll:'scroll-album' });
       // new Draggable('img'+pkfoto, { revert:true, scroll: window, ghosting:true }  );
	return(del);
       
} 
  
</script>


