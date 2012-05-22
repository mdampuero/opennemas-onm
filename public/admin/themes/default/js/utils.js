function checkAll(field,img)
{
	if(field){
		if( $( img ).getAttribute('status')==0){
			var status=true;
			$( img ).src='/admin/themes/default/images/deselect_button.png';
			$( img ).setAttribute('status','1');
		}else{
			var status=false;
			$( img ).src='/admin/themes/default/images/select_button.png';
			$( img ).setAttribute('status','0');
		}
		if(field.length){
			for (i = 0; i < field.length; i++) {
				$( field[i].id ).checked = status;
			}
		}else{ //Solo hay un elemento a de/seleccionar
			 	$( field ).checked = status;
		}
	}
}


function objetoAjax(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

//Para articles
function get_tags(title)
{
    //instanciamos el objetoAjax
    ajax=objetoAjax();
    var category="";
    if($('category')){
       category=$('category').options[$('category').selectedIndex].getAttribute('data-name');
       if(category==null) {category='';}
    }
    var tags= document.getElementById('metadata').value;

    //uso del medotod GET
    ajax.open("GET", "/admin/controllers/utils_content.php?action=get_tags&title="+title+"&categ="+category+"&tags="+tags);
    ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
         //mostrar resultados en esta capa
         document.getElementById('metadata').value = ajax.responseText

       }
    }
   //como hacemos uso del metodo GET
   //colocamos null
   ajax.send(null)


}
//Para publicidad, opinion y album
function get_metadata(title)
{
	 //instanciamos el objetoAjax
	   ajax=objetoAjax();

	   var tags= document.getElementById('metadata').value;

	   //uso del medotod GET
	   ajax.open("GET", "/admin/controllers/utils_content.php?action=get_tags&title="+title+"&tags="+tags);
	   ajax.onreadystatechange=function() {
	    if (ajax.readyState==4) {
		     //mostrar resultados en esta capa
		     document.getElementById('metadata').value = ajax.responseText

		   }
       }
   //como hacemos uso del metodo GET
   //colocamos null
   ajax.send(null)
}

//Para imagen
function get_metadata_imagen(description,id)
{
	 //instanciamos el objetoAjax
	ajax=objetoAjax();

	var tags= document.getElementById('metadata['+id+']').value;

    //uso del medotod GET
    ajax.open("GET", "/admin/controllers/utils_content.php?action=get_tags&title="+description+"&tags="+tags);
    ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
            //mostrar resultados en esta capa
            document.getElementById('metadata['+id+']').value = ajax.responseText
        }
    }
   //como hacemos uso del metodo GET
   //colocamos null
   ajax.send(null)
}

function countWords(text,counter){

	var y=text.value;
	var r = 0;
	a=y.replace(/\s/g,' ');
	a=a.split(' ');
	for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
	counter.value=r;
}

function counttiny(counter, editor){

	//var codigo = document.getElementById('body_ifr').contentWindow.document.getElementById('tinymce').innerHTML;
	var codigo = editor.getContent();

	resul=codigo.replace(/<[^>]+>/g,''); //Quitamos html;
	var y=resul;
	var r = 0;
	a=y.replace(/\s/g,' ');
	a=a.split(' ');
	for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
	counter.value=r;

}

function onChangeGroup(evaluateControl, ids)
{
    if (document.getElementById)
    {
        var combo = document.getElementById('ids_category');
        //se define la variable "el" igual a nuestro div
        if (evaluateControl.options[evaluateControl.selectedIndex].text.toLowerCase() == "administrador")
        {
            for (iIndex=0; iIndex<ids.length; iIndex++)
            {
                var hideDiv = document.getElementById(ids[iIndex]);
                hideDiv.style.display = 'none'; //damos un atributo display:none que oculta el div
            }
            combo.options[0].selected = false;
            for(iIndex=1; iIndex<combo.options.length;  iIndex++)
                combo.options[iIndex].selected = true;
        }
        else
        {
            for (iIndex=0; iIndex<ids.length; iIndex++)
            {
                var showDiv = document.getElementById(ids[iIndex]);
                if (showDiv) {
                    showDiv.style.display = 'block'; //damos un atributo display:block que muestra el div
                }
            }
            if (combo) {
                for(iIndex=0; iIndex<combo.options.length;  iIndex++)
                    combo.options[iIndex].selected = false;
            }
        }

    }
}


function paginate_search(action,page,stringSearch,categories)
{
    new Ajax.Updater(
        'search-results',
        "search_advanced.php?action="+action+"&page="+page+"&stringSearch="+stringSearch+categories,
        {
            evalScripts: true
        }
    );
}


function del_photo(id)
{
    new Ajax.Request(
        "author.php?action=check_img_author&id_img="+id+"",
        {
            method: 'get',

            onSuccess: function(transport) {
                if( transport.responseText =='no' ){
                    if(confirm('¿Seguro que desea eliminar la foto?')){
                        $('del_img').value =$('del_img').value + ","+ id;
                        var li = $(id);

                        li.parentNode.removeChild(li);

                    }
                }else{
                    alert('No se puede eliminar, está asociado a alguna opinion. ');
                }
            }
        }
    );
}


function delete_fichero(id,page){
    new Ajax.Request(
        'files.php?action=delete&id='+id+'&page='+page,
        {
            onSuccess: function(transport) {
                 var msg = transport.responseText;
                // showMsg({'warn':[msg ]},'growl');
                   if(confirm(msg)) {
                      var ruta='files.php?action=yesdel&id='+id+'&page='+page;
                      location.href= ruta;
                   }
                   return false;
            }
        }
    );
}


function  show_subcat(category,home){
    new Ajax.Updater('menu_subcats', "/admin/controllers/utils_content.php?action=get_subcategories&category="+category+"&home="+home,
    {
        evalScripts: true
    });//
}

function salir(msg,url) {
	if(confirm(msg)) {
		location.href = url;
	}
}
