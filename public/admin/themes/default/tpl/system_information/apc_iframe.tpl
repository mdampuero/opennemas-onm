{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">

html,body {

margin:0px;

height:100%;

}
#content {
    height:100%;
    margin:0;
}

iframe {
    margin:0;
    margin-top:35px;
    height:95%;
    border:0 none;
}

</style>
{/block}

{block name="copyright"}{/block}

{block name="content"}
<div style="height:100%; text-align:right;">
    <iframe src="/admin/controllers/system_information/apc.php" width="99%"></iframe>
</div>
{/block}
