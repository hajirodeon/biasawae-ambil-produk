<?php
session_start();


ini_set('max_execution_time', 0);



//ambil nilai
require("inc/config.php");
require("inc/fungsi.php");
require("inc/koneksi.php");
require("inc/class/simple_html_dom.php");
$tpl = LoadTpl("template/cp_depan.html");


	


nocache;

//nilai
$filenya = "bukalapak_produk.php";
$judul = "Proses Ambil Produk --> BUKALAPAK";
$judulku = $judul;
$s = nosql($_REQUEST['s']);
$kunci = balikin($_REQUEST['kunci']);
$page = nosql($_REQUEST['page']);

//jika null, anggap page 1
if (empty($page))
	{
	$page = 1;
	}


//jika page 50, lanjut
if ($page >= 50)
	{
	$ke = "$filenya?s=lanjut&kunci=$kunci";
	xloc($ke);
	exit();
	}	





//cari
if ($_POST['btnCARI'])
	{
	//nilai
	$kunci = balikin($_POST['kunci']);


	//cek
	if (empty($kunci))
		{
		//re-direct
		$pesan = "Input Pencarian Tidak Lengkap. Harap diperhatikan...!!";
		pekem($pesan,$filenya);
		exit();
		}
	else
		{
		//re-direct
		$ke = "$filenya?kunci=$kunci";
		xloc($ke);
		exit();
		}
	}









//isi *START
ob_start();





echo '<h3>'.$judul.'</h3>';





//cari
if ($_POST['btnCARI'])
	{
	//nilai
	$kunci = balikin($_POST['kunci']);


	//cek
	if (empty($kunci))
		{
		//re-direct
		$pesan = "Input Pencarian Tidak Lengkap. Harap diperhatikan...!!";
		pekem($pesan,$filenya);
		exit();
		}
	else
		{
		//re-direct
		$ke = "$filenya?kunci=$kunci";
		xloc($ke);
		exit();
		}
	}





//cek lapak
if (!empty($kunci))
	{
	//cek lapak
	$ccx_ke = "https://m.bukalapak.com/$kunci";


	//base url
	$base = $ccx_ke;


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

	
	
	//isinya........
	$start = "<html>";
	$end   = "</html>";
	
	
	$startPosisition = strpos($grab, $start);
	$endPosisition   = strpos($grab, $end); 
	
	$longText = $endPosisition - $startPosisition;
	
	$result = substr($grab, $startPosisition, $longText);
	
	

	//ambil cek
	$start = "<meta property=\"og:title\" content=\"";
	$end   = "<meta property=\"og:description\"";
	
	
	$startPosisition = strpos($result, $start);
	$endPosisition   = strpos($result, $end); 
	
	$longText = $endPosisition - $startPosisition;
	$result_judulx = substr($result, $startPosisition, $longText);
	 
	//sempurnakan title
	function titelkuyaa($str)
		{
	    $str = trim($str);
		$search = array ("'\" />'", "'<meta property=\"og:title\" content=\"'");
		$replace = array ("", "");
	
		$str = preg_replace($search,$replace,$str);
		return $str;
	  	}
	
	 
	$result_cek = cegah2(titelkuyaa($result_judulx));
	
	
	//balikin
	$kuncii = balikin($kunci);
	
	
	//jika gak ada itu
	if ($result_cek == "Situs Jual Beli Online Mudah Dan Terpercaya")
		{
		echo "Maaf, Tidak Ditemukan Pelapak dengan nama : $kuncii";
		}
	else
		{
		//baca database
		$qku = mysql_query("SELECT * FROM bukalapak ".
								"WHERE lapaknya = '$kunci' ".
								"ORDER BY postdate DESC");
		$rku = mysql_fetch_assoc($qku);
		$tku = mysql_num_rows($qku);
		
		$ku_postdate = $rku['postdate'];
		
		echo '<form action="'.$filenya.'" method="post" name="formx">

		<hr>
		['.$kunci.']. [Postdate :'.$ku_postdate.'].
		<br> 
		[<a href="'.$filenya.'?s=grabdatalg&kunci='.$kunci.'">AMBIL PRODUK LAGI</a>].'; 
		}


	}







//null-kan dahulu
if ($s == "grabdatalg")
	{
	//hapus
	mysql_query("DELETE FROM bukalapak ".
					"WHERE lapaknya = '$kunci'");
					
	//re-direct
	$ke = "$filenya?s=grabdata&kunci=$kunci";
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
	$ccx_ke = "https://m.bukalapak.com/$kunci?page=$page";


	//base url
	$base = $ccx_ke;


	

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
	$html_base->load($str);

	// Load HTML from a string
	$grab = $html_base->load($str);

	//echo $grab;
	

	//echo $grab;

	

	//get all category links
	foreach($html_base->find('a') as $element) {
		
		$nilku = $element->href;
		$url_asli = "https://m.bukalapak.com$nilku";

		$nilkux = substr($nilku,0,3);

			
		

		if ($nilkux == "/p/")
			{
			echo "$url_asli <br>";

			//insert
			mysql_query("INSERT INTO bukalapak(lapaknya, url_asli, postdate) VALUES ".
							"('$kunci', '$url_asli', '$today')");
			}



		}
	
	$html_base->clear(); 
	unset($html_base);
	




	//lanjutkan ke halaman berikutnya
	$page = $page + 1;
	
	$ke = "$filenya?s=grabdata&kunci=$kunci&page=$page";
	?>
	<script>setTimeout("location.href='<?php echo $ke;?>'", <?php echo $jml_detik;?>);</script>
	
	<?php
	}







//pengambilan per produk //////////////////////////////////////////////////////////////////////////////////
if ($s == "lanjut")
	{

	//cek
	$qcc = mysql_query("SELECT * FROM bukalapak ".
							"WHERE lapaknya = '$kunci' ".
							"AND kategori = '' ".
							"ORDER BY RAND()");
	$rcc = mysql_fetch_assoc($qcc);
	$tcc = mysql_num_rows($qcc);
	$cc_mpkd = nosql($rcc['kd']);
	$cc_url_asli = $rcc['url_asli'];

	
	

	//jika abis, kembali
	if (empty($tcc))
		{
		//re-direct
		$ke = "bukalapak_produk.php?kunci=$kunci";
		xloc($ke);
		exit();			
		}
	else
		{
		//pengambilan data
		$base = $cc_url_asli;
		
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

		
		
		//isinya........
		$start = "<html>";
		$end   = "</html>";
		
		
		$startPosisition = strpos($grab, $start);
		$endPosisition   = strpos($grab, $end); 
		
		$longText = $endPosisition - $startPosisition;
		
		$result = substr($grab, $startPosisition, $longText);
		

		



		//ambil judulnya
		$start = "Product\",\"name\":\"";
		//$end   = "\",\"image\":\"https:";
		$end   = "\",\"description";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_judulx = substr($result, $startPosisition, $longText);
		 
		//sempurnakan title
		function titelkuya($str)
			{
		    $str = trim($str);
			$search = array ("'Product\",\"name\":\"'");
			$replace = array ("");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		 
		$result_judul = cegah2(titelkuya($result_judulx));

/*
		echo "<h1>
		$result_judul
		</h1>";
*/
		
		
		
		

		//harga
		$start = "<meta property=\"product:price:amount\" content=\"";
		$end   = "<meta property=\"product:price:currency\" content=\"IDR\" />";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_hargax = substr($result, $startPosisition, $longText);

 
		//sempurnakan harga
		function titelkuya2($str)
			{
		    $str = trim($str);
			$search = array ("'<meta property=\"product:price:amount\" content=\"'", "'\" />'");
			$replace = array ("", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
		
		$result_harga = titelkuya2($result_hargax);







		





		//kode
		$start = "ecomm_prodid: ";
		$end   = "ecomm_pagetype";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_kodex = substr($result, $startPosisition, $longText);

		$kodenya = explode('"', $result_kodex);
	
		$result_kode = $kodenya[1];




	
		//stock
		$start = "<span class='detail-stat__title'>STOK</span>";
		$end   = "<div class='product-statistics__sold'>";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_stockx = substr($result, $startPosisition, $longText);
	
		//sempurnakan stock
		function titelkuya4($str)
			{
		    $str = trim($str);
			$search = array ("'</div>'", "'</span>'", "'<span class=\'detail-stat__val\'>'", "'<span class=\'detail-stat__title\'>STOK'", "'<span class=\'detail-stat__val js-variant-detail-element js-variant-stock-\'>'");
			$replace = array ("", "", "", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
	
		$result_stock = titelkuya4($result_stockx);


		

		




		
		
		//deskripsi
		$start = "<h3 class='header__title'>Deskripsi</h3>";
		$end   = "<span id='product-term'></span>";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_desk1 = substr($result, $startPosisition, $longText);

		

		//sempurnakan 
		function titelkuya31($str)
			{
		    $str = trim($str);
			$search = array ("'<h3 class=\'header__title\'>Deskripsi</h3>'", "'</div>'");
			$replace = array ("", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}


		$result_deskripsi = titelkuya31($result_desk1);
		$result_deskripsii = cegah2($result_deskripsi);
		



		
		//gambar
		$start = "<meta property=\"og:image\"";
		$end   = "<meta property=\"og:description\"";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_gambarx = substr($result, $startPosisition, $longText);


		//sempurnakan 
		function titelkuya6($str)
			{
		    $str = trim($str);
			$search = array ("'content=\"'", "'\" />'", "'<meta property=\"og:image\"'");
			$replace = array ("", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
	
		$result_gambar = titelkuya6($result_gambarx);

		
		
		
		
		$strkuu = explode('\'', $result_gambar);
		$i_gambarku = $strkuu[0];
		
		






		
		//terjual
		$start = "<span class='detail-stat__title'>TERJUAL</span>";
		$end   = "<div class='product-statistics__interest'>";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_terjualx = substr($result, $startPosisition, $longText);
	
		//sempurnakan 
		function titelkuya41($str)
			{
		    $str = trim($str);
			$search = array ("'</div>'", "'</span>'", "'<span class=\'detail-stat__val\'>'", "'<span class=\'detail-stat__title\'>TERJUAL'");
			$replace = array ("", "", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
	
		$result_terjual = titelkuya41($result_terjualx);







		//peminat
		$start = "<span class='detail-stat__title'>PEMINAT</span>";
		$end   = "<div class='product-statistics__viewed'>";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_peminatx = substr($result, $startPosisition, $longText);
	
		//sempurnakan 
		function titelkuya42($str)
			{
		    $str = trim($str);
			$search = array ("'</div>'", "'</span>'", "'<span class=\'detail-stat__val\'>'", "'<span class=\'detail-stat__title\'>PEMINAT'");
			$replace = array ("", "", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
	
		$result_peminat = titelkuya42($result_peminatx);






		//jml dilihat
		$start = "<span class='detail-stat__title'>DILIHAT</span>";
		$end   = "<div class='product-detailed-actions' id='product-detailed-view'>";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_dilihatx = substr($result, $startPosisition, $longText);
	
		//sempurnakan 
		function titelkuya43($str)
			{
		    $str = trim($str);
			$search = array ("'</div>'", "'</span>'", "'<span class=\'detail-stat__val\'>'", "'<span class=\'detail-stat__title\'>DILIHAT'", "'<span class=\'detail-stat__val js-product-seen-value\'>'");
			$replace = array ("", "", "", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
	
		$result_dilihat = titelkuya43($result_dilihatx);

		//echo "<p>$result_dilihat</p>";






		//berat
		$start = "<dt class='kvp__key'>Berat</dt>";
		$end   = "<span id='product-desc'></span>";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_beratx = substr($result, $startPosisition, $longText);
	
		//sempurnakan 
		function titelkuya44($str)
			{
		    $str = trim($str);
			$search = array ("'<dd class=\'kvp__value qa-product-detail-weight\'>'", "'<dd class=\'kvp__value\'>'", "'dt class=\'kvp__key\'>'", "'</dl>'", "'</dd>'", "'</div>'", "'gram'", "'<Berat</dt>'", "'<dd class=\'kvp__value qa-pd-weight\'>'");
			$replace = array ("", "", "", "", "", "", "", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
	
		$result_berat = titelkuya44($result_beratx);

		
		




		//kondisi
		$start = "<dt class='kvp__key'>Kondisi</dt>";
		$end   = "<dt class='kvp__key'>Berat</dt>";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_kondisix = substr($result, $startPosisition, $longText);
	
		//sempurnakan 
		function titelkuya45($str)
			{
		    $str = trim($str);
			$search = array ("'<dd class=\'kvp__value qa-product-detail-condition\'>'", "'<dd class=\'kvp__value\'>'", "'dt class=\'kvp__key\'>'", "'</dl>'", "'</dd>'", "'</div>'", "'<span class=\"product__condition product__condition--new\">'", "'</span>'", "'<Kondisi</dt>'", "'<dd class=\'kvp__value qa-pd-condition\'>'");
			$replace = array ("", "", "", "", "", "", "", "", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
	
		$result_kondisi = titelkuya45($result_kondisix);

		










		//kategori
		$start = "<dt class='kvp__key'>Kategori</dt>";
		$end   = "<dt class='kvp__key'>Kondisi</dt>";
		
		
		$startPosisition = strpos($result, $start);
		$endPosisition   = strpos($result, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result_kategorix = substr($result, $startPosisition, $longText);
	
		//sempurnakan 
		function titelkuya451($str)
			{
		    $str = trim($str);
			$search = array ("'<dd class=\'kvp__value qa-product-detail-category\' itemprop=\'category\'>'", "'<dd class=\'kvp__value\'>'", "'dt class=\'kvp__key\'>'", "'</dl>'", "'</dd>'", "'</div>'", "'<span class=\"product__condition product__condition--new\">'", "'</span>'", "'<category</dt>'", "'<dd class=\'kvp__value\' itemprop=\'category\'>'", "'<Kategori</dt>'", "'<dd class=\'kvp__value qa-pd-category\'>'");
			$replace = array ("", "", "", "", "", "", "", "", "", "", "", "");
		
			$str = preg_replace($search,$replace,$str);
			return $str;
		  	}
		
	
		$result_kategori = titelkuya451($result_kategorix);

		





		

		//update
		mysql_query("UPDATE bukalapak SET kode = '$result_kode', ".
						"nama = '$result_judul', ".
						"harga = '$result_harga', ".
						"berat = '$result_berat', ".
						"kondisi = '$result_kondisi', ".
						"kategori = '$result_kategori', ".
						"jml_stock = '$result_stock', ".
						"jml_terjual = '$result_terjual', ".
						"jml_peminat = '$result_peminat', ".
						"jml_dilihat = '$result_dilihat', ".
						"url_gambar1 = '$result_gambar', ".						
						"deskripsi = '$result_deskripsii' ".
						"WHERE kd = '$cc_mpkd'");



							
				
		$html_base->clear(); 
		unset($html_base);

		
		
		
		
		//yg udah masuk
		$qcc2 = mysql_query("SELECT * FROM bukalapak ".
								"WHERE lapaknya = '$kunci' ".
								"AND kategori <> ''");
		$rcc2 = mysql_fetch_assoc($qcc2);
		$tcc2 = mysql_num_rows($qcc2);
		

				
		
		echo "<p>
		[<a href='bukalapak.php?kunci=$kunci' target='_blank'>Lihat Daftar Produk Sementara</a>].
		</p>
		[Telah berhasil masuk <font color=green>$tcc2</font> Produk. Sisa kekurangan <font color=red>$tcc</font> Produk].
		</p>
		<hr>";				
		
		
		echo "<h3>
		Sedang mengambil data : 
		<br>
		<font color=red>$cc_url_asli</font>
		</h3>
		<br>";
		
				
		echo "<table border=\"0\">
		<tr valign=\"top\">
		<td width=\"250\">

		<img src=\"$result_gambar\" width=\"200\">
		</td>
		
		<td>

		[Harga : <b>Rp.$result_harga,-</b>].
		<br> 
		[Jumlah Stock : <b>$result_stock</b>].
		<br>
		[Jumlah Terjual : <b>$result_terjual</b>].
		<br>
		[Jumlah Peminat : <b>$result_peminat</b>]. 
		<br>
		[Jumlah Dilihat: <b>$result_dilihat</b>]. 
		<hr>
		[Berat : <b>$result_berat</b>]. 
		<hr>
		[Kondisi : <b>$result_kondisi</b>]. 
		<hr>
		[Kategori : <b>$result_kategori</b>]. 
		<hr>
		
		Tunggu sejenak, masih berlangsung lanjut GRAB DATA, sampai semua Produk dari BUKALAPAK masuk semua.";				


		
				
		
		//re-direct
		$ke = "$filenya?s=lanjut&kunci=$kunci";
		?>
		<script>setTimeout("location.href='<?php echo $ke;?>'", <?php echo $jml_detik;?>);</script>
		
		<?php
		}				
			
	}







//jika daftar barang
if (empty($s))
	{
	if (!empty($tku))
		{
		//re-direct
		$ke = "bukalapak.php?kunci=$kunci";
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