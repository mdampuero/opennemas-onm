// JavaScript Document


function changePhotos(fk_author)
{
	new Ajax.Updater('photos', "opinion.php?action=change_photos&fk_author="+fk_author,
        {
            evalScripts: true,
            onComplete: function() {
                $('fk_author_img_widget').value='';
                var photos = $('photos').select('img');
                for(var i=0; i<photos.length; i++) {

                    try {
                        new Draggable(photos[i].id, { revert:true }  );
                        Imagen = new Image();
                        Imagen.setAttribute('id',photos[i].id);
      	       		    	Imagen.onload=function(){
             			    		$('widget').src=this.src;
             			    		$('fk_author_img_widget').value=this.id;
             			    		$('fk_author_img').value=this.id;
             		   			    $('seleccionada').src=this.src;
      	       		    	};
      	       		    	Imagen.src=photos[i].src+'?'+Math.random();
      	       		    //	 debugger;
                    } catch(e) {
                     //  console.debug( e );
                    }
                }
            }
        } );
}

function show_authors(type_opinion)
{
	if(type_opinion==0){
        $('div_author1').setStyle({display:'inline'});
		$('div_author2').setStyle({display:'inline'});
	}else{
		$('div_author1').setStyle({display:'none'});
		$('div_author2').setStyle({display:'none'});
	}

    if(type_opinion==1){
       $('widget').src='';
       $('fk_author_img_widget').value='';
       $('fk_author_img').value='';
       fk_author=1; //Editorial
       $('fk_author').value='1';
	   $('seleccionada').src='';
       $('thelist').childElements().each(function(item){
            item.parentNode.removeChild(item);
        });
    }else{
        if(type_opinion==2){
            //TODO: get id director not manually
            fk_author=2; //Director
            $('fk_author').value=2;
        }
        changePhotos(fk_author);
    }
}

function change_algoritm(algoritm)
{
		new Ajax.Request( "opinion.php?action=change_algoritm&algoritm="+algoritm,
                {
                    onSuccess: function() {
                                 //   alert('ok');
                            }
                });

}

function savePositionsOpinion() {
    var orden=null;

    //Editorial
    if ($('editoriales')) {
        items = $('editoriales').select(".edits_sort");
        for (i = 0; i < items.length; i++) {
            orden =orden + "," +items[i].id;
        }
    }
    //Other Opinion
    if($('cates')){
        var items = $('cates').select(".sortable");
        for (i = 0; i < items.length; i++) {
            if(orden){
                    orden =orden + "," +items[i].id;
            }else{
                    orden =items[i].id;
            }
        }
    }

    if (orden) {
        new Ajax.Request(
            "/admin/controllers/opinion/opinion.php?action=save_positions",
            {
                method: 'post',
                postBody: 'orden='+orden,
                onLoaded : $('msg').update('<div class="notice">Guardando posiciones...</div>'),
                onSuccess: function(transport) {
                           $('msg').update('<div class="success">Posiciones guardadas correctamente</div>');
                }
            }
        );
    }
}

function changeList(author, status)
{
    new Ajax.Updater('list_opinion', "opinion.php?action=change_list_byauthor&author="+author+"&opinion-status="+status );
}

function changepageList(author, page)
{
    new Ajax.Updater('list_opinion', "opinion.php?action=change_list_byauthor&author="+author+"&page="+page );
}


function delete_opinion(id,page){

  new Ajax.Request( 'opinion.php?action=delete&id='+id+'&page='+page,
    {
        onSuccess: function(transport) {
             var msg = transport.responseText;

               if(confirm(msg)) {
                  var ruta='opinion.php?action=yesdel&id='+id+'&page='+page;
                  location.href= ruta;
               }
               return false;
        }
    });
 }
