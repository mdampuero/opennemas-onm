{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsarticle.js" language="javascript"}
    {script_tag src="/editables.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
{/block}

{block name="content"}
<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
        <div id="content-wrapper">
        {if isset($smarty.request.action) && $smarty.request.action eq "list_agency"}
            {include  file="article/agencys.tpl"}
        {/if}

        {if isset($smarty.request.action) && $smarty.request.action eq "list_hemeroteca"}
            {include  file="article/library.tpl"}
        {/if}

        {if isset($smarty.request.action) && $smarty.request.action eq "only_read"}
            {include  file="article/only_read.tpl"}
        {/if}

        <td valign="top" align="right" style="padding:4px;" width="30%">

            <script type="text/javascript" language="javascript">
            document.observe('dom:loaded', function() {
                if($('title')){
                    new OpenNeMas.Maxlength($('title'), {});
                    $('title').focus(); // Set focus first element
                }
                getGalleryImages('listByCategory','{$category}','','1');
                getGalleryVideos('listByCategory','{$category}','','1');
            });

            if($('starttime')) {
                new Control.DatePicker($('starttime'), {
                    icon: './themes/default/images/template_manager/update16x16.png',
                    locale: 'es_ES',
                    timePicker: true,
                    timePickerAdjacent: true,
                    dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
                });

                new Control.DatePicker($('endtime'), {
                    icon: './themes/default/images/template_manager/update16x16.png',
                    locale: 'es_ES',
                    timePicker: true,
                    timePickerAdjacent: true,
                    dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
                });
            }
            </script>


<!--            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" name="id" id="id" value="{$id|default:""}" />-->
        </div>
    </form>
</div>
{/block}
