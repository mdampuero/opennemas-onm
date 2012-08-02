{extends file="base/base.tpl"}

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
        display:block; font-family:monospace;
        padding:0; margin:0;
        border-top:0 none;
        width:100%;
    }
    .error-trace .backtrace table tbody {
        border:1px solid #888;
        display:block
    }
    .error-trace .backtrace table tr {
        width:100%;
    }
    .error-trace .backtrace  table td:first-child,
    .error-trace .backtrace  table th:first-child {
        padding-left:10px;
    }
    .error-trace .backtrace  table td:not(:last-child) { padding-right:30px; }
    .error-trace .backtrace table th { font-weight:bold; text-align:left;}
    .error-trace .backtrace .title {
        width:100%; background:#aaa; color:#333; font-size:12px;
        text-transform:uppercase; border-radius:0px; padding:0; color: White;
        font-weight:bold;
    }
    .error-trace .backtrace .title span {
        padding:3px 10px; display:block
    }
    .right {
        text-align:right
    }
</style>
{/block}

{block name="content"}
<div class="wrapper-content error-page">
{if $environment == 'development'}
    <div class="error-page-message env-{$environment}">
        <div class="icon">:(</div>
        <div class="message">{$error_message|default:"Unknown error"}</div>
        <div class="error-trace">
            <div class="title {if $error->getCode() == 1}error{/if}">
                <p>
                    ( ! ) Exception: \{$error|get_class} - '{$error->getMessage()}' :  in
                    {$error->getFile()} on line {$error->getLine()}
                </p>
            </div>
            {if is_array($backtrace)}
            <div class="backtrace">
                <div class="title"><span>Backtrace:</span> </div>
                <table>
                    <tbody>
                        <tr>
                            <th style="width:90%">File</th>
                            <th class="right" style="width:100px">Line</th>
                        </tr>
                        {foreach from=$backtrace item=trace_step}
                        <tr>
                            <td>
                                <a href="file://{$trace_step['file']}"> {$trace_step['file']}</a>

                                <p>Class: {$trace_step['class']}::{$trace_step['function']}()</p>
                                {foreach from=$trace_step['args'] item=arg}
                                {$arg|var_dump}
                                {/foreach}
                            </td>
                            <td>{$trace_step['line']}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {/if}
        </div>
    </div>
{else}
    <div class="error-page-message error-mini">
        <div class="icon">:(</div>
        <div class="message">{$error_message}</div>
        <div class="error-tracing">{t 1=$error_id}We already have being informed of this error: %1{/t}</div>
    </div>
{/if}
</div>
{/block}
