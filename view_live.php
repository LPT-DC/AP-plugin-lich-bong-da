  <link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet">
  <script src="https://unpkg.com/video.js/dist/video.js"></script>
  <script src="https://unpkg.com/videojs-contrib-hls/dist/videojs-contrib-hls.js"></script>

<?php
global $wpdb, $table_prefix;
date_default_timezone_set('Asia/Ho_Chi_Minh');
$show_live = FALSE;
// $ket_thuc = FALSE;
if (!isset($wpdb)) {
	require_once '../../../../wp-config.php';
	require_once '../../../../wp-includes/wp-db.php';

}
if (isset($_GET['tructiep'])) {
	$show_live = TRUE;
	$id_tran = $_GET['tructiep'];
	$data = $wpdb->get_results("SELECT * FROM `lich_bong_da` WHERE  `id`='$id_tran' ");
	$time_start = $data[0]->thoi_gian_da;

	if (count($data) > 0) {
		$show_live = TRUE;

		$url = $data[0]->id_xem_live;
		$url_live = regex_link($url);
		$url_live = "https://pullb.glive888.com/live/" . $url_live . ".m3u8";
	}

}

if (isset($_GET['link'])) {

	$url = $_GET['link'];
	$url = regex_link($url);
	$url_live = "https://pullb.glive888.com/live/" . $url . ".m3u8";
}
function regex_link($url) {
	$curl = curl_init();
// curl_setopt(CURLOPT_RETURNTRANSFER, true);
	ob_start();

	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_SSL_VERIFYPEER => true,
	));
	$resp = curl_exec($curl);

//Dữ liệu thời tiết ở dạng JSON
	// $weather = json_decode($resp);
	$html = html_entity_decode($resp);
	ob_end_clean();

	curl_close($curl);

	$pattern = '/(?:url:")(.*?)(?:")/';
	if (preg_match_all($pattern, $html, $matches)) {

		if (isset($matches[1])) {
			$matches = $matches[1];
		}
// var_dump($matches);
		$matches = $matches[0];
		$str = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
			return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
		}, $matches);

// var_dump($str);
		$re = '/\/([a-z0-9_-]*[?])/m';
		preg_match_all($re, $str, $result, PREG_SET_ORDER, 0);
// echo "====";
		// var_dump($result);
		// Print the entire match result
		$link = (str_replace("?", "", $result[0]));
		$link = $link[1];
		return $link;
	}
}
?>






	<?php if (isset($_GET['tructiep']) && isset($data[0])) {?>

    ?>

	<h1 class="h1-live"><?php echo $data[0]->doi_nha . ' vs ' . $data[0]->doi_khach . " Lúc " . date("H:i", strtotime($data[0]->thoi_gian_da)) . " Ngày " . date("d/m/Y", strtotime($data[0]->thoi_gian_da)); ?></h1>
<?php } else {
	?>
  <h1 class="h1-live">Trận đấu đã kết thúc. Vui lòng chọn trận khác để xem</h1>
<?php }?>
  <video id="my_video_1" class="video-js vjs-fluid vjs-default-skin" controls preload="auto"
  data-setup='{}'>
    <source src="<?php echo $url_live; ?>">
  </video>


<script>
var player = videojs('my_video_1');
function checkURL(x){
			var xhr = new XMLHttpRequest();
			xhr.open('GET', x, false);
			xhr.send();
			if (xhr.status != 200) {
			  console.log("Link đã die");
			} else {
			  console.log("Link còn sống");
			}
		}
		checkURL("<?php echo $url_live; ?>");
    result = player.play();
    console.log(result);
</script>
