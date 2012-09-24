<div class="modal hide fade" id="modal-video-batchDelete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete videos{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> videos?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-video-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

jQuery('.delChecked').click(function(e) {
    var number = jQuery(".minput:checked").length;
    if (number >= 1 ) {
        jQuery('#modal-video-batchDelete .modal-body span').html(number);
        jQuery("#modal-video-batchDelete").modal('show');
    }else{
        jQuery("#modal-video-batchDelete").modal('hide');
        jQuery("#modal-video-accept").modal('show');
        jQuery('#modal-video-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }

    e.preventDefault();
});

jQuery('#modal-video-batchDelete .btn.yes').on('click', function(){
    jQuery('#formulario').attr('action', video_manager_urls.batchDelete);
    jQuery('#formulario').submit();
});

jQuery('#modal-video-batchDelete a.btn.no').on('click', function(e){
    jQuery("#modal-video-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
