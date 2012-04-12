<div class="modal hide fade" id="modal-edit-album-errors">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Complete all the required album information before save it{/t}</h3>
    </div>
    <div class="modal-body">
        <ul>
            <li>{t}Check that you have included more than one image in this album{/t}</li>
            <li>{t}Assign an image as a cover image{/t}</li>
        </ul><!-- / -->
    </div>
    <div class="modal-footer">
        <a class="btn primary accept" href="#">{t}Close{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-edit-album-errors").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});
jQuery('#modal-edit-album-errors a.btn.accept').on('click', function(e){
    jQuery("#modal-edit-album-errors").modal('hide');
    e.preventDefault();
});
</script>