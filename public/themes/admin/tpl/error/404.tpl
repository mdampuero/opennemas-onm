{extends file="base/admin.tpl"}

{block name="quick-create"}{/block}
{block name="sidebar"}{/block}

{block name="header"}
<header class="header navbar navbar-inverse">
  <div class="navbar-inner">
    <div class="header-seperation">
      <a class="header-logo pull-left" href="{url name=admin_welcome}">
        <h1>
          open<strong>nemas</strong>
        </h1>
      </a>
    </div>
    <div class="header-quick-nav">
    </div>
  </div>
</header>
{/block}

{block name="page_container"}
  {if $environment == 'development'}
    <div class="error-container env-{$environment} p-t-50 p-l-30 p-r-30 p-b-30">
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
{/block}
