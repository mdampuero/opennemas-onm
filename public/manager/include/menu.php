<?php

$menuXml = '<?xml version="1.0"?>
<menu>
    <submenu title="'.htmlspecialchars(_("Instances"), ENT_QUOTES).'" id="frontpage" link="#">
        <node
            title="'.htmlspecialchars(_("Instance Manager"), ENT_QUOTES).'"
            id="instance_manager"
            link="controllers/instances/instances.php"
        />
    </submenu>
    <submenu title="'.htmlspecialchars(_("System"), ENT_QUOTES).'" id="system" link="#">
        <node
            title="'.htmlspecialchars(_("Global settings"), ENT_QUOTES).'"
            id="settings_manager"
            link="controllers/system_settings/system_settings.php"
        />
        <node class="divider" />
        <node
            title="'.htmlspecialchars(_("Global cache"), ENT_QUOTES).'"
            id="cache_manager"
            link="controllers/cache_manager/cache_manager.php"
        />
        <node
            title="'.htmlspecialchars(_("Support and Help"), ENT_QUOTES).'"
            id="support_help"
            link="http://www.openhost.es/"
            target="external"
        />
    </submenu>
</menu>';
