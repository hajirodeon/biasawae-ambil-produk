<?php
session_start();

sleep(1);
ini_set('max_execution_time', 0);
error_reporting(0);



//ambil nilai
require("inc/config.php");
require("inc/fungsi.php");
require("inc/koneksi.php");
require("inc/class/simple_html_dom.php");
$tpl = LoadTpl("template/cp_depan.html");

	


nocache;

//nilai
$filenya = "tokopedia_produk.php";
$judul = "BIASAWAE-Ambil-Produk --> TOKOPEDIA";
$judulku = $judul;
$s = nosql($_REQUEST['s']);
$kodenya = nosql($_REQUEST['kodenya']);
$kunci = balikin($_REQUEST['kunci']);
$page = nosql($_REQUEST['page']);

//jika null, anggap page 1
if (empty($page))
	{
	$page = 1;
	}


//jika page 10, lanjut
if ($page >= 10)
	{
	$ke = "$filenya?kodenya=$kodenya&s=lanjut&kunci=$kunci";
	xloc($ke);
	exit();
	}	









//isi *START
ob_start();





echo '<h3>'.$judul.'</h3>

<form action="'.$filenya.'" enctype="multipart/form-data" method="post" name="formx">';



//null-kan dahulu
if ($s == "grabdatalg")
	{
	//hapus
	mysql_query("DELETE FROM tokopedia ".
					"WHERE lapaknya = '$kunci'");


					
	//re-direct
	$ke = "$filenya?kodenya=$kodenya&s=grabdata&kunci=$kunci";
	xloc($ke);
	exit();
	}






//jika grab data lagi ///////////////////////////////////////////////////////////////////////////////////// 
if ($s == "grabdata")
	{
	echo "<h2>
	PROSES PENGAMBILAN DATA PRODUK DARI PELAPAK : $kunci, MASIH BERLANGSUNG. 
	</h2>
	<h3>
	AKAN MEMAKAN WAKTU SEKITAR 15Menit Sampai 1Jam. 
	</h3>
	<hr>";
	
	//ngambil detail ///////////////////////////////////////////////////////////////////////////////
	//jika page 1
	if ((empty($page)) OR ($page == 1))
		{
		$mulainya = "0";
		$akhirnya = "200";		
		}
	else if ($page == 2)
		{
		$mulainya = "200";
		$akhirnya = "400";		
		}
	else if ($page == 3)
		{
		$mulainya = "400";
		$akhirnya = "600";		
		}
	else if ($page == 4)
		{
		$mulainya = "600";
		$akhirnya = "800";		
		}
	else if ($page == 5)
		{
		$mulainya = "800";
		$akhirnya = "1000";		
		}
	else if ($page == 6)
		{
		$mulainya = "1000";
		$akhirnya = "1200";		
		}
	else if ($page == 7)
		{
		$mulainya = "1200";
		$akhirnya = "1400";		
		}
	else if ($page == 8)
		{
		$mulainya = "1400";
		$akhirnya = "1600";		
		}
	else if ($page == 9)
		{
		$mulainya = "1600";
		$akhirnya = "1800";		
		}
	else if ($page == 10)
		{
		$mulainya = "1800";
		$akhirnya = "2000";		
		}
	else if ($page >= 10)
		{
		//re-direct
		$ke = "$filenya?s=lanjut&kodenya=$kodenya&kunci=$kunci";
		xloc($ke);
		exit();		
		}
	
	
	
	//pengambilan data
	$base = "https://ace.tokopedia.com/search/v1/product?shop_id=$kodenya&ob=11&start=$mulainya&rows=$akhirnya&full_domain=www.tokopedia.com&scheme=https&source=shop_product";
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_URL, $base);
	curl_setopt($curl, CURLOPT_REFERER, $base);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$str = curl_exec($curl);
	curl_close($curl);
	
	
	
	
	//echo $str;
	
	
	
	// Create a DOM object
	$html_base = new simple_html_dom();
	
	// Load HTML from a string
	$grab = $html_base->load($str);
	
	
	//echo $grab;
	
	
	
	$strku = explode('"shop"', $grab);
			
	
	
	for ($j=1;$j<=500;$j++)
		{
		$nila = $strku[$j];
		
		//echo "[$j]. $nila <hr>";
	
	
	
		$strku2 = explode('"uri"', $nila);
		$baca = $strku2[2];
	
		$strku3 = explode('"', $baca);
		$baca2 = trim($strku3[1]);
		
	
		//jika gak null
		if (!empty($baca2))
			{
			echo "[$j]. $baca2 <hr>";
			
			
			//cek
			$qcc = mysql_query("SELECT * FROM tokopedia ".
									"WHERE lapaknya = '$kunci' ".
									"AND lapaknya_kode = '$kodenya' ".
									"AND urlnya = '$baca2'");
			$tcc = mysql_num_rows($qcc);
			
			//jika null
			if (empty($tcc))
				{
				//insert
				mysql_query("INSERT INTO tokopedia(lapaknya, lapaknya_kode, urlnya, postdate) VALUES ('$kunci', '$kodenya', '$baca2', '$today')");
				}
			}
	
	
		}
	
	
	
	
	
	
	//re-direct
	$targett = $page + 1;
	$ke = "$filenya?s=grabdata&kunci=$kunci&kodenya=$kodenya&page=$targett";
	?>
	<script>setTimeout("location.href='<?php echo $ke;?>'", <?php echo $jml_detik;?>);</script>
	
	<?php
	}







//pengambilan per produk //////////////////////////////////////////////////////////////////////////////////
if ($s == "lanjut")
	{

	//cek
	$qcc = mysql_query("SELECT * FROM tokopedia ".
							"WHERE lapaknya = '$kunci' ".
							"AND kategori = '' ".
							"ORDER BY RAND()");
	$rcc = mysql_fetch_assoc($qcc);
	$tcc = mysql_num_rows($qcc);
	$cc_kd = nosql($rcc['kd']);
	$cc_url_asli = $rcc['urlnya'];

	
	
	
	//yg udah masuk
	$qcc2 = mysql_query("SELECT * FROM tokopedia ".
							"WHERE lapaknya = '$kunci' ".
							"AND kategori <> ''");
	$rcc2 = mysql_fetch_assoc($qcc2);
	$tcc2 = mysql_num_rows($qcc2);
	
	
	

	//jika abis, kembali
	if (empty($tcc))
		{
		//re-direct
		$ke = "tokopedia.php?kunci=$kunci";
		xloc($ke);
		exit();			
		}
	else
		{
		echo "<p>
		Sedang mengambil data produk dari : <font color=blue>$kunci</font>.
		<br>  
		[<a href='tokopedia.php?kunci=$kunci' target='_blank'>Lihat Daftar Produk Sementara</a>].
		</p>
		[Telah berhasil masuk <font color=green>$tcc2</font> Produk. Sisa kekurangan <font color=red>$tcc</font> Produk].
		</p>
		<hr>";				



						
		
		//re-direct
		$ke = "$filenya?s=lanjut&kunci=$kunci&kodenya=$kodenya";
		?>
		<script>setTimeout("location.href='<?php echo $ke;?>'", <?php echo $jml_detik;?>);</script>
		
		<?php
		
		
		//pengambilan data
		$base1 = $cc_url_asli;
		

		//ubah ke mobile
		function titelmobi($str)
			{
		    $str = trim($str);
			$search = array ("'www'");
			$replace = array ("m");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		
		
		$base = titelmobi($base1);
		
		
		
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_URL, $base);
		curl_setopt($curl, CURLOPT_REFERER, $base);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		$str = curl_exec($curl);
		curl_close($curl);
		
		
		
		// Create a DOM object
		$html_base = new simple_html_dom();
		
		// Load HTML from a string
		$grab = $html_base->load($str);
		
		
		
		
		//kategori........
//		$start = "'category': '";
//		$end   = "','key':";
		
		$start = "category_name_full";
		$end   = "cc_url";
		
				
		$startPosisition = strpos($grab, $start);
		$endPosisition   = strpos($grab, $end); 
		
		$longText = $endPosisition - $startPosisition;
		
		$kategorixx = substr($grab, $startPosisition, $longText);
		
		
		
		$strkuu = explode('"', $kategorixx);
		$kategorix1 = $strkuu[2];
		//$kategorix2 = $strkuu[7];
		
		
		
		
		
		
		//sempurnakan 
		function titelkuya57($str)
			{
		    $str = trim($str);
			$search = array ("'\"'", "'\''");
			$replace = array ("", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		
		$kategori = titelkuya57($kategorix1);
		//$kategori2 = titelkuya57($kategorix2);
		
		echo "Kategori : 
		<br>
		$kategori  
		<hr>";
		
		
		
		
		
		

		
		
		//isinya........
		$start = "<html>";
		$end   = "</html>";
		
		
		$startPosisition = strpos($grab, $start);
		$endPosisition   = strpos($grab, $end); 
		
		$longText = $endPosisition - $startPosisition;
		
		$result = substr($grab, $startPosisition, $longText);
		
		
		//echo $result;
		
		
		
		
		
		//judul
		$start = "receiver-subject=\"";
		$end   = "\"><div ng-cloak class=\"";
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		
		$result_judul = substr($result, $startPosisition, $longText);
		
		
		//sempurnakan 
		function titelkuyaa23($str)
			{
		    $str = trim($str);
			$search = array ("'receiver-subject=\"'");
			$replace = array ("");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		$result_judulx = trim(titelkuyaa23($result_judul));
		
		
		
		
		
		echo "<table border=\"0\">
		<tr valign=\"top\">
		<td width=\"250\">";
		
		
		//img url
		$start = "<div class=\"swiper-slide single-image__slide\">";
		$end   = "</div></div><div class=\"swiper-pagination\">";
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		
		$result_img = substr($result, $startPosisition, $longText);
		
		preg_match('/(<img[^>]+>)/i', $result_img, $i_gambare); 
		
		
		$i_gambarkux = $i_gambare[0];
		
		
		
		//preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $i_gambarkux, $x_result);
		//$i_gambarkux = array_pop($x_result);
		
		
		
		//sempurnakan 
		function titelkuya61($str)
			{
		    $str = trim($str);
			$search = array ("'<img itemprop=\"image\" class=\"invisible\" src=\''");
			$replace = array ("");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		
		
		
		
		
		$i_gambarku2 = titelkuya61($i_gambarkux);
		
	
		$strkuu = explode('\'', $i_gambarku2);
		$i_gambarku = $strkuu[0];
		
		


		
		
		echo "<img src=\"$i_gambarku\" width=\"200\">
		</td>
		
		<td>";
		
		
		
		
		
		
		
				

		
		echo "Judul : 
		<br>
		$result_judulx
		<hr>";
		
		
		
		
		
		
		
		
		//berat
		$start = "<div class=\"uppercase muted fs-12\"> Berat </div>";
		$end   = "<div class=\"uppercase muted fs-12\">Kondisi";
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		
		$result_berat = substr($result, $startPosisition, $longText);
		
		
		
		//sempurnakan stock
		function titelkuya4($str)
			{
		    $str = trim($str);
			$search = array ("'<div class=\"uppercase muted fs-12\"> Berat </div>'", "'<div class=\"font-black fw-600\">'", "',00 gr </div>'", "'</div>'", "'<div class=\"mt-15\">'");
			$replace = array ("", "", "", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		
		
		$result_beratx = trim(titelkuya4($result_berat));
		
		echo "Berat : 
		<br>
		$result_beratx
		<br>
		<br>";
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//harga
		$start = "id=\"product_price_int\" value=\"";
		$end   = "\"><input type=\"hidden\" id=\"url_login\"";
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		
		$result_harga = substr($result, $startPosisition, $longText);
		
		
		//sempurnakan 
		function titelkuya54($str)
			{
		    $str = trim($str);
			$search = array ("'id=\"product_price_int\" value=\"'");
			$replace = array ("");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		
		
		$result_hargax = trim(titelkuya54($result_harga));
		
		
		
		echo "harga : 
		<br>
		$result_hargax
		<hr>";
		
		

		//isi
		$start = "<p itemprop=\"description\">";
		$end   = "<div class=\"text-center fw-600\">";
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		
		$result_isi = substr($result, $startPosisition, $longText);
		
		
		//sempurnakan 
		function titelkuya44($str)
			{
		    $str = trim($str);
			$search = array ("'<p itemprop=\"description\">'", "'<div class=\"absolute\">'", "'</div>'");
			$replace = array ("", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		
		
		$result_isix = trim(titelkuya44($result_isi));
		
		echo "$result_isix";
		






		//cari yg tidak dijual lagi..
		
		
		$start = "<div class=\"alert alert-block\">";
		$end   = "<b>Stok produk kosong.</b>";
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		
		$result_isiya = substr($result, $startPosisition, $longText);
		
		
		//sempurnakan 
		function titelkuya47($str)
			{
		    $str = trim($str);
			$search = array ("'<p itemprop=\"description\">'", "'<div class=\"absolute\">'", "'</div>'");
			$replace = array ("", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		
		
		
		$result_kosongnya = trim(titelkuya47($result_isiya));
		
		//echo "[kosong -> $result_kosongnya]";

		
		//jika ada kosong
		if (!empty($result_kosongnya))
			{
			$jml_dilihat = 0;
			}
		
		
		
		
		
		//update
		mysql_query("UPDATE tokopedia SET nama = '$result_judulx', ".
						"kategori = '$kategori', ".
						"harga = '$result_hargax', ".
						"isi = '$result_isix', ".
						"img_url = '$i_gambarku', ".
						"jml_dilihat = '$jml_dilihat', ".
						"berat = '$result_beratx', ".
						"postdate = '$today' ".
						"WHERE lapaknya = '$kunci' ".
						"AND urlnya = '$cc_url_asli'");
		
		
		
		echo "</td>
		</tr>
		</table>";

		}				
			
	}







//jika daftar barang
if (empty($s))
	{
	if (!empty($tku))
		{
		//re-direct
		$ke = "tokopedia.php?kunci=$kunci";
		xloc($ke);
		exit();
		}
	}











//isi
$isi = ob_get_contents();
ob_end_clean();

require("inc/niltpl.php");



exit();
?>