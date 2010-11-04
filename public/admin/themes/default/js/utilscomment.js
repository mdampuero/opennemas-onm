// JavaScript Document

//Para recargar nuevos comments

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

function reload_comments(go,cat)
{
   if(go==1){
	 //instanciamos el objetoAjax
	   ajax=objetoAjax();

	   //uso del medotod GET
	   ajax.open("GET", "reloadcomment.php?segundos=60&category="+cat);
	   ajax.onreadystatechange=function() {
	    if (ajax.readyState==4) {
		     //mostrar resultados en esta capa
		     document.getElementById('fisgona').innerHTML = document.getElementById('fisgona').innerHTML +   ajax.responseText
		  
		  
		  
		   setTimeout(reload_comments(1,cat),60000); //Para que recargue cada 60 segundos
		   }
       }
   //como hacemos uso del metodo GET
   //colocamos null
   ajax.send(null)
   }
	
}

