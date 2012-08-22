<div class="modal hide fade" id="modal-opinion-batchDelete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete opinions{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> opinions?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-opinion-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

jQuery('.delChecked').click(function(e) {
    var number = jQuery(".minput:checked").length;
    if(number >= 1 ) {
        jQuery('#modal-opinion-batchDelete .modal-body span').html(number);
        jQuery("#modal-opinion-batchDelete").modal('show');
    }else{
        jQuery("#modal-opinion-batchDelete").modal('hide');
        jQuery("#modal-opinion-accept").modal('show');
        jQuery('#modal-opinion-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }

    e.preventDefault();
});

jQuery('#modal-opinion-batchDelete a.btn.yes').on('click', function(){
    jQuery('#action').attr('value', "batchDelete");
    jQuery('#formulario').attr('method', "POST");
    jQuery('#formulario').submit();
    e.preventDefault();
});

jQuery('#modal-opinion-batchDelete a.btn.no').on('click', function(e){
    jQuery("#modal-opinion-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
