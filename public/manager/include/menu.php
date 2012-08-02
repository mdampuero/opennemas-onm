<?php

$menuXml = '<?xml version="1.0"?>
<menu>
    <submenu title="'.htmlspecialchars(_("Instances"), ENT_QUOTES).'" id="frontpage" link="#">
        <node
            title="'.htmlspecialchars(_("Instance Manager"), ENT_QUOTES).'"
            id="instance_manager"
            link="'.url('manager_instances').'"
        />
    </submenu>
    <submenu title="'.htmlspecialchars(_("Framework"), ENT_QUOTES).'" id="system" link="#">
        <node
            title="'.htmlspecialchars(_("Status"), ENT_QUOTES).'"
            id="support_help"
            link="'.url('manager_framework_status').'"
        />
    </submenu>
</menu>';
