<?php
  require_once './nara-premium-config.php';

  $conn = curl_init(); // cURLセッションの初期化
  curl_setopt($conn, CURLOPT_RETURNTRANSFER, true); // 実行結果を文字列で返す。
  
  foreach ($ids as $id) {
    $url = "https://www.premium-nara.jp/detail.php?id={$id}";
    $filename = "{$scraped_dir}/{$id}.data";
    curl_setopt($conn, CURLOPT_URL, $url); //　取得するURLを指定
    $res =  curl_exec($conn);
    $httpcode = curl_getinfo($conn, CURLINFO_RESPONSE_CODE);
    if ($httpcode == 200) {
      fprintf(STDERR, "ISDATA:O%s\n", $id);
      if(FALSE === is_dir($scraped_dir)) {
        mkdir($scraped_dir);
      }
      file_put_contents($filename, $res);
    } else {
      fprintf(STDERR, "NODATA:O%s\n", $id);
    }
  }

  curl_close($conn); //セッションの終了
?>
