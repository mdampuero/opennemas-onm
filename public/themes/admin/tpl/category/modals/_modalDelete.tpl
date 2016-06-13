<div class="modal fade" id="modal-category-delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
          {t}Delete category{/t}
        </h4>
      </div>
      <div class="modal-body">
          <p>{t escape=off}Are you sure that do you want delete "<span>%title%</span>"?{/t}</p>
      </div>
      <div class="modal-footer">
          <a class="btn btn-primary yes" href="#">{t}Yes, delete{/t}</a>
          <a class="btn secondary no" href="#">{t}No{/t}</a>
      </div>
    </div>
  </div>
</div>

{javascripts}
  <script>
    jQuery("#modal-category-delete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    jQuery('.del-category').click(function(e, ui) {
        jQuery('#modal-category-delete .modal-body span').html( jQuery(this).data('title') );
        //Sets up the modal
        jQuery("#modal-category-delete").modal('show');
        jQuery("body").data("selected-for-del", jQuery(this).data("url"));
        e.preventDefault();
    });

    jQuery('#modal-category-delete a.btn.yes').on('click', function(e, ui){
        var url = jQuery("body").data("selected-for-del");
        if (url) {
            document.location = url;
        }
        e.preventDefault();
    });

    jQuery('#modal-category-delete a.btn.no').on('click', function(e){
        jQuery("#modal-category-delete ").modal('hide');
        e.preventDefault();
    });
  </script>
{/javascripts}
