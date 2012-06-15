{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    body {
        word-wrap:break-word;
    }
    .error-title {
        font-family:Arial, Helvetica, sans-serif;
        font-size:1.2em;
        color:#777;
        border-bottom: 1px solid #777;
    }
    .wrapper {
        width:900px;
        margin:0 auto;
    }
    .error-trace { font-family:Arial; margin-top:20px;
        box-shadow:0px 0px 15px #ccc;
    }
    .error-trace .title {
        background:#DD4B39; font-size:13px; color: #FFFFFF;
        padding: 4px 5px; border-radius:3px 3px 0 0;
    }
    .error-trace .title p { margin: 0px; padding: 0; }
    .error-trace .source {
        border-right: 1px solid #888; border-bottom: 1px solid #888;
        overflow: auto; background: #fff; font-family: monospace;
        font-size: 12px; margin: 0px; display:block;
    }
    .error-trace .source .highlighted { background:#ff9; }
    .error-trace .lineno.highlighted { background:#aaa; }
    .error-trace .lineno {
        color: #333; padding:3px 10px 3px 0px; min-width:45px; margin:0;
        background:#ccc; display:inline-block; text-align:right;
    }
    .error-trace .backtrace table {
        display:block; font-family:monospace; border:1px solid #888;
        width:100%; padding:0; margin:0;
        padding-left:10px;
    }
    .error-trace .backtrace  table td { padding-right:30px; }
    .error-trace .backtrace table th { font-weight:bold; text-align:left;}
    .error-trace .backtrace .title {
        width:100%; background:#aaa; color:#333; font-size:12px;
        text-transform:uppercase; border-radius:0px; padding:0; color: White;
        font-weight:bold;
    }
    .error-trace .backtrace .title span {
        padding:3px 10px; display:block
    }
</style>
{/block}

{block name="content"}
<div class="wrapper-content welcome-page">
    {if $environment == 'development'}
        <h1 class="error-title">There was an error in onm</h1>
        <div class="error-trace">
            <div class="title {if $error->getCode() == 1}error{/if}">
                <p>
                    ( ! ) Exception: {$error|get_class} - {$error->getMessage()} {$errorMessage} :  in
                    {$error->getFile()} on line {$error->getLine()}
                </p>
            </div>
            {if is_array($backtrace)}
            <div class="backtrace">
                <div class="title"><span>Backtrace:</span> </div>
                <table>
                    <tbody>
                        <tr>
                            <th>File</th>
                            <th>Line</th>
                        </tr>
                        {foreach from=$backtrace item=trace_step}
                        <tr>
                            <td>
                                <a href="file://{$trace_step['file']}"> {$trace_step['file']}</a>

                                <p>Class: {$trace_step['class']}::{$trace_step['function']}()</p>
                                <p>
                                    Args:
                                    {$trace_step['args']|print_r}
                                </p>
                            </td>
                            <td>{$trace_step['line']}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {/if}
        </div>
    {else}
        Not content available
    {/if}
</div>
{/block}
