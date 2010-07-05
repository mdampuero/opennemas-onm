<h1>An error occurred</h1>
<h2>{$message}</h2>

{if $smarty.const.APPLICATION_ENV == 'development'}
<h3>Exception information:</h3>
<p>
    <b>Message:</b> {$exception->getMessage()}
</p>

<h3>Stack trace:</h3>
<pre>{$exception->getTraceAsString()}
</pre>

<h3>Request Parameters:</h3>
{$request->getParams()|var_dump}

<h3>Request URI:</h3>
{$request->getRequestUri()|var_dump}    
{/if}

