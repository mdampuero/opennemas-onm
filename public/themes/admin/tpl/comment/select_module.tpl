{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
    $('[rel=tooltip]').tooltip({ placement : 'bottom' });
</script>
{/block}

{block name="content"}
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-comment"></i>
                            {t}Comments{/t}
                        </h4>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content">
        {render_messages}
        <script>
        $(function() {
            jQuery("#modal-comment-change").modal('show');

        });
        </script>
    </div>
{/block}

{block name="modals"}
  {include file="comment/modals/_modalChange.tpl"}
{/block}
