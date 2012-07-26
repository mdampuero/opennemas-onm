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
            document.getElementById('metadata').value = ajax.responseText;
        }
    };
    //como hacemos uso del metodo GET
    //colocamos null
    ajax.send(null);
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
            document.getElementById('metadata['+id+']').value = ajax.responseText;
        }
    };
   //como hacemos uso del metodo GET
   //colocamos null
   ajax.send(null);
}

