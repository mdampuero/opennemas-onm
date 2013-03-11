/**
 * Onm voting auxiliar jQuery functions
 */

function rating(ip,value,page,id) {
	$('.vota'+id).html( '<div class="ajax-loading">Actualizando...</div>');
	var url='/controllers/rating.php?action=rating&i='+ip+'&v='+value+'&p='+page+'&a='+id;

    $.ajax({
        url: url,
        success: function(result){
            $('.vota'+id).html( result);
        }
    });
}


function change_rating(num,pk_rating,sufijo) {
	for(i=1; i<=5; i++) {
		if (i<=num) {
			$('.'+pk_rating+'_'+i).addClass('active');

		} else {
			$('.'+pk_rating+'_'+i).removeClass('active');
		}
	}
}
