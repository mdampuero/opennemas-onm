<div class="modal hide fade" id="modal-edit-user-group" style="width:70%; margin-left:-450px">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal-edit-user-group" aria-hidden="true">×</button>
      <h3>{t}Editing user group{/t}</h3>
    </div>
    <div class="modal-body"></div>
</div>

<script>
jQuery(document).ready(function($) {
    jQuery("#modal-edit-user-group").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });
    jQuery("#modal-edit-user-group").bind('show', function (){
        selectedGroup = $('#id_user_group').val();
        jQuery("#modal-edit-user-group div.modal-body").html(
            '<iframe width="100%" height="450" src="user_groups.php?action=read&id='
            + selectedGroup
            +'"  frameborder="0" marginheight="0" marginwidth="0"></iframe>'
        );
    });
    jQuery("#show-user-group-modal").on('click', function (e) {
        selectedGroup = $('#id_user_group').val();
        if (selectedGroup != "") {
            jQuery("#modal-edit-user-group").modal('show');
        };
        e.preventDefault();
    });
});
</script>