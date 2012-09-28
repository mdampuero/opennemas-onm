<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
require  __DIR__.'/../app/autoload.php';
require __DIR__.'/../config/config.inc.php';
require SITE_VENDOR_PATH.'/../vendor/adodb5/adodb.inc.php';

global $onmInstancesConnection;

$conn = ADONewConnection($onmInstancesConnection['BD_TYPE']);
$conn->Connect(
    $onmInstancesConnection['BD_HOST'],
    $onmInstancesConnection['BD_USER'],
    $onmInstancesConnection['BD_PASS'],
    $onmInstancesConnection['BD_DATABASE']
);

$rs = $conn->Execute('SELECT * FROM instances LIMIT 1');

while (!$rs->EOF) {

    $database = unserialize($rs->fields['settings']);

    $conn2 = ADONewConnection($database['BD_TYPE']);
    $conn2->Connect(
        $database['BD_HOST'],
        $database['BD_USER'],
        $database['BD_PASS'],
        $database['BD_DATABASE']
    );
    $conn2->SetFetchMode(ADODB_FETCH_ASSOC);

    $privileges = $conn2->Execute('SELECT * FROM privileges ORDER BY name');

    echo "<?php\n\$privileges = array(\n";
    while (!$privileges->EOF) {

        echo "\tarray(\n";
        foreach ($privileges->fields as $key => $value) {
            echo "\t\t'".$key."' => '".$value."',\n";
        }

        echo "\t),\n";

        $privileges->MoveNext();
    }
    echo ");";

    $rs->MoveNext();
}

