<?php

global $wpdb, $table_prefix;

if (!isset($wpdb)) {
	require_once '../../../../wp-config.php';
	require_once '../../../../wp-includes/wp-db.php';

}

$leagueid = $atts['leagueid'];
if (($leagueid != "all")) {
	$sql = '';
	$arr_leagueid = explode(",", $leagueid);
	foreach ($arr_leagueid as $v => $k) {
		if ($v === array_key_last($arr_leagueid)) {
			$sql .= "'" . $k . "'";

		} else {
			$sql .= "'" . $k . "',";

		}
	}
// echo "SELECT * FROM `lich_bong_da` WHERE  (`thoi_gian_da`) >= (now()) and `leagueid` IN ($sql) ORDER BY `hot` DESC, `thoi_gian_da` ASC limit 10";
	$data = $wpdb->get_results("SELECT * FROM `lich_bong_da` WHERE  date(`thoi_gian_da`) = date(now()) and `leagueid` IN ($sql) ORDER BY `hot` DESC, `thoi_gian_da` ASC");

} else if ($leagueid == "all") {
	$data = $wpdb->get_results("SELECT * FROM `lich_bong_da` WHERE  date(`thoi_gian_da`) = date(now()) GROUP by `leagueid`,`lich_bong_da`.`id` ORDER BY `hot` DESC, `thoi_gian_da` ASC");
}

?>
<?php if (isset($data[0]->giai)) {}?>
<?php

$current_ten_giai = '';
$result = array();
$key = "leagueid";

foreach ($data as $val) {
	if (array_key_exists($key, $val)) {

		$result[$val->$key][] = $val;
	}

}

foreach ($result as $rs) {

	foreach ($rs as $k => $v) {
		if ($current_ten_giai != $v->giai) {
			$current_ten_giai = $v->giai;
			$tmp_ten_giai = TRUE;
		} else {
			$tmp_ten_giai = FALSE;
		}
		// echo $v;

		?>


<?php if ($tmp_ten_giai) {?>
<p style="font-weight: 700;text-align: center; " class="header-pc"><img
		src="https://dev.xemlivenhanh.com/wp-content/uploads/2022/01/image-86.png">
	<?php	echo $current_ten_giai; ?>
</p>
<?php }?>

<?php
// $date = date("d/m", strtotime($v->thoi_gian_da));
		$hour = date("H:i", strtotime($v->thoi_gian_da));
		?>
<section class="sec header-pc">
	<div class="shortcode">
		<p class="doi1"><?php echo $v->doi_nha; ?></p>
		<p class="gio"> <?php echo $hour; ?>
		</p>
		<!-- <p class="gio"> <?php echo $date; ?> -->
		</p>
		<p class="doi2"><?php echo $v->doi_khach; ?></p>

		<button class="button xemngay"><a target="_blank" rel="noreferrer noopener nofollow" href="/chuyen-huong/?redirect=https://xemlivenhanh.com/live?tructiep=<?php echo $v->id; ?>">Xem ngay</a></button>
		<button class="button datcuoc"><a class="bonus_slider" href="#">Đặt cược</a></button>
	</div>
</section>

<?php }}?>




<div class="sec-container header-mobile">
	<?php foreach ($result as $rs) {
	foreach ($rs as $k => $v) {
		$hour = date("H:i", strtotime($v->thoi_gian_da));
		?>
	<section class="sec-hot">
		<a rel="noreferrer noopener nofollow" target="_blank" href="/chuyen-huong/?redirect=https://xemlivenhanh.com/live?tructiep=<?php echo $v->id; ?>">
			<div class="shortcode-hot"><a rel="noreferrer noopener nofollow" href="https://xemlivenhanh.com/live?tructiep=<?php echo $v->id; ?>">
					<p class="cup"> <?php echo str_replace("UEFA Europa Conference League", "UEFA", $v->giai); ?> </p>
					<div class="flex">
						<div class="column">
							<img class="logo_doi1" src="<?php echo $v->logo_doi_nha; ?>">
							<p class="doi_1"><?php echo $v->doi_nha; ?> </p>
						</div>
						<p class="gio"><?php echo $hour; ?></p>
						<div class="column">
							<img class="logo_doi2" src="<?php echo $v->logo_doi_khach; ?>">
							<p class="doi_2"> <?php echo $v->doi_khach; ?></p>
						</div>
					</div>
			</div>
		</a></section>


	<?php }}?>
</div>