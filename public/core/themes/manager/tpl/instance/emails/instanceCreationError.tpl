{t}There was an error while creating an instance with the following data:{/t}

{t}Instance name{/t}: {$instance->name}
{t}Instance internal_name{/t}: {$instance->internal_name}
{t}Instance contact_mail{/t}: {$instance->contact_mail}
{t}Instance domains{/t}: {implode(', ', $instance->domains)}

{t}Error{/t}:
Message: {$exception->getMessage()}:
Trace:
{$exception->getTraceAsString()}
