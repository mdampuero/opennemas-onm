<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Adapter;

use Common\Data\Core\Adapter;

/**
 * This adapter allows us to modify classes where one or more fields may have several values but we want to return
 * one. The opposite may also be the case. Initially it has only one value, but we want to return it as if it were
 * multivalued.
 */
class MultiOptionAdapter extends Adapter
{

    /**
     *  Param id for default value in multivalued fields
     */
    const PARAM_DEFAULT_KEY_VALUE = "DEFAULT_VALUE";

    /**
     *  Param id for the list of multivalued fields
     */
    const PARAM_MULTIVALUED_FIELDS = "MULTIVALUED_FIELDS";

    /**
     * Param id for the value of the fields
     */
    const PARAM_KEY_FOR_MULTIVALUED_FIELDS = "KEY_FOR_MULTIVALUED_FIELDS";


    /**
     * The behavior of this component changes depending on the input parameters. If you indicate which key of
     * multi-valuated fields must return, only that value is returned. if this key does not exist, use the default
     * and if neither the default key return the first one. But if not exist key for multivalued the response will be
     * the multivalued field. if the value is not multivalued what it will do is to transform it into one using the
     * default value as the key.
     *
     * @param mixed $item Object with multivalued fields to adap
     * @param array $params you can have several input parameters depending on the required functionality:
     *
     *      - PARAM_DEFAULT_KEY_VALUE:          Required    String value to be used if the field is not multivalued
     *      - PARAM_MULTIVALUED_FIELDS:         Required    List with all multivalued fields of the object
     *      - PARAM_KEY_FOR_MULTIVALUED_FIELDS:             Key of the value to return in the multivalued fields.
     *
     * @return mixed The same object but with the fields adapted
     */
    public function adapt($item, $params = [])
    {
        if (empty($item) ||
                (!is_array($item) && !is_object($item)) ||
                !isset($params[self::PARAM_MULTIVALUED_FIELDS]) ||
                empty($params[self::PARAM_MULTIVALUED_FIELDS]) ||
                !isset($params[self::PARAM_DEFAULT_KEY_VALUE])
        ) {
            return $item;
        }

        foreach ($params[self::PARAM_MULTIVALUED_FIELDS] as $field) {
            if (is_object($item)) {
                if (property_exists($item, $field)) {
                    $item->{$field} = self::adaptField($item->{$field}, $params);
                }

                continue;
            }

            if (array_key_exists($fields, $item)) {
                $item[$field] = self::adaptField($item[$field], $params);
            }
        }

        return $item;
    }

    /**
     * The behavior of this function is the same of the adapt method but only for one multivalued field. How you only
     * have one field, the param of the list of multivalued fields not is needed
     *
     * @param       $field Multivalued field with multivalued value or not to adap
     * @param array $params you can have several input parameters depending on the required functionality:
     *
     *      - PARAM_DEFAULT_KEY_VALUE:            String value to be used if the field is not multivalued
     *      - PARAM_KEY_FOR_MULTIVALUED_FIELDS:   Identifier of the value to return in the multivalued fields.
     *
     * @return mixed The field adapted to a multivalued o simple value
     */
    public static function adaptField($field, array $params)
    {
        $keyMultivalued = null;
        if (isset($params[self::PARAM_KEY_FOR_MULTIVALUED_FIELDS])) {
            $keyMultivalued = $params[self::PARAM_KEY_FOR_MULTIVALUED_FIELDS];
        }

        $defaultValue = $params[self::PARAM_DEFAULT_KEY_VALUE];
        if (isset($params[self::PARAM_KEY_FOR_MULTIVALUED_FIELDS])) {
            if (!is_array($field) || sizeOf($field) == 0) {
                return $field;
            }

            if (isset($field[$keyMultivalued])) {
                return $field[$keyMultivalued];
            }

            if (!empty($defaultValue) && isset($field[$defaultValue])) {
                return $field[$defaultValue];
            }

            return next($field);
        }

        if (!is_array($field)) {
            return [$defaultValue => $field];
        }

        return $field;
    }
}
