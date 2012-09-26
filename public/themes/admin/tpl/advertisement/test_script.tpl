 {extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsadvertisement.js"}
    {script_tag src="/AdPosition.js"}
{/block}


{block name="content"}
<div id="content-wrapper" style="width:70%;margin:0 auto;">

    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs} >

           <h1>{t}Test for Javascript ad{/t}</h1>
            <div style="text-align:center; border: 2px dashed #CCC;">
                {$script}
            </div>

            <div align="right" style="margin: 10px 10px;">
                <a href="#" style="color: #666; font-size: large;"
                    onclick="window.close();" title="{t}Close window{/t}">[{t}Close{/t}]</a>
            </div>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" value="{$id|default:""}" />
    </form>
</div><!--fin content-wrapper-->
{/block}
