<div class="modal hide fade" id="modal-add-item">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Add an external link item.{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}Fill the below form with the title and the external URL you want to add to the menu.{/t}</p>
        <p>
            <label>{t}Title:{/t}</label>
            <input type="text" name="itemTitle" value="" id="itemTitle" size="60">
        </p>
        <p>
            <label>{t}URL:{/t}</label>
            <input type="text" name="link" value="" id="link" size="60"> <br>
        </p>
        <input type="hidden" name="itemID" id="itemID" value=""/>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Add element{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-add-item").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

jQuery('#modal-add-item a.btn.yes').on('click', function(e, ui){
    e.preventDefault();
    var name = jQuery('#itemTitle').val();
    var link = jQuery('#link').val();

    if (name && link) {
        ul = jQuery('#menuelements');

        var li = document.createElement('li');

        ul.append( '<li data-title="'+ name +'" data-link="'+ link +
                    '" class="menuItem" data-name="'+ name +'" data-id ="'+ name +
                    '" data-item-id="" data-type="external"><div>'+name+
                    '<div class="btn-group actions" style="float:right;">'+
                        '<a href="#" class="edit-menu-item"><i class="fa fa-pencil"></i></a> '+
                        '<a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>'+
                    '</div></div></li>' );

        jQuery('#itemTitle').attr('value','');
        jQuery('#link').attr('value','');
        jQuery('#linkInsertions').hide();
    }
    jQuery('#modal-add-item').modal('hide');
});
</script>