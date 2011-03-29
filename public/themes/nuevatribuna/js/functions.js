
var Onm = {
   
   minFontSize: 8,
   maxFontSize: 18,
   
   increaseFontSize: function(classEl) {
      var currentFontSize = $(classEl).css('font-size');
      var currentFontSizeNum = parseFloat(currentFontSize, 10);
      if(currentFontSizeNum <= this.minFontSize) { return false; }
      var newFontSize = currentFontSizeNum+1;
      $(classEl).css('font-size', newFontSize);
      return false;
   },

   decreaseFontSize: function(classEl) {
      
      var currentFontSize = $(classEl).css('font-size');
      var currentFontSizeNum = parseFloat(currentFontSize, 10);
      if(currentFontSizeNum >= this.maxFontSize) { return false; }
      var newFontSize = currentFontSizeNum-1;
      $(classEl).css('font-size', newFontSize);
      return false;
      
   }
   
}

function showCommentForm(){
    $('.auth-selector').slideToggle();
    $('.form-comments .form').slideToggle();
}


 

function rating(ip,value,page,id) {

	$('.vota'+id).html( '<img src="/themes/nuevatribuna/images/loading.gif" height="9" border="0"/> Actualizando...');

	var url='/controllers/article.php?action=rating&i='+ip+'&v='+value+'&p='+page+'&a='+id;
	  
        $.ajax({ url: url, context: document.body, success: function(result){
            $('.vota'+id).html( result);
            }
        });
}


function change_rating(num,pk_rating,sufijo) {
  
	for(i=1; i<=5; i++) {
		if (i<=num) {                       
			$('.'+pk_rating+'_'+i).attr('src', '/themes/nuevatribuna/images/utilities/f-star'+sufijo+'.png');

		} else {
			$('.'+pk_rating+'_'+i).attr('src', '/themes/nuevatribuna/images/utilities/e-star'+sufijo+'.png');
		}
	}
}


vote_comment = function (ip, value, id) {
   $('#vota'+id).html('<img src="/themes/nuevatribuna/images/loading.gif" height="9" border="0"/> Actualizando...');
   
   var url = '/controllers/article.php?action=vote&i='+ip+'&v='+value+'&a='+id;
   
   $.ajax({
      url: url,
      success: function(result) {
         $('#vota'+id).html( result );
      }
   });
}


/* http://quirksmode.org */
function getEvent (e) {
   var event = e || window.event;
      if( ! event.target ) {
      event.target = event.srcElement
   }
   return event;
}

videos_incategory = function (category, page) {
   $('#videos_incategory div.clean-paginator div.buttons').prepend('<div class="ajax-loading"><img src="/themes/nuevatribuna/images/loading.gif" height="9" border="0"/> Actualizando...</div>');

   var url = '/controllers/videos.php?action=videos_incategory&category='+category+'&page='+page;

   $.ajax({
      url: url,
      success: function(result) {
         $('#videos_incategory').html( result );
      }
   });
}


videos_more = function (category, page) {
   $('#videos_more div.clean-paginator div.buttons').prepend('<div class="ajax-loading"><img src="/themes/nuevatribuna/images/loading.gif" height="9" border="0"/> Actualizando...</div>');

   var url = '/controllers/videos.php?action=videos_more&category='+category+'&page='+page;

   $.ajax({
      url: url,
      success: function(result) {
         $('#videos_more').html( result );
      }
   });
}