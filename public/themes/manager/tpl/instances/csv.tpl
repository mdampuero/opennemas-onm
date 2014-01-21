id,name,contact_mail,articles,images,ads,last_login,created,activated,domains,domain_expire,site_title,site_description, site_language, activated_modules
{foreach from=$instances item=instance}
"{$instance->id}","{$instance->name}","{$instance->configs['contact_mail']}","{$instance->totals[1]}","{$instance->totals[8]}","{$instance->totals[2]}","{datetime date=$instance->configs['last_login']}","{$instance->configs['site_created']}","{$instance->activated}","{implode(":", $instance->domains)}","{$instance->configs['domain_expire']}","{$instance->configs['site_title']}","{$instance->configs['site_description']}","{$instance->configs['site_language']}","{implode(", ", $instance->configs['activated_modules'])}"
{/foreach}
