<?php
global $wpdb;
require_once '../../../wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

$url = 'https://api.24xem.com/api/json';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
curl_close($ch);
// print($result);
$link_soi_keo = $hot = "";
date_default_timezone_set('Asia/Ho_Chi_Minh');

$sql = '';
$dt = (json_decode($result, true));
$list_team = array("Chelsea",
	"Liverpool",
	"Manchester United",
	"Manchester City",
	"Leicester City",
	"Arsenal",
	"Everton",
	"Tottenham Hotspur",
	"Atletico de Madrid",
	"Winterthur F.C. Barcelona",
	"Real Madrid",
	"Paris Saint-Germain",
	"Bayern Munich",
	"Borussia Dortmund",
	"Juventus",
	"AC Milan",
	"Inter Milan",
	"Vietnam");
function array_partial_search($array, $keyword) {
	$found = FALSE;
	foreach ($array as $string) {
		if (strpos($keyword, $string) !== false) {
			$found = TRUE;
		}
	}
	return $found;
}

foreach ($dt as $k => $v) {

	$found = array_partial_search($list_team, $v['homeName']);
	$found1 = array_partial_search($list_team, $v['awayName']);

	// if (($found || $found1) && $v['sportType'] == 1){
	if (true) {
		$id = $v['matchId'];
		$ten_giai = $v['leagueName'];
		$homeName = $v['homeName'];
		$logo_doi_nha = $v['homeLogo'];
		$logo_doi_khach = $v["awayLogo"];
		$awayName = $v['awayName'];
		$leagueid = $v['leagueId'];

		$time = $v['matchDate'];

		$time = date("Y-m-d H:i", substr("$time", 0, 10));
		$linkurl = str_replace("24xem.com", "88xem.live", $v['linkUrl']);
		// insert_data_to_db($v['matchId'],$v['leagueName'],$v['homeName'],$v['awayName'],$v['matchDate'],$v['linkUrl']);
		$sql = "INSERT INTO `lich_bong_da`(`id`,`leagueid`, `giai`, `thoi_gian_da`, `doi_nha`, `logo_doi_nha`, `doi_khach`, `logo_doi_khach`, `id_xem_live`)";
		$sql .= "VALUES ('$id','$leagueid','$ten_giai', '$time','$homeName', '$logo_doi_nha', '$awayName','$logo_doi_khach', '$linkurl');";
		echo $sql;
		$wpdb->query($sql);
	}
}
