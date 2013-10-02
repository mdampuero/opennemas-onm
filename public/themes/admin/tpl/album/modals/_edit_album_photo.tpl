<div class="modal hide fade" id="modal-edit-album-photo">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>{t}Edit image properties{/t}</h3>
    </div>
    <div class="modal-body clearfix">
        <div class="thumbnail article-resource-image">
            <img src="#"/>
        </div>
        <div class="article-resource-image-info">
            <div><label>{t}Description{/t}</label>
                <textarea name="footer_image" id="footer_image" class="footer_image" autofocus></textarea>
                <input type="hidden" name="id_image" id="id_image" value="">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary save" href="#">{t}Save{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function() {
    jQuery("#modal-edit-album-photo").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    jQuery('#modal-edit-album-photo a.btn.save').on('click', function(e) {

        var imageID = jQuery("#modal-edit-album-photo input#id_image").val();
        var imageFooter = jQuery("#modal-edit-album-photo textarea#footer_image").val();
        var parent = jQuery('#'+imageID).closest('.image.thumbnail');

        //updated hidden textarea
        parent.find('textarea').html(imageFooter);
        console.log(parent.find('textarea'));

        jQuery("#modal-edit-album-photo").modal('hide');

        e.preventDefault();
    });
});
</script>
