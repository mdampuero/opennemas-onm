<div class="modal fade" id="modal-category-empty">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>{t}Delete all the contents in category{/t}</h4>
      </div>
      <div class="modal-body">
        <p>{t escape=off}Are you sure that do you want delete all the contents in the category "<span>%title%</span>"?{/t}</p>
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
    jQuery("#modal-category-empty").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    jQuery('.empty-category').click(function(e, ui) {
        jQuery('#modal-category-empty .modal-body span').html( jQuery(this).data('title') );
        //Sets up the modal
        jQuery("#modal-category-empty").modal('show');
        jQuery("body").data("selected-for-del", jQuery(this).data("url"));
        e.preventDefault();
    });

    jQuery('#modal-category-empty a.btn.yes').on('click', function(e, ui){
        var url = jQuery("body").data("selected-for-del");
        if (url) {
            document.location = url;
        }
        e.preventDefault();
    });

    jQuery('#modal-category-empty a.btn.no').on('click', function(e){
        jQuery("#modal-category-empty ").modal('hide');
        e.preventDefault();
    });
  </script>
{/javascripts}
