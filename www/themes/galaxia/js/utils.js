

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

/**
 *
 * @see CommentFormClass#saveComment
 * @deprecated
 */
function save_comment() {	
	var params = $('comentar').serialize();
	
	new Ajax.Request('/save_comment.php?cacheburst=' + (new Date()).getTime(), {
		'method': 'post',
		'encoding': 'UTF-8',
		'parameters': params,
		'onSuccess': function(transport) {
			try {
				// save these values before reset form
				var category = $('category').value;
				var id       = $('id').value;
				
				// clean form
				$('comentar').reset();
				
				// update values for these fields
				$('category').value = category;
				$('id').value 	    = id;
				
				// Reload img captcha
				var imgKaptcha = $('comentar').select('.CImagenKaptcha img');
				if(imgKaptcha && imgKaptcha[0]) {
					imgKaptcha = imgKaptcha[0];
					imgKaptchaSrc = imgKaptcha.src.split('?')[0] + '?' + Math.ceil(Math.random() * 100000);
					imgKaptcha.setAttribute('src', imgKaptchaSrc);
				}
			} catch(e) {
				alert(e);
			}
			
			// show message
			alert(transport.responseText);
		},
		
		'onFailure': function() {
			alert('Su comentario no ha sido guardado. Asegúrese de escribir correctamente el código de verificación.');
		}
	});

}

function rating(ip,value,page,id) {

	ajax=objetoAjax();
	
	$('vota'+id).innerHTML = '<img src="/themes/xornal/images/loading.gif" height="9" border="0"/> Actualizando...';
	
	//<div class="CVotos CComentariosNotaDestacada" id="vota'+id+'"></div> 
	ajax.open('get', '/article.php?action=rating&i='+ip+'&v='+value+'&p='+page+'&a='+id);
	ajax.onreadystatechange = function() {
		if(ajax.readyState == 4){
			if (ajax.status == 200){
				$('vota'+id).innerHTML = ajax.responseText;
			}
		}
	}
	ajax.send(null);	

}

function change_rating(num,pk_rating) {
	for(i=1; i<=5; i++) {
		if (i<=num) {
			$(pk_rating+'_'+i).src = '/themes/xornal/images/home_noticias/semaforoAzul.gif';
		} else {
			$(pk_rating+'_'+i).src = '/themes/xornal/images/home_noticias/semaforoGris.gif';
		}
	}
}

//Ajax vote comments

function vote_comment(ip,value,id) {
var page='Article';


 new Ajax.Updater('vota'+id, '/article.php?action=vote&i='+ip+'&v='+value+'&p='+page+'&a='+id,
        {

             onLoading: function() {
                $('vota'+id).update('<img src="/themes/xornal/images/loading.gif" height="9" border="0"/> Actualizando...');
                console.log('/article.php?action=vote&i='+ip+'&v='+value+'&p='+page+'&a='+id);
             },
             onComplete: function(transport) {

               $('vota'+id).update(transport.responseText);
 
            }
        } );
}

//Ajax paginate divs articles express, +vistas, +valoradas....
function get_paginate_articles(action,category,page) {
	if(action){
		ajax=objetoAjax();
	 	
	 	ajax.open('get', '/index_paginate_articles.php?action='+action+'&category_name='+category+'&page='+page);
	 	ajax.onreadystatechange = function() {
	 		if(ajax.readyState == 4){
	 			if (ajax.status == 200){
	 				$('div_'+action).innerHTML = ajax.responseText;
	 			}
	 		}
	 	}
	 	ajax.send(null);	
	}
}

//Ajax paginate divs plan conecta
function get_paginate_pc(action,name,id,page) {
	if(action){
		ajax=objetoAjax();
	 	
	 	ajax.open('get', '/planconecta.php?action='+action+'&category_name=conecta&is_ajax=ok&name='+name+'&page='+page+'&id='+id);
	 	ajax.onreadystatechange = function() {
	 		if(ajax.readyState == 4){
	 			if (ajax.status == 200){
                                      var resp='div_pc_'+name;
	 				$(resp).innerHTML = ajax.responseText;
	 			}
	 		}
	 	}
	 	ajax.send(null);	
	}
}

//Cambia Video en portada
function cambiavideo(vid,vtitle) {
	var code = '<object width="250" height="250"><param name="movie" value="http://www.youtube.com/v/'+vid+'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'+vid+'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="250" height="250"></embed></object>';
	$('videoactual').innerHTML = code;
	code = '<div class="CFlechaGrisPieGenteXornal"></div>'+vtitle;
	$('videotitle').innerHTML = code;
}

//Cambia Album portada
function cambiaalbum(atitle,link,src) {
	var code = '<a title="'+atitle+'" href="'+link+'"><img style="height: 250px;" title="'+atitle+'" alt="'+atitle+'" src="'+src+'"/></a>';
	$('albumactual').innerHTML = code;
	code = '<div class="CFlechaGrisPieGenteXornal"></div><a title="'+atitle+'" href="'+link+'">'+atitle+'</a>';
	$('albumtitle').innerHTML = code;
}

function get_plus_content(content,options) {

    container = options.container || 'pestanha1';
    category = options.category || 0;
    author = options.author || 0;
    days = options.days || 2;

    var url = '';
    if (content=='Opinion') {
        url = '/opinion.php?action=get_plus&content=Opinion&category='+category+'&author='+author+'&days='+days;
    } else {
        url = '/article.php?action=get_plus&content='+content+'&category='+category+'&author='+author+'&days='+days;
    }

    new Ajax.Request(url, {
        'method': 'get',
        onSuccess: function(transport) {
            $('div_articles_viewed').update(transport.responseText);

            no_container='pestanha0';
            if(container=='pestanha0') no_container='pestanha1';
            
            $(container).removeClassName('pestanyaOFF');
            $(container).addClassName('pestanyaON');
            $(no_container).removeClassName('pestanyaON');
            $(no_container).addClassName('pestanyaOFF');
        },
        onLoading: function() {
            $('div_articles_viewed').update('<div class="loading_container" style="height:400px;"></div>');
        }
    });
}


//Ajax paginate divs articles listado de autores....
function get_paginate_authors(page) {	
		ajax=objetoAjax();
	 	
		ajax.open('get', '/opinions/listado_xornalistas/'+page+'/46323353463.html');
	 	ajax.onreadystatechange = function() {
	 		if(ajax.readyState == 4){
	 			if (ajax.status == 200){
	 				$('list_authors').innerHTML = ajax.responseText;
	 			}
	 		}
	 	}
	 	ajax.send(null);	

}

function get_paginate_comments(id, page) {
  
    var url = '/comments.php?action=paginate_comments&id='+id+'&page='+page;
    new Ajax.Request(url, {
        'method': 'get',
        onLoading: function() {
            $('div_comments').update('<div class="loading_container" style="height:400px;"></div>');
        },
        'onSuccess': function(transport) {
            $('div_comments').update(transport.responseText);
             $('COpina').scrollTo();
            // ($'id_eleto').scrollTo() ó location.href
        }
    });

}

function get_tags(title)
{
        var tags= $('palabrasClave').value;
        new Ajax.Request( "/planconecta.php?action=get_tags&category_name=conecta&title="+ title +"&tags=" + tags,
        {
                'method': 'get',
		'encoding': 'UTF-8',
           	'onSuccess': function(transport) {
			try {
                            $('palabrasClave').value=transport.responseText;
                        } catch(e) {
				alert(e);
			}
                }
        } );


}