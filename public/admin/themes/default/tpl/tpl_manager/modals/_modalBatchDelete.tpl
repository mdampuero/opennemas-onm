<div class="modal hide fade" id="modal-cache-batchDelete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal-cache-batchDelete" aria-hidden="true">×</button>
      <h3>{t}Delete advertisements{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> advertisements?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function ($){
    $("#modal-cache-batchDelete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    $('.delChecked').click(function(e, ui) {
        var number = $(".minput:checked").length;
        if(number >= 1 ) {
            $('#modal-cache-batchDelete .modal-body span').html(number);
            $("#modal-cache-batchDelete").modal('show');
        }else{
            $("#modal-cache-batchDelete").modal('hide');
            $("#modal-cache-accept").modal('show');
            $('#modal-cache-accept .modal-body')
                .html("{t}You must select some elements.{/t}");
        }

        e.preventDefault();
    });

    $('#modal-cache-batchDelete a.btn.yes').on('click', function(e, ui){
        $('#tplform').attr('action', '{url name="admin_tpl_manager_delete"}');
        $('#tplform').submit();
    });

    $('#modal-cache-batchDelete a.btn.no').on('click', function(e, ui){
        $("#modal-cache-batchDelete").modal('hide');
        e.preventDefault();
    });
});
</script>
