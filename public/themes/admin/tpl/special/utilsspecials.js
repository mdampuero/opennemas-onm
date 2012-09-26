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

function set_only_pdf(my){
   if(my.checked==false){
                Effect.BlindDown($('cates'));

		Effect.BlindUp($('div_pdf'));
                my.value=0;
	}else{
		Effect.BlindUp($('cates'));

		Effect.BlindDown($('div_pdf'));
                my.value=1;
	}
}

function get_noticias() {

   var orden="";
   if($('cates_left')){
		var items = $('cates_left').getElementsByClassName("tabla");
		for (i = 0; i < items.length; i++) {
		    orden = orden + "," +items[i].getAttribute('value');
		}
		$('noticias_left').value=orden;


      }
      orden="";
    if($('cates_right')){
                items = $('cates_right').getElementsByClassName("tabla");
		for (i = 0; i < items.length; i++) {
		    orden = orden + "," +items[i].getAttribute('value');
		}
		$('noticias_right').value=orden;


      }

}


//Orden Listado de specials en portada
function savePrioritySpecial(category) {
	  var orden="";
	  if($('cates')){
		var items = $('cates').select("table");
		for (i = 0; i < items.length; i++) {

		    orden =orden + "," +items[i].id;
		}
		//	alert('destaca:'+orden);



	 //instanciamos el objetoAjax
	   ajax=objetoAjax();
	   //uso del medotod GET
	   ajax.open("GET", "special.php?action=save_orden_list&category="+category+"&orden="+orden+" ");
	   ajax.onreadystatechange=function() {
	    if (ajax.readyState==4) {
		   //mostrar resultados en esta capa
		    alert("Orden guardado correctamente");
		   }
       }
   //como hacemos uso del metodo GET
   //colocamos null
   ajax.send(null)
	}
}