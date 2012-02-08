//Funciones js para categorys

function savePriority() {

	var orden="";

	if(document.getElementById('cates')){

	  var items = document.getElementById('cates').getElementsByClassName("tabla");
	  for (i = 0; i < items.length; i++) {
		  orden =orden + "," +items[i].id;
	  }

	}else{

		if(document.getElementById('subcates')){
			var items = document.getElementById('subcates').getElementsByClassName("tabla");
			for (i = 0; i < items.length; i++) {
				orden =orden + "," +items[i].id;
			}
		}

	}

	if(orden){

		var url = "cambiapriority.php?orden="+orden+" ";

		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				$('warnings-validation').update('<div class="success">Posiciones guardadas con éxito</div>');
			},
			onFailure: function(req,exception) {
				$('warnings-validation').update('<div class="error">Se ha producido un error al guardar las posiciones.</div>');
			}
		});
	}
}