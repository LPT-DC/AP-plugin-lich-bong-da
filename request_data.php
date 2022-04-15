<?php
global $wpdb;

/** Sets up WordPress vars and included files. */
require_once '../../../wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
if ($_POST['function'] == 'delete_match') {
	 $sql = "DELETE FROM `lich_bong_da` WHERE `id` = '" . $_POST['id_match'] . "'";
	$wpdb->query($sql);
} elseif ($_POST['function'] == "delete_all_match") {
	 $sql = "DELETE FROM `lich_bong_da`";
	$wpdb->query($sql);
} elseif ($_POST['function'] == "update_match") {

	 $sql = "UPDATE `lich_bong_da` SET `link_soi_keo` = '" . $_POST['link_soi_keo'] . "',
	`hot`=" . $_POST['tran_hot'] . "
	WHERE `lich_bong_da`.`id` = '" . $_POST['id_match'] . "'";
	$wpdb->query($sql);
}
?>