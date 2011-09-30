<?php

function smarty_modifier_mb_capitalize($string)
{
    return ucfirst(mb_strtolower($string, 'UTF-8'));
}
