<?php
$url = "https://api.iijmio.jp/mobile/d/v1/log/packet/";

$curl = curl_init($url); // 初期化！

$mioponCliFile = file_get_contents('/root/.miopon-cli');
preg_match('/access_token:(.*)/', $mioponCliFile, $matches);

$accessToken = $matches[1];

if (empty($accessToken)) {
  echo "access token is not found.";
  exit(1);
}

$options = array(           // オプション配列
  //HEADER
  CURLOPT_HTTPHEADER => array(
    'X-IIJmio-Developer: 1NVP7BglwTio8UmJAQ3',
    'X-IIJmio-Authorization:' . $accessToken,
  ),
  //Method
  CURLOPT_HTTPGET => true,//GET
  CURLOPT_RETURNTRANSFER => true,
);

//set options
curl_setopt_array($curl, $options); /// オプション値を設定
// request
$result = curl_exec($curl); // リクエスト実行
//print
$result_array = json_decode($result, true);

/* ドライバ呼び出しを使用して ODBC データベースに接続する */
$host = 'db';
$dbname = getenv('MYSQL_DATABASE');
$dsn = "mysql:dbname=$dbname;host=$host";
$user = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

try {
  $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

foreach ($result_array['packetLogInfo'][0]['hdoInfo'] as $hdoInfoDetail) {
  $hdoServiceCode = $hdoInfoDetail['hdoServiceCode'];
  $dbh = new PDO($dsn, $user, $password);
  $sql = 'SELECT id FROM hdo_service_codes WHERE hdo_service_code = :hdo_service_code';
  $stmt = $dbh->prepare($sql);
  $stmt->execute(array(':hdo_service_code'=> $hdoServiceCode));
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $hdoServiceCodeId = $result['id'];

  foreach ($hdoInfoDetail['packetLog'] as $packetLogDetail) {
    $loggingDate = $packetLogDetail['date'];
    $withCouponPacket = $packetLogDetail['withCoupon'];
    $withoutCouponPacket = $packetLogDetail['withoutCoupon'];

    $dbh = new PDO($dsn, $user, $password);
    $sql = 'REPLACE INTO packet_logs VALUES (:hdo_service_code_id, :logging_date, :with_coupon_packet, :without_coupon_packet)';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array(':hdo_service_code_id'=> $hdoServiceCodeId, ':logging_date' => $loggingDate, 'with_coupon_packet' => $withCouponPacket, 'without_coupon_packet' => $withoutCouponPacket));
    }
}
