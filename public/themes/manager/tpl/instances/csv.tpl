id,name,activated,domains,site_title,site_description, site_language, activated_modules
{foreach from=$instances item=instance}
"{$instance->id}","{$instance->name}","{$instance->activated}","{implode(":", $instance->domains)}","{$instance->configs['site_title']}","{$instance->configs['site_description']}","{$instance->configs['site_language']}","{implode(", ", $instance->configs['activated_modules'])}"
{/foreach}