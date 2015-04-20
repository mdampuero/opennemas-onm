{extends file="base/admin.tpl"}

{block name="content"}
  <div class="content">
    {if $environment == 'development'}
       <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
          <div class="navbar-inner">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <h4>
                  <i class="fa fa-warning"></i>
                  {t}Exception{/t} {$error->getStatusCode()}
                </h4>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="error-container env-{$environment}">
        <div class="error-trace">
          <div class="title {if $error->getCode() == 1}error{/if}">
            <h4>
              ( ! ) Exception: \{$error|get_class}{if $error->getMessage() != ''}- '{$error->getMessage()}'{/if}:  in
              <a href="file://{$error->getFile()}">{$error->getFile()}</a> on line {$error->getLine()}
            </h4>
          </div>
          <div class="source m-l-30">
            {$preview}
          </div>
          {if is_array($backtrace)}
            <div class="backtrace m-t-30">
              <h4 class="title">Backtrace:</h4>
              {foreach from=$backtrace item=trace_step}
                <div class="m-t-30">
                  {if $trace_step['file']}
                    <p>
                      <strong>File: </strong> <a href="file://{$trace_step['file']}"> {$trace_step['file']}</a> (line {$trace_step['line']})
                    </p>
                  {/if}
                  {if $trace_step['class']}
                    <p>
                      <strong>Method:</strong> {$trace_step['class']}::{$trace_step['function']}()
                    </p>
                  {/if}
                  {if is_array($trace_step['args']) && count($trace_step['args']) > 0}
                    <p>
                      <strong>Args:</strong>
                      {foreach from=$trace_step['args'] item=arg}
                        <div class="m-l-30">
                          {$arg|var_dump}
                        </div>
                      {/foreach}
                    </p>
                  {/if}
                </div>
              {/foreach}
            </div>
          {/if}
        </div>
      </div>
    {else}
      <div class="error-container">
        <div class="error-main">
          <div class="error-number"> {$error->getStatusCode()} </div>
          <div class="error-description-mini"> {$error_message|default:"Unknown error"}</div>
        </div>
      </div>
    {/if}
  </div>
{/block}
