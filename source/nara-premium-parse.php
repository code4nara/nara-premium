<?php

  require_once './nara-premium-config.php';

  $csvdata = '';

  foreach ($ids as $id) {
    $filename = "{$scraped_dir}/{$id}.data";
    if (is_file($filename)) {
      $htmldoc = file_get_contents($filename);
    } else {
      continue;
    }
	$htmllines = explode("\n", $htmldoc);
	$htmllines = array_map('trim', $htmllines);
	$htmllines = array_filter($htmllines, 'strlen');
	$htmllines = array_values($htmllines);

	$shop_name = preg_replace('/<h3 class="title">/i', '', $htmllines[46]);
	$shop_name = preg_replace('/<\/h3>/i', '', $shop_name);
	$shop_name = '"'.$shop_name.'"';

	$shop_category = preg_replace('/<td>/i', '', $htmllines[52]);
	$shop_category = preg_replace('/<br><\/td>/i', '', $shop_category);
	$shop_category = preg_replace('/<br>/i', '', $shop_category);
	$shop_category = preg_replace('/、/i', ',', $shop_category);
	$shop_category = '"'.$shop_category.'"';

	$shop_fulladdress = preg_replace('/<td>/i', '', $htmllines[56]);
	$shop_fulladdress = preg_replace('/<\/td>/i', '', $shop_fulladdress);
	$shop_fulladdress = '"'.$shop_fulladdress.'"';

	$shop_post = preg_replace('/"/i', '', $shop_fulladdress);
	$shop_post = preg_replace('/〒/i', '', $shop_post);
	$shop_post = mb_convert_kana($shop_post, 's');
	$parse_post = explode(' ', $shop_post);
	$shop_post = array_shift($parse_post);

	$shop_address = preg_replace('/<iframe src=\"https:\/\/www.google.com\/maps\?output=embed&q=/i', '', $htmllines[66]);
	$shop_address = preg_replace('/\&z=12.*$/i', '', $shop_address);
	$shop_address = preg_replace('/ー/i', '-', $shop_address);
	$parse_address = explode(' ', $shop_address);
	$shop_address = mb_convert_kana($parse_address[0], 'asKV');
	$shop_address = '"'.$shop_address.'"';

	$csvdata .= sprintf("%d,%s,%s,%s,%s,%s\n", $id, $shop_name, $shop_category, $shop_fulladdress, $shop_post, $shop_address);
  }
  file_put_contents($parsed_file, $csvdata);
