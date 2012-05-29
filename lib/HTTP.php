<?php

namespace Conduit;

class HTTP {
  public $curl;

  function curl_init($url) {
    $this->curl = curl_init($url);
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl, CURLOPT_VERBOSE, DEBUG);
  }

  function curl_exec() {
    $data = curl_exec($this->curl);

    switch ($this->detect_content_type()) {
      case 'application/json':
        return json_decode($data, true);

      default:
        return $data;
    }
  }

  function status() {
    return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
  }

  function detect_content_type() {
    $parts = explode(';', curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE));
    return trim($parts[0]);
  }

  function http_build_url($url, $params) {
    if ($params) $url .= '?' . http_build_query($params);
    return $url;
  }

  function get($url, $params = array(), $headers = array(), $curl_params = array()) {
    $this->curl_init($this->http_build_url($url, $params));
    if ($headers) curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    if ($curl_params) curl_setopt_array($this->curl, $curl_params);
    return $this->curl_exec();
  }

  function post($url, $params, $headers = array(), $curl_params = array()) {
    $this->curl_init($url);
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
    if ($headers) curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    if ($curl_params) curl_setopt_array($this->curl, $curl_params);
    return $this->curl_exec();
  }
}