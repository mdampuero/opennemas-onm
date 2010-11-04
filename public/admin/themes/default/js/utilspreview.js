//Funciones js para front_preview

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

function saveDinamyc() {
//Para que valga la llamada al iframe en cualquier navegador
  var oIframe = document.getElementById("portada");
    var inframe = oIframe.contentWindow || oIframe.contentDocument;
    if (inframe.document) {
        inframe = inframe.document;
    }
  
	  var orden="";
		var items = inframe.getElementById('noticia-principal').getElementsByClassName("noticia");
		for (i = 0; i < items.length; i++) {	   				 
		  
		    orden =orden + "," +items[i].id;
		} 
			//alert('destaca:'+orden);
 	
 
  var pares ="";
  var impares ="";

  var items=inframe.getElementById('columna-central').getElementsByClassName("noticia");
	for (i = 0; i < items.length; i++) {	   			 
	    pares =pares + "," +items[i].id;
	  }
	
	
	 
	 var items=inframe.getElementById('columnas').getElementsByClassName("noticia");
	for (i = 0; i < items.length; i++) {	   			 
	    impares =impares + "," +items[i].id;
	  }
	
	
	//alert ('Son: p'+ pares +" i " + impares);
	
	
	
	  
	
	 //instanciamos el objetoAjax
	   ajax=objetoAjax();
	   //uso del medotod GET
	   ajax.open("GET", "changedinamyc.php?orden="+orden+"&pares="+pares+"&impares="+impares+" ");
	   ajax.onreadystatechange=function() {
	    if (ajax.readyState==4) {
		   //mostrar resultados en esta capa
	//	   divResultado.innerHTML = ajax.responseText
		//     alert("gardado ok" + orden);
		   }
       }
   //como hacemos uso del metodo GET
   //colocamos null
   ajax.send(null)
	
}

//Agranda el iframe tamaño pantalla

function agranda(my) {

my.height=(window.innerHeight-110);
}



