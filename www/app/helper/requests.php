<?php
// REQUESTS
function get($name, $noFilter = false) {

  if ($noFilter) return $_GET[$name];

  if(isset($_GET[$name])) {

    if (is_array($_GET[$name])) {
      return array_map(function($item) {
        return filterUrl($item);
      }, $_GET[$name]);
    }

    return filterUrl($_GET[$name]);

  }
  return false;
}

function post($name, $noFilter = false) {

  if ($noFilter) return $_POST[$name];

  if(isset($_POST[$name])) {

    if (is_array($_POST[$name])) {
      return array_map(function($item) {
        return filterUrl($item);
      }, $_POST[$name]);
    }

    return filterUrl($_POST[$name]);

  }
  return false;
}

function request($name, $noFilter = false) {

  if ($noFilter) return $_REQUEST[$name];

  if(isset($_REQUEST[$name])) {

    if (is_array($_REQUEST[$name])) {
      return array_map(function($item) {
        return filterUrl($item);
      }, $_REQUEST[$name]);
    }

    return filterUrl($_REQUEST[$name]);

  }
  return false;
}