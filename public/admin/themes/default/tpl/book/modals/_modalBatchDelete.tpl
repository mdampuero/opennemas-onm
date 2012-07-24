<div class="modal hide fade" id="modal-book-batchDelete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete books{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> books?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-book-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true //Can close on escape

});

jQuery('.delChecked').click(function(e) {
    var number = jQuery(".minput:checked").length;
    if(number >= 1 ) {
        jQuery('#modal-book-batchDelete .modal-body span').html(number);
        jQuery("#modal-book-batchDelete").modal(true);
    }else{
        jQuery("#modal-book-batchDelete").modal(false);
        jQuery("#modal-book-accept").modal('show');
        jQuery('#modal-book-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }

    e.preventDefault();
});

jQuery('#modal-book-batchDelete a.btn.yes').on('click', function(){
    jQuery('#formulario').attr('action', book_manager_urls.batchDelete);
    jQuery('#formulario').submit();
});

jQuery('#modal-book-batchDelete a.btn.no').on('click', function(e){
    jQuery("#modal-book-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
