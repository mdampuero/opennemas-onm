<div class="modal hide fade" id="modal-logged-users">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Logged in users{/t}</h3>
    </div>
    <div class="modal-body"></div>
</div>

<script>
jQuery(document).ready(function($) {
    jQuery("#modal-logged-users").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });
    jQuery("#modal-logged-users").bind('show', function (){
        jQuery.get(
            '{url name=backend_user_connected_users}',
            function (data) {
                jQuery("#modal-logged-users div.modal-body").html(
                    data
                );
            }
        );
    });
    jQuery("#user_activity").on('click', function () {
        jQuery('#modal-logged-users').modal('show');
    });
});
</script>