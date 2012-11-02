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
            title="'.htmlspecialchars(_("Framework status"), ENT_QUOTES).'"
            id="support_help"
            link="'.url('manager_framework_check_dependencies').'"
        />
        <node
            title="'.htmlspecialchars(_("APC status"), ENT_QUOTES).'"
            id="support_help"
            link="'.url('manager_framework_apc').'"
        />
    </submenu>
</menu>';

