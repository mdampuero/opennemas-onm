<?php
/**
 * Check type of menu element and prepare link
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_renderLink($params, &$smarty)
{
    $item        = $params['item'];
    $type        = $item->type;
    $referenceId = $item->referenceId;

    $fetchItem = getItemByIdAndType($referenceId, $type);

    if (!$fetchItem) {
        $fetchItem = '';
    }

    dump($fetchItem);

    die();
}

function getItemByIdAndType($referenceId, $itemType)
{
    $fetchFunction = 'get' . ucfirst($itemType) . 'ByReference';

    if ($fetchFunction && function_exists($fetchFunction)) {
        return $fetchFunction($referenceId);
    }

    return false;
}

function getTagsByReference($referenceId)
{
    return $referenceId;
}
