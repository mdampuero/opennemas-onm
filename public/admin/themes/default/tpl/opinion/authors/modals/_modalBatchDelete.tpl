<div class="modal hide fade" id="modal-author-batchDelete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete author{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> authors?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-author-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false

});

jQuery('.delChecked').click(function(e) {
    var number = jQuery(".minput:checked").length;
    if(number >= 1 ) {
        jQuery('#modal-author-batchDelete .modal-body span').html(number);
        jQuery("#modal-author-batchDelete").modal('show');
    }else{
        jQuery("#modal-author-batchDelete").modal('hide');
        jQuery("#modal-author-accept").modal('show');
        jQuery('#modal-author-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }

    e.preventDefault();
});

jQuery('#modal-author-batchDelete a.btn.yes').on('click', function(){
    jQuery('#formulario').attr('action', "{url name=admin_opinion_author_batchdelete}");
    jQuery('#formulario').submit();
    e.preventDefault();
});

jQuery('#modal-author-batchDelete a.btn.no').on('click', function(e){
    jQuery("#modal-author-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
