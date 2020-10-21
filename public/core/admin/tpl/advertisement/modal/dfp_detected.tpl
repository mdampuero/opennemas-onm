<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
    <h4 class="modal-title">Google DFP detected</h4>
  </div>
  <div class="modal-body">
    <p>{t}A Google DFP advertisement has been detected in your HTML/javascript advertisement but Opennemas already supports this type of advertisements.{/t}</p>
    <p>{t}Would you like to auto-correct this advertisement to use our Google DFP integration?{/t}</p>
    <ul>
      <li>{t}Google DFP unit{/t}: [% template.googledfp_unit_id %]</li>
      <li>{t}Width{/t}: [% template.params_width %]</li>
      <li>{t}Height{/t}: [% template.params_height %]</li>
    </ul>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="close()">{t}No{/t}</button>
    <button type="button" class="btn btn-primary" ng-click="confirm()">{t}Yes{/t}</button>
  </div>
</div>
