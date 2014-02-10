{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
    $('[rel=tooltip]').tooltip({ placement : 'bottom' });
</script>
{/block}

{block name="header-css" append}
<style type="text/css">
    .submitted-on {
        color: #777;
    }
</style>
{/block}

{block name="content"}
    <div class="top-action-bar clearfix" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>
                    {t}Comments{/t}
                </h2>
            </div>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}


        {include file="comment/modals/_modalChange.tpl"}
        <script>
        $(function() {
            jQuery("#modal-comment-change").modal('show');

        });
        </script>
    </div>
{/block}
