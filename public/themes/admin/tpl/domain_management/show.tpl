{extends file="domain_management/list.tpl"}

{block name="content"}
<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <i class="fa fa-indent fa-server fa-lg"></i>
            {t}Domain Mapping{/t}
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" href="{url name=admin_domain_management}">
              <span class="fa fa-reply"></span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <div class="domain-management-container">
    <div class="grid simple">
      <div class="grid-title clearfix">
        <h4>frandieguez.opennemas.com</h4>
        <div class="pull-right">
          <label class="label label-success">
            <span class="fa fa-check"></span>
            <span>{t}Primary Domain{/t}</span>
          </label>
          <noscript>
          </noscript>
        </div>
      </div>
      <div class="grid-body">
        <div class="domain-details-card">
          <div class="is-compact card">
            <div class="domain-details-card__property">
              <strong>
                <span>Type</span>
                <span>:</span>
              </strong>
              <span>Included with Site</span>
            </div>
            <div class="domain-details-card__property">
              <strong>
                <span>Renews on</span>
                <span>:</span>
              </strong>
              <span>
                <em>Never Expires</em>
              </span>
            </div>
            <div class="domain-details-card__property">
              <strong>
                <span>Points to</span>
                <span>:</span>
              </strong>
              <span class="text-small">
                <em>frandieguez.com.opennemas.net</em> <a href="#" data-toggle="modal" data-target="#myModal"><span class="fa fa-info-circle"></span> {t}More info{/t}</a>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div  class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{t}Technical information{/t}</h4>
      </div>
      <div class="modal-body">
        <p>Show here a detailed information about how this domain is mapped to our opennemas servers</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{/block}
