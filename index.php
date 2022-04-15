<?php
/*
Plugin Name: AP - Lịch bóng đá
Description: Cập nhật lịch bóng đá của các giải!
Author: T-DC
Version: 1.5
 */
if (!defined('DC_LBD')) {
	define('DC_LBD', dirname(__FILE__) . '/');
	define('PLUGIN_VERSION', '1.5');
}
date_default_timezone_set('Asia/Ho_Chi_Minh');

$timestamp =strtotime( '+1 day' );
wp_schedule_single_event($timestamp,'_auto_get', NULL);
function _auto_get(){
  $url = 'https://api.24xem.com/api/json';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = curl_exec($ch);
  curl_close($ch);
  // print($result);
  $link_soi_keo = $hot = "";

  $sql = '';
  $dt = (json_decode($result, true));
  $list_team = array(		"Chelsea",
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
  		function array_partial_search( $array, $keyword ) {
  		    $found = FALSE;
  		    foreach ( $array as $string ) {
  		        if ( strpos($keyword,$string ) !== false ) {
  		            $found = TRUE;
  		        }
  		    }
  		    return $found;
  		}

  foreach ($dt as $k => $v) {

  	$found = array_partial_search( $list_team, $v['homeName']);
  	$found1 = array_partial_search( $list_team, $v['awayName']);

  	if (($found || $found1) && $v['sportType'] == 1){
  	// if (true){
  		$id = $v['matchId'];
  		$ten_giai = $v['leagueName'];
  		$homeName = $v['homeName'];
  		$logo_doi_nha = $v['homeLogo'];
  		$logo_doi_khach=$v["awayLogo"];
  		$awayName = $v['awayName'];
  		$leagueid = $v['leagueId'];

  		$time = $v['matchDate'];

   		$time= date("Y-m-d H:i", substr("$time", 0, 10));
  		$linkurl =str_replace("24xem.com", "88xem.live",  $v['linkUrl']);
  		// insert_data_to_db($v['matchId'],$v['leagueName'],$v['homeName'],$v['awayName'],$v['matchDate'],$v['linkUrl']);
  		 $sql ="INSERT INTO `lich_bong_da`(`id`,`leagueid`, `giai`, `thoi_gian_da`, `doi_nha`, `logo_doi_nha`, `doi_khach`, `logo_doi_khach`, `id_xem_live`)";
  		 $sql .="VALUES ('$id','$leagueid','$ten_giai', '$time','$homeName', '$logo_doi_nha', '$awayName','$logo_doi_khach', '$linkurl');";
  		 echo $sql;
  		 $wpdb->query($sql);
  	}
  }
}
function add_custom_files_lbd(){
  $wp_scripts = wp_scripts();
  wp_enqueue_script("jquery");

  wp_register_script('lich-bong-da', plugins_url('js/script.js', __FILE__),'2');
  wp_enqueue_script('lich-bong-da');
  wp_register_style('lich-bong-da-css', plugins_url('css/style.css', __FILE__),'2');
  wp_enqueue_style('lich-bong-da-css');
  wp_localize_script('lich-bong-da', 'dc_var',
  	array(
  		'ajax_url' => admin_url('admin-ajax.php'),
  		'url_post' =>  WP_PLUGIN_URL.'/lich_bong_da/request_data.php',
  		'url_scrapper' => WP_PLUGIN_URL.'/lich_bong_da/api_data.php'
  	)
  );
  wp_enqueue_script('lbd_js_bootstrap', plugins_url('libs/bootstrap.min.js', __FILE__ ),false,'3.3.7',false);
  wp_enqueue_style('lbd_css_bootstrap', plugins_url('libs/bootstrap.min.css', __FILE__ ),true,'3.3.7','all');

}
add_action( 'wp_enqueue_scripts', 'add_custom_files_lbd' );

// Đoạn này show data cho end-users
function lbd_Load_admin_manager() {
  add_custom_files_lbd();
	require_once DC_LBD . '/includes/lbd_admin.php';
}
function api_get_data() {

}
// function su dung shortcode
function load_short_code($atts, $content = null) {

  $atts = shortcode_atts(
         array(
             'leagueid' => 'all',
         ), $atts, 'lich-bong-da');
  // return $atts['leagueid'];
  // ob_start();


	require DC_LBD . '/shortcode_view.php';
  // return ob_get_clean();

}
function group_by_sort($key, $data) {
    $result = array();

    foreach($data as $val) {
        if(array_key_exists($key, $val)){
            $result[$val[$key]][] = $val;
        }else{
            $result[""][] = $val;
        }
    }

    return $result;
}
add_action ('group_by_sort_array', 'group_by_sort');

function check_table_exist() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name = "lich_bong_da";
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE `lich_bong_da` (
			  `id` varchar(50) NOT NULL,
        `leagueid` int NOT NULL,
			  `giai` text NOT NULL,
			  `ngay_da` date NOT NULL,
			  `thoi_gian_da` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `doi_nha` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `logo_doi_nha` text NOT NULL,
			  `doi_khach` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `logo_doi_khach` text NOT NULL,
			  `id_xem_live` text NOT NULL,
			  `link_soi_keo` text NOT NULL,
			  `hot` tinyint(1) NOT NULL
			) ";
		$results = $wpdb->query($sql);
	}

}
function lbd_Add_My_Admin_Menu() {

	add_menu_page('Lịch BĐ', 'Lịch Bóng Đá', 'manage_options', 'manager-lich-bong-da', 'lbd_Load_admin_manager', 'dashicons-awards', 100);

}

add_action('admin_menu', 'lbd_Add_My_Admin_Menu');
add_shortcode('lich-bong-da', 'load_short_code');
add_shortcode('lich-bong-da-hot', 'load_tran_hot');

register_activation_hook(__FILE__, 'check_table_exist');


function load_tran_hot(){
  require_once DC_LBD . '/views/view_load_tran_hot.php';
}


// cấu hình url riêng cho page chuyển hướng
add_action('query_vars','wpyog_add_query_vars');
function wpyog_add_query_vars( $qvars ) {
    $qvars[] = 'truc-tiep';
    return $qvars;
}

// add_action( 'init', 'add_author_rules' );
// function add_author_rules() {
// add_rewrite_rule(
// 'truc-tiep-bong-da/([a-z0-9-]+)[/]?$',DC_LBD.'/view_live.php?url=$matches[1]','top');

// add_rewrite_rule(
// "writer/([^/]+)/page/?([0-9]{1,})/?",
// "index.php?author_name=$matches[1]&paged=$matches[2]",
// "top");

// add_rewrite_rule(
// "writer/([^/]+)/(feed|rdf|rss|rss2|atom)/?",
// "index.php?author_name=$matches[1]&feed=$matches[2]",
// "top");

// add_rewrite_rule(
// "writer/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?",
// "index.php?author_name=$matches[1]&feed=$matches[2]",
// "top");
// }
function custom_link_live(&$wp) {
  if (array_key_exists('truc-tiep', $wp->query_vars)) {
  // require_once DC_LBD.'/view_live.php';
    return $wp->query_vars;
  exit;
 }

}
add_action('parse_request', 'custom_link_live');


add_shortcode('load_player', 'load_short_code_player');
function load_short_code_player(){
  require_once DC_LBD.'/view_live.php';
  // exit();
}
