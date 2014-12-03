{t}List of modules to hire{/t}:

{foreach $modules['upgrade'] as $key => $module}
    {$module@index + 1}.- {$key} ({$module})
{/foreach}

{t}List of modules to unsubscribe{/t}:

{foreach $modules['downgrade'] as $key => $module}
    {$module@index + 1}.- {$key} ({$module})
{/foreach}