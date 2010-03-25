

var min=8;
var max=18;
function increaseFontSize() {
   var p = document.getElementsByClassName('content-article');
   for(i=0;i<p.length;i++) {
      if(p[i].style.fontSize) {
         var s = parseInt(p[i].style.fontSize.replace("px",""));
      } else {
         var s = 12;
      }
      if(s!=max) {
         s += 1;
      }
      p[i].style.fontSize = s+"px"
   }
}
function decreaseFontSize() {
   var p = document.getElementsByClassName('content-article');
   for(i=0;i<p.length;i++) {
      if(p[i].style.fontSize) {
         var s = parseInt(p[i].style.fontSize.replace("px",""));
      } else {
         var s = 12;
      }
      if(s!=min) {
         s -= 1;
      }
      p[i].style.fontSize = s+"px"
   }   
}

function sendbyemail(title,url) {
alert("holas");
  window.location="mailto:insert_here@your_friend.mail?subject=See this article '"+title+"' from Mabishu Studio&body=Please take a look at: "+url+" from Mabishu Blog.";
}

function showCommentForm(){
    $('.auth-selector').slideToggle();
    $('.form-comments .form').slideToggle();
}


 

function rating(ip,value,page,id) {

	$('.vota'+id).html( '<img src="/themes/lucidity/images/loading.gif" height="9" border="0"/> Actualizando...');

	var url='/article.php?action=rating&i='+ip+'&v='+value+'&p='+page+'&a='+id;
	  
        $.ajax({ url: url, context: document.body, success: function(result){
            $('.vota'+id).html( result);
            }
        });
}


function change_rating(num,pk_rating,sufijo) {
  
	for(i=1; i<=5; i++) {
		if (i<=num) {                       
			$('.'+pk_rating+'_'+i).attr('src', '/themes/lucidity/images/utilities/f-star'+sufijo+'.png');

		} else {
			$('.'+pk_rating+'_'+i).attr('src', '/themes/lucidity/images/utilities/e-star'+sufijo+'.png');
		}
	}
}

function vote_comment(ip,value,id) {

        $('.vota'+id).html( '<img src="/themes/lucidity/images/loading.gif" height="9" border="0"/> Actualizando...');

	var url='/article.php?action=vote&i='+ip+'&v='+value+'&p='+page+'&a='+id;

        $.ajax({ url: url, context: document.body, success: function(result){
            $('.vota'+id).html( result);
            }
        });
}
/*
//prototype Ajax vote comments

function vote_comment(ip,value,id) {
var page='Article';
 
 new Ajax.Updater('vota'+id, '/article.php?action=vote&i='+ip+'&v='+value+'&p='+page+'&a='+id,
        {

             onLoading: function() {
                $('vota'+id).update('<img src="/themes/lucidity/images/loading.gif" height="9" border="0"/> Actualizando...');
                console.log('/article.php?action=vote&i='+ip+'&v='+value+'&p='+page+'&a='+id);
             },
             onComplete: function(transport) {

               $('vota'+id).update(transport.responseText);

            }
        } );
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
*/