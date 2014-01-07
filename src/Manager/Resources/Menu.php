<?php
/**
 * Defines the manager menu
 *
 * @package Manager
 */
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
            title="'.htmlspecialchars(_("Commands"), ENT_QUOTES).'"
            id="framework_commands"
            link="'.url('manager_framework_commands').'"
        />
        <node
            title="'.htmlspecialchars(_("Status"), ENT_QUOTES).'"
            id="support_help"
            link="'.url('manager_framework_check_dependencies').'"
        />
        <node
            title="'.htmlspecialchars(_("Opcache status"), ENT_QUOTES).'"
            id="support_help"
            link="'.url('manager_framework_opcache_status').'"
        />
    </submenu>
    <submenu title="'.htmlspecialchars(_("Settings"), ENT_QUOTES).'" id="system" link="#">
        <submenu title="'.htmlspecialchars(_("Users & Groups"), ENT_QUOTES).'" id="user-group_manager" link="#">
            <node
                title="'.htmlspecialchars(_("Users"), ENT_QUOTES).'"
                id="user_manager"
                link="'.url("manager_acl_user", array()).'"
            />
            <node
                title="'.htmlspecialchars(_("User Groups"), ENT_QUOTES).'"
                id="user_group_manager"
                link="'.url("manager_acl_usergroups", array()).'"
            />
        </submenu>
    </submenu>
</menu>';
