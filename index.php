<?php
include_once('DBHandler.php');

$db = new DB(['dbname'=>'local.test']);

if ($db) {

  $update = $db->update('customers', ['FullName' => 'Eminem', 'Email' => 'emin@em313.com'])->where('CustomerID', '3')->_or()->where('PostCode', 'WA9 3XT');
  echo $update->run(), ' rows affected.';

  echo '<br /><br />';

  $insert = $db->insert('customers', [
    ['FullName', 'PostCode', 'Email'],
    ['MP Eriksson', 'WA1 3DF', 'm.eriksson@crownoil.co.uk']
  ]);
  echo $insert->run(), ' rows affected.';

  echo '<br /><br />';

  $delete = $db->delete('customers')->where('FullName', 'MP Eriksson');
  echo $delete->run(), ' rows affected.';

} else {
  echo 'Connection failed';
}
