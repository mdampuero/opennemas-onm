//Funciones js para categorys 


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

function savePriority() {
	  var orden="";
	  if(document.getElementById('cates')){
		var items = document.getElementById('cates').getElementsByClassName("tabla");
		for (i = 0; i < items.length; i++) {	  
	  
		    orden =orden + "," +items[i].id;
		} 
		//	alert('destaca:'+orden);
	  }else{
		  if(document.getElementById('subcates')){
			  var items = document.getElementById('subcates').getElementsByClassName("tabla");
				for (i = 0; i < items.length; i++) {	  
			  
				    orden =orden + "," +items[i].id;
				} 
		  }
	  }
	  
	  if(orden){
	 //instanciamos el objetoAjax
	   ajax=objetoAjax();
	   //uso del medotod GET
	   ajax.open("GET", "cambiapriority.php?orden="+orden+" ");
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
}