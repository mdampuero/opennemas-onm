<div class="modal fade" id="modal-edit-album-errors">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">{t}Complete all the required album information before save it{/t}</h4>
      </div>
      <div class="modal-body">
        <ul>
          <li>{t}Check that you have included more than one image in this album{/t}</li>
          <li>{t}Assign an image as a cover image{/t}</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="button">{t}Close{/t}</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
