#!/usr/bin/php
<?php


function curl($url, $token) {
  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => $url.'?access_token='.$token,
    CURLOPT_USERAGENT => 'Backtick.io Forks Crawler (lyoshenka.github.io/backticklist)',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,

  ]);

  $response = curl_exec($curl);
  // $headers = curl_getinfo($curl);
  // $errorNumber = curl_errno($curl);
  // $errorMessage = curl_error($curl);

  curl_close($curl);
  return json_decode($response, true);
}



$token = trim(file_get_contents('token'));

$forks = curl("https://api.github.com/gists/6859173/forks", $token);

echo "Found " . count($forks) . " forks\n";

$tableData = [];
$count = 0:

foreach($forks as $fork) {
  $count++;
  if ($count % 10 == 0) {
    echo "$count\n";
  }

  $forkData = curl($fork['url'], $token);
  $desc = json_decode($forkData['files']['command.json']['content'], true);

  if ($desc['name'] == 'Example Command') {
    continue;
  }

  $tableData[] = [
    $desc['name'],
    $desc['description'],
    '<a href="' . $forkData['html_url'] . '">' . $forkData['user']['login'] . '/' . $forkData['id'] . '</a>'
  ];
}

echo count($tableData) . " non-example scripts found\n";

file_put_contents('data.json', json_encode(['aaData' => $tableData]));