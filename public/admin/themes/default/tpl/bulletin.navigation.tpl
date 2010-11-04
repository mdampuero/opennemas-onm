{* Bulletin navigation *}
<script language="javascript">
{literal}
function goToStep(paso) {
    if(paso!='{/literal}{$ACTION}{literal}') {
        $('action').value = paso;
        $('bulletin_wizard').submit();
    }
    
    return false;
}
{/literal}
</script>
<div class="pasos">
    {if ($ACTION == 'select') || ($ACTION == 'step0') }
        <table border="0" class="pasos">
        <tr>
            <td valign="middle" align="center" {if $ACTION == 'select'}style="border: 1px solid #369;"{/if}>
                <img src="{$params.IMAGE_DIR}bulletin/paso0.jpg" class="icon_paso" alt="PASO 1" />
            </td>
            <td valign="middle" align="center">
                <a href="#" onclick="goToStep('news');">
                    <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" /></a>
            </td>        
            <td valign="middle" align="center" {if $ACTION == 'news' || $ACTION == 'step1'}style="border: 1px solid #369;"{/if}>
                <img src="{$params.IMAGE_DIR}bulletin/paso1.jpg" class="icon_paso" />
            </td>
            <td valign="middle" align="center">                
                <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" />
            </td>
            <td valign="middle" align="center" {if $ACTION == 'opinions' || $ACTION == 'step2'}style="border: 1px solid #369;"{/if}>
                <img src="{$params.IMAGE_DIR}bulletin/paso2.jpg" class="icon_paso" />
            </td>
            <td valign="middle" align="center">
                <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" />
            </td>
            <td valign="middle" align="center" {if $ACTION == 'mailboxes' || $ACTION == 'step3'}style="border: 1px solid #369;"{/if}>
                <img src="{$params.IMAGE_DIR}bulletin/paso3.jpg" class="icon_paso" />
            </td>
            <td valign="middle" align="center">
                <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" />
            </td>
            <td valign="middle" align="center" {if $ACTION == 'preview' || $ACTION == 'step4'}style="border: 1px solid #369;"{/if}>
                <img src="{$params.IMAGE_DIR}bulletin/paso4.jpg" class="icon_paso" />
            </td>
            <td valign="middle" align="center">
                <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" />
            </td>
            <td valign="middle" align="center">
                <img src="{$params.IMAGE_DIR}bulletin/paso5.jpg" class="icon_paso" />
            </td>    
        </tr>
        </table>    
    {else}
        <table border="0" class="pasos">
        <tr>
            <td valign="middle" align="center" {if $ACTION == 'step0'}style="border: 1px solid #369;"{/if}>
                <a href="#" onclick="goToStep('select');"><img src="{$params.IMAGE_DIR}bulletin/paso0.jpg" class="icon_paso" /></a>
            </td>
            <td valign="middle" align="center">
                <a href="#" onclick="goToStep('news');">
                    <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" /></a>
            </td>        
            <td valign="middle" align="center" {if $ACTION == 'news' || $ACTION == 'step1'}style="border: 1px solid #369;"{/if}>
                <a href="#" onclick="goToStep('news');"><img src="{$params.IMAGE_DIR}bulletin/paso1.jpg" class="icon_paso" /></a>
            </td>
            <td valign="middle" align="center">
                <a href="#" onclick="goToStep('opinions');">
                    <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" /></a>
            </td>
            <td valign="middle" align="center" {if $ACTION == 'opinions' || $ACTION == 'step2'}style="border: 1px solid #369;"{/if}>
                <a href="#" onclick="goToStep('opinions');"><img src="{$params.IMAGE_DIR}bulletin/paso2.jpg" class="icon_paso" /></a>
            </td>
            <td valign="middle" align="center">
                <a href="#" onclick="goToStep('mailboxes');">
                    <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" /></a>
            </td>
            <td valign="middle" align="center" {if $ACTION == 'mailboxes' || $ACTION == 'step3'}style="border: 1px solid #369;"{/if}>
                <a href="#" onclick="goToStep('mailboxes');"><img src="{$params.IMAGE_DIR}bulletin/paso3.jpg" class="icon_paso" /></a>
            </td>
            <td valign="middle" align="center">
                <a href="#" onclick="goToStep('preview');">
                    <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" /></a>
            </td>
            <td valign="middle" align="center" {if $ACTION == 'preview' || $ACTION == 'step4'}style="border: 1px solid #369;"{/if}>
                <a href="#" onclick="goToStep('preview');"><img src="{$params.IMAGE_DIR}bulletin/paso4.jpg" class="icon_paso" /></a>
            </td>
            <td valign="middle" align="center">
                <a href="#" onclick="goToStep('send');">
                    <img src="{$params.IMAGE_DIR}bulletin/siguiente.gif" border="0" /></a>
            </td>
            <td valign="middle" align="center">
                <img src="{$params.IMAGE_DIR}bulletin/paso5.jpg" class="icon_paso" />
            </td>    
        </tr>
        </table>
    {/if}
</div>
<br class="clearer" />