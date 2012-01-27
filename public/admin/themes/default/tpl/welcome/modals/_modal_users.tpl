<div class="modal hide fade" id="modal-logged-users">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Logged in users{/t}</h3>
    </div>
    <div class="modal-body"></div>
</div>

<script>
jQuery(document).ready(function($) {
    jQuery("#modal-logged-users").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
    });
    jQuery("#modal-logged-users").bind('show', function (){
        jQuery.get(
            '/admin/controllers/acl/panel.php?action=show_panel',
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