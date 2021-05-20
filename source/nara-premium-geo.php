<?php

  require_once './nara-premium-config.php';

  $conn = curl_init(); // cURLセッションの初期化
  curl_setopt($conn, CURLOPT_RETURNTRANSFER, true); // 実行結果を文字列で返す。

  $csvdoc = file_get_contents($parsed_file);
  $csvlines = explode("\n", $csvdoc);
  $csvdata = '';

  foreach ($csvlines as $line) {
    if(0 == strlen($line)) {
      break;
    }
    $data = str_getcsv($line);
    $id = $data[0];
    $query = $data[5];

    $url = "https://msearch.gsi.go.jp/address-search/AddressSearch?q={$query}";
    curl_setopt($conn, CURLOPT_URL, $url); //　取得するURLを指定
    $res =  curl_exec($conn);
    if (strlen($res) > 0) {
      $geocode = json_decode($res);
      $lng = $geocode[0]->geometry->coordinates[0];
      $lat = $geocode[0]->geometry->coordinates[1];
      fprintf(STDERR, "ISDATA:%04d (%f,%f)\n", $id, $lng, $lat);
      $csvdata .= sprintf("%s,%f,%f\n", $line, $lng, $lat);
    } else {
      fprintf(STDERR, "NODATA:%04d\n", $id);
    }
  }
  curl_close($conn); //セッションの終了
  file_put_contents($geocoded_file, $csvdata);
