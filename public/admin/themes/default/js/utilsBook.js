//Orden Listado de libros en el backend y frontend
function savePriorityLibros() {
    var orden="";
    if(document.getElementById('cates')){
        var items = $('cates').select("table");
        for (i = 0; i < items.length; i++) {
            orden =orden + "," +items[i].id;
        }
        //	alert('destaca:'+orden);

        //instanciamos el objetoAjax
        ajax=objetoAjax();
        //uso del medotod GET
        var url = "libros.php?action=save_orden_list&orden="+orden+" ";

        new Ajax.Request(url, {
            method: 'get',
            onSuccess: function() {
              alert("Orden guardado correctamente.");
              return false;
            }
        });
    }
}


function deleteBook(id,page){

      new Ajax.Request( 'book.php?action=delete&id='+id+'&page='+page,
        {
            onSuccess: function(transport) {
                 var msg = transport.responseText;

                 if(confirm(msg)) {
                      var ruta='book.php?action=yesdel&id='+id+'&page='+page;
                      location.href= ruta;
                   }
            }
        });


 }
