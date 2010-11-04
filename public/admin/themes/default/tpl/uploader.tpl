{* CONTAINER HTML ************************************************************ *}
{if $ACTION_UPLOADER == 'upload-container'}
    

    <script language="javascript" type="text/javascript" src="{$params.JS_DIR}control.progress_bar.js"></script>
    <script language="javascript" type="text/javascript">
    var uri = '{$smarty.server.SCRIPT_NAME}';
    {literal}
    var updater = null;
    var progress_bar = null;
    function start_upload(uniqid) {        
        init();
        
        updater = new Ajax.PeriodicalUpdater('upload-status', uri, {
            method: 'get', frequency: 2, decay: 1, 'parameters': {'action': 'upload-progress', 'APC_UPLOAD_PROGRESS': uniqid},
            onSuccess: setStatusUpload, evalJSON: true
        }); 
    }
    
    function setStatusUpload(xhr, json) {
        if(json.apc) {
            if(json.done == 1) {
                finish();                
            } else {
                progress_bar.setProgress( Math.floor((json.current*100)/json.total) );
            }
        } else {
            if(json.done == 1) {
                finish();                
            } else {            
                progress_bar.setProgress(50);
            }
        }
    }
    
    function init() {
        progress_bar.setProgress(0);
        Element.setStyle('progress_bar', {'border':'1px solid #ccc'});
    }
    
    function finish() {
        updater.stop();
        progress_bar.setProgress(100);
        Element.setStyle('progress_bar', {'border':'0px solid #ccc'});
        $('upload-status').innerHTML = 'Fichero subido con \u00E9xito';
    }
    
    window.onload = function() {
		Element.setStyle('progress_bar', {'display': ''});
        progress_bar = new Control.ProgressBar('progress_bar');
		progress_bar.setProgress(0);
    }
    </script>
    {/literal}
    
    <div id="container-uploader">
        <div id="marco">
            <iframe name="filedialog" id="filedialog" src="{$smarty.server.SCRIPT_NAME}?action=upload-form&path={$path}" width="360" height="50" frameborder="0"></iframe>
        </div>
        <div id="progress-container">
            <div id="progress_bar" style="display:none;"></div>
            <div id="upload-status">&nbsp;</div>
        </div>
    </div>
{/if}

{* FORM HTML ***************************************************************** *}
{if $ACTION_UPLOADER == 'upload-form'}
<html>
<head>
    <title>Gestor de ficheros</title>
	<link rel="stylesheet" href="{$params.JS_DIR}style.css" type="text/css" />
    <script language="javascript" type="text/javascript">
    var uniqid = '{$uniqid}';
    {literal}
    function sendData(frm) {
        frm.submit();
        window.parent.start_upload(uniqid);
    }
    </script>	
    <style>
    body {
        margin: 0;
        padding: 0;
    }
    
    .field-style {
        border: 1px solid #ccc;
    }
    </style>
    {/literal}
</head>
<body>
    <form action="{$smarty.server.SCRIPT_NAME}?action=upload-run" method="POST" enctype="multipart/form-data" onsubmit="return false;">
        <input type="hidden" name="MAX_FILE_SIZE" value="{$maxfilesize|default:"500000"}" />
        <input type="hidden" name="APC_UPLOAD_PROGRESS" value="{$uniqid}" />		
        <input type="file" name="archivo" class="field-style" />
		<input type="hidden" name="path" value="{$path}" />		
        <input type="button" value="Subir" class="field-style" onclick="sendData(this.form);return false;" />
    </form>
</body>
</html>
{/if}

{* FINISH UPLOAD ************************************************************* *}
{if $ACTION_UPLOADER == 'upload-finish'}
<html>
<head>
    <title>Gestor de ficheros</title>
	<link rel="stylesheet" href="{$params.CSS_DIR}admin.css" type="text/css" />
</head>
<body>
    
	{if strlen($smarty.request.message) <= 0}		
		<script language="javascript" type="text/javascript">
		{literal}
		function redirect_ui() {
			{/literal}
			parent.location.href="{$smarty.server.SCRIPT_NAME}?path={$smarty.request.path}&message=Fichero subido"; 
			{literal}
		}
		window.setTimeout(redirect_ui, 1000);
		{/literal}	
		</script>
	{else}
		<strong style="color:#f00;">{$smarty.request.message}</strong>
	{/if}
</body>
</html>
{/if}

{* POSTED YOUTUBE ************************************************************ *}
{if $ACTION_UPLOADER == 'posted-youtube'}
<html>
<head>
    <title>Gestor de ficheros</title>
	<link rel="stylesheet" href="{$params.CSS_DIR}admin.css" type="text/css" />
</head>
<body>
	{if strlen($smarty.request.message) <= 0}		
		<script language="javascript" type="text/javascript">
		{literal}
		function redirect_ui() {
			{/literal}
			parent.location.href="{$smarty.server.SCRIPT_NAME}?path={$smarty.request.path}&message=Video+subido+a+YouTube."; 
			{literal}
		}
		window.setTimeout(redirect_ui, 50);
		{/literal}	
		</script>
	{else}
		<strong style="color:#f00;">{$smarty.request.message}</strong>
	{/if}
</body>
</html>
{/if}