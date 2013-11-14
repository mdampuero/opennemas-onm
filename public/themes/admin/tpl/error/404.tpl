{extends file="base/admin.tpl"}

{block name="content"}
<div class="wrapper-content error-page">
{if $environment == 'development'}
    <div class="error-page-message env-{$environment}">
        <div class="icon">:(</div>
        <div class="message">{$error_message|default:"Unknown error"}</div>
        <div class="error-trace">
            <div class="title {if $error->getCode() == 1}error{/if}">
                <p>
                    ( ! ) Exception: \{$error|get_class}{if $error->getMessage() != ''}- '{$error->getMessage()}'{/if}:  in
                    <a href="file://{$error->getFile()}">{$error->getFile()}</a> on line {$error->getLine()}
                </p>
            </div>
            <div class="source">
                {$preview}
            </div>
            {if is_array($backtrace)}
            <div class="backtrace">
                <div class="title"><span>Backtrace:</span> </div>
                <table>
                    <tbody>
                        <tr>
                            <th>File</th>
                        </tr>
                        {foreach from=$backtrace item=trace_step}
                        <tr>
                            <td><a href="file://{$trace_step['file']}"> {$trace_step['file']}</a> (line {$trace_step['line']})
                                <p><strong>Method:</strong> {$trace_step['class']}::{$trace_step['function']}()</p>
                                {if is_array($trace_step['args']) && count($trace_step['args']) > 0}
                                <p>
                                    <strong>Args:</strong>
                                        {foreach from=$trace_step['args'] item=arg}
                                            {$arg|var_dump}
                                        {/foreach}
                                </p>
                                {/if}
                            </td>
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
        {if $error_id}
        <div class="error-tracing">{t 1=$error_id}We already have being informed of this error: %1{/t}</div>
        {/if}
    </div>
{/if}
</div>
{/block}
