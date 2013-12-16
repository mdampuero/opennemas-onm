<div class="modal hide fade" id="modal-instance-batchDelete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete instances{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> instances?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function ($){
    $("#modal-instance-batchDelete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    $('.delChecked').on('click', function(e, ui) {
        var number = $(".minput:checked").length;
        if(number >= 1 ) {
            $('#modal-instance-batchDelete .modal-body span').html(number);
            $("#modal-instance-batchDelete").modal('show');
        } else {
            $("#modal-instance-batchDelete").modal('hide');
            $("#modal-instance-accept").modal('show');
            $('#modal-instance-accept .modal-body')
                .html("{t}You must select some instances{/t}");
        }

        e.preventDefault();
    });

    $('#modal-instance-batchDelete a.btn.yes').on('click', function(e, ui){
        $('#formulario').attr('action', '{url name="manager_instance_batch_delete" filter_name=$filter_name filter_email=$filter_email}');
        $('#formulario').submit();
        e.preventDefault();
    });

    $('#modal-instance-batchDelete a.btn.no').on('click', function(e, ui){
        $("#modal-instance-batchDelete").modal('hide');
        e.preventDefault();
    });
});
</script>
