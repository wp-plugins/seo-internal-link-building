<?php
/**
 * @package Internal_link_Building
 * @version 1.6
 */
/*
Plugin Name: Internal Link Building
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: Internal Link Building is plugin for website whose terrafic is not engaged.
Author: Muhammad Irfan
Version: 1.6
Author URI: http://Irfan.com/
*/
	add_action("admin_menu","create_seo_setting_page");
	function create_seo_setting_page(){
		add_menu_page( "SEO Internal Link Building Setting", "Internal Linking", "manage_options", "seo-internal-link", "seo_setting",plugins_url( 'Internal Link Building/images/icon.png' ) );
	}
	function seo_setting(){
		?>
			<style>
				.title {
					background: rgb(229, 136, 136);
					padding: 11px;
					color: black;
					font-size: 23px;
				}
				table{
					margin:auto;
					width:600px;
				}
				table caption{
					background: brown;
					font-size: 18px;
					padding: 2px;
					color: white;
				}
			</style>
			<h1 class="title">SEO INTERNAL LINKING SETTING</h1>
			<h3>Add Word For Linking</h3>
			<table>
				<tr>
				<?php
					if(isset($_REQUEST['update'])){
						// $id = $_REQUEST['update'];
						// global $wpdb;
						// $results = $wpdb->get_results("select * from internal_link_info where id='$id'",ARRAY_A);
						// $word = $results[0]['name'];
					}
					if(isset($_REQUEST['delete'])){
						$id = $_REQUEST['delete'];
						global $wpdb;
						$results = $wpdb->query("delete from internal_link_info where id='$id'");
					}
				?>
					<form method="post" action="">
					<td>Word which have to replace with link<br /><input style="width: 250px;" type="text" name="word" placeholder="Word here" /></td>
					<td>Link to be replace on word<br /><input style="width: 250px;"  type="text" name="link" placeholder="Link here" /></td>
					<td>Density<br /><input style="width: 50px;"  type="number" name="density" value="1" /></td>
					<td><input type="submit"  name="add_link" value="Add Internal Link" /></td>
					</form>
				</tr>
			</table>
		<?php
		
		if(isset($_REQUEST['add_link'])){
			$word = $_REQUEST['word'];
			$link = $_REQUEST['link'];
			$density = $_REQUEST['density'];
			global $wpdb;
			$query = "insert into internal_link_info values('','$word','$link','$density')";
			$wpdb->query($query);
		}
		global $wpdb;
		$results = $wpdb->get_results("select * from internal_link_info order by id desc");

		?>
			<tr>
				<?php 
					$page = (isset($_GET['pagenum']) && is_numeric($_GET['pagenum']) ? (int) $_GET['pagenum'] : 1);
					$limit = ($page - 1) * 15;
					$sql = mysql_query("select * from internal_link_info");
					$totalres = mysql_num_rows(mysql_query("select * from internal_link_info"));
					$totalpages = ceil($totalres / 15);

					for ($i = 1; $i <= $totalpages; $i++) {
						if ($i == $page)
						  $pagination .= "$i ";
						else
						  $pagination .=  "<a href='".admin_url( "admin.php?page=seo-internal-link&pagenum=$i", 'http' )."' >$i</a> ";
					}
					
?>
<table style="margin-top:50px;"><tr><td>
					<?php
						if($page == 1){
						echo "<< Previous";
					} else {
						$i=$page-1;
						echo "<a href='".admin_url( "admin.php?page=seo-internal-link&pagenum=$i", 'http' )."'>Previous</a>";
					}
					?></td><td style="text-align: right;">
				<?php
						if($page == $totalpages){
						echo "Next >>";
					} else {
						
						$i=$page+1;
						echo "<a href='".admin_url( "admin.php?page=seo-internal-link&pagenum=$i", 'http' )."'>Next</a>";
					}
					?></td></tr></table>
		<?php
					echo "<table><caption>Internal Links</caption>"; 
		foreach($results as $index=>$values){
			$low = ($page*15)-15;
			$high = ($page*15);
			if($index>=$low && $index<$high){
			$array = (array)$values;
			?>
				<tr>
					<td><?php echo $array['word']; ?></td>
					<td><?php echo $array['link']; ?></td>
					<td><?php echo $array['density']; $id = $array['id']; ?></td>
					<?php $http = admin_url( get_site_url(), 'http' ); ?>
					<td><a href="<?php echo admin_url( "admin.php?page=seo-internal-link&delete=$id", 'http' ); ?>">Delete</a></td>
				</tr>
			<?php
			}
		}
		echo "</table>";
	}
	register_activation_hook( __FILE__, 'seo_internal_link_active' );
	function seo_internal_link_active(){
		global $wpdb;
		$query = "CREATE TABLE `internal_link_info` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `word` varchar(255) NOT NULL,
			  `link` varchar(255) NOT NULL,
			  `density` int(10) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$wpdb->query($query);
	}
	function replace_word($word,$link,$density,$content){
		$array = explode($word,$content);
		if(count($array) > 1){
		foreach($array as $id=>$value){
			$num = $density;
			if($id < $num){
				$html .= $value."<a target='_blank' href='$link'>$word</a>";
			} else{
				if($id<$count-1){
					$html .= $value."$word";
				} else{ $html .= $value; }
			}
		}
		} else{ $html = $array[0]; }
		return $html;
	}
	add_Action('the_content','replace');
	function replace($content){
		global $wpdb;
		$results = $wpdb->get_results("select * from internal_link_info",ARRAY_A);
		foreach($results as $value){
			$content = replace_word($value['word'],$value['link'],$value['density'],$content);
		}
		return $content;
	}
/*  Copyright 2015  Muhammad Irfan  (email : hmsraza24@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>