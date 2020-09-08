<?php
/*
Plugin Name: SIA Custom Plugin
*/

## Get numeric group ID for given name
function civi_group_id($name) {
  $result = civicrm_api3('Group', 'get', array(
    'sequential' => 1,
    'return' => array("id"),
    'title' => $name,
  ));
  return $result['values'][0]['id'];
}

## Voice-part index
function vp_index($a) {
  $vp_order = array('soprano' => 1, 'alto' => 2, 'tenor' => 3, 'bass' => 4);
  $a = preg_replace("/ .*/", "", $a);
  $out = $vp_order[ strtolower($a) ];
  return $out;
}

## Voice-part sort
function vp_sort($a, $b) {
  $res = vp_index($a) - vp_index($b);
  if ($res) return $res;
  return strcmp($a, $b);
}

function singer_data($groups=['Current Singers']) {
  require_once '/home/singersin/www/wp/wp-content/plugins/civicrm/civicrm/api/class.api.php';
  civicrm_initialize();
  $api = new civicrm_api3();
  $group_list = array_filter(array_map('civi_group_id', $groups), function($x) {return isset($x);});
  $api->Contact->Get(array('group' => $group_list,
    'sequential' => 1,
    'rowCount' => 10000,
    'return' => 'custom_4,display_name,email,phone,'.
      'street_address,city,state_province,postal_code,id',
    'sort'=>'custom_4,last_name,first_name'));
  return $api;
}

function singer_list() {
  $api = singer_data();

  $arr = array();
  foreach($api->values as $c) {
    $arr[$c->custom_4][] = $c->display_name;
  }
  uksort($arr, "vp_sort");

  $left = '';
  $right = '';
  foreach ($arr as $vp => $p) {
    if ($vp == '')  continue;

    $chunk = "<strong>{$vp}</strong>:<br>\n";

    foreach ($p as $name)
      $chunk .= "&nbsp;&nbsp;&nbsp;&nbsp;{$name}<br>\n";

    if (preg_match('/bass|tenor/i', $vp)) {
      $right .= $chunk;
    } else {
      $left .= $chunk;
    }
  }

  return( array($left, $right) );
}
