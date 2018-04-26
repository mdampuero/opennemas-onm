<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Delete selected items{/t}
  </h4>
</div>
<div class="modal-body">
    <p>{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to delete %1 item(s)?{/t}</p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, delete all{/t}</button>
    <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
</div>


<div class="modal hide fade" id="modal-subscriptors-batchSubscribe">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete videos{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to toggle subscription status for those <span>%num%</span> subscribers?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-subscriptors-batchSubscribe").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});

jQuery('.batchSubscribeButton').click(function(e) {
    e.preventDefault();
    jQuery('#subscribe').val(jQuery(this).data('subscribe'));
    var number = jQuery(".minput:checked").length;
    if (number >= 1 ) {
        jQuery('#modal-subscriptors-batchSubscribe .modal-body span').html(number);
        jQuery("#modal-subscriptors-batchSubscribe").modal('show');
    }else{
        jQuery("#modal-subscriptors-batchSubscribe").modal('hide');
        jQuery("#modal-subscriptions-accept").modal('show');
        jQuery('#modal-subscriptions-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }
});

jQuery('#modal-subscriptors-batchSubscribe .btn.yes').on('click', function(){
    jQuery('#formulario').attr('action', '{url name=admin_newsletter_subscriptors_batch_subscribe}');
    jQuery("#modal-subscriptors-batchSubscribe").modal('hide');
    jQuery('#formulario').submit();
});

jQuery('#modal-subscriptors-batchSubscribe a.btn.no').on('click', function(e){
    jQuery("#modal-subscriptors-batchSubscribe").modal('hide');
    e.preventDefault();
});
</script>
