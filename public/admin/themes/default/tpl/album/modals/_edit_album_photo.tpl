<div class="modal hide fade" id="modal-edit-album-photo">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Complete all the required album information before save it{/t}</h3>
    </div>
    <div class="modal-body">
        <h2>En construcción...</h2>
        <div class="thumbnail article-resource-image">
            <img src="#"/>
        </div>
        <div class="article-resource-image-info">
            <div><label>{t}File name{/t}</label>     <span class="filename"></span></div>
            <div><label>{t}Creation date{/t}</label> <span class="created_time"></span></div>
            <div><label>{t}Description{/t}</label>   <span class="description"></span></div>
            <div><label>{t}Tags{/t}</label>          <span class="tags"></span></div>
        </div>
        <textarea name="footer_image" class="footer_image"></textarea>
    </div>
    <div class="modal-footer">
        <a class="btn primary accept" href="#">{t}Close{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function() {
    jQuery("#modal-edit-album-photo").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });
    jQuery('#modal-edit-album-photo a.btn.accept').on('click', function(e){
        jQuery("#modal-menu-accept").modal('hide');
        e.preventDefault();
    });
});
</script>