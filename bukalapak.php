<?php
session_start();


ini_set('max_execution_time', 0);



//ambil nilai
require("inc/config.php");
require("inc/fungsi.php");
require("inc/koneksi.php");
require("inc/class/paging.php");
require("inc/class/simple_html_dom.php");
$tpl = LoadTpl("template/cp_depan.html");

	


nocache;

//nilai
$filenya = "bukalapak.php";
$judul = "BIASAWAE-AMBIL-PRODUK --> BUKALAPAK";
$judulku = $judul;
$kunci = balikin($_REQUEST['kunci']);
$s = nosql($_REQUEST['s']);
$page = nosql($_REQUEST['page']);
if ((empty($page)) OR ($page == "0"))
	{
	$page = "1";
	}


$limit = "20";


//batal
if ($_POST['btnBTL'])
	{
	//re-direct
	xloc($filenya);
	exit();
	}












//isi *START
ob_start();






echo '<form action="'.$filenya.'" enctype="multipart/form-data" method="post" name="formx">


<h1>'.$judul.'</h1>
<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr bgcolor="red">
<td align="left">
http://bukalapak.com/
<input name="kunci" type="text" value="'.$kunci.'" size="30">
<br>
<input name="btnCARI" type="submit" value="CARI USERNAME PELAPAK">
</td>
</tr>
</table>
</form>';





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
		['.$kunci.']. [Postdate : '.$ku_postdate.']. 
		<br>
		[<a href="bukalapak_produk.php?s=grabdatalg&kunci='.$kunci.'">AMBIL DATA LAGI</a>].
		<br>'; 



		
		//query
		$p = new Pager();
		$start = $p->findStart($limit);
		
		
		$kuerine = "SELECT DISTINCT(kode) AS kodenya ".
					"FROM bukalapak ".
					"WHERE nama <> '' ".
					"AND lapaknya LIKE '$kunci' ".
					"ORDER BY round(jml_terjual) DESC, ".
					"round(jml_peminat) DESC, ".
					"round(jml_dilihat) DESC";
		
		$sqlcount = $kuerine;
		$sqlresult = $sqlcount;
		
		$count = mysql_num_rows(mysql_query($sqlcount));
		$pages = $p->findPages($count, $limit);
		$result = mysql_query("$sqlresult LIMIT ".$start.", ".$limit);
		$target = "$filenya?kunci=$kunci";
		$pagelist = $p->pageList($_GET['page'], $pages, $target);
		$data = mysql_fetch_array($result);
		
		
		
		echo '<h3>
		DATA PRODUK PELAPAK : '.$kunci.'
		</h3>
		[<a href="bukalapak_csv.php?kunci='.$kunci.'">FILE CSV</a>]. 
		[<a href="bukalapak_csv2.php?kunci='.$kunci.'">FILE CSV DENGAN HARGA + 10%</a>].
		[<a href="bukalapak_csv3.php?kunci='.$kunci.'">FILE CSV DENGAN HARGA + 20%</a>].
		[<a href="bukalapak_csv4.php?kunci='.$kunci.'">FILE CSV DENGAN HARGA + 30%</a>]. 
		
		<br>

		<table width="1200" border="1" cellspacing="0" cellpadding="3">
		<tr bgcolor="red">
		<td><strong><font color="'.$warnatext.'">GAMBAR</font></strong></td>
		<td><strong><font color="'.$warnatext.'">NAMA</font></strong></td>
		<td width="50"><strong><font color="'.$warnatext.'">HARGA</font></strong></td>
		<td width="50"><strong><font color="'.$warnatext.'">BERAT</font></strong></td>
		<td width="50"><strong><font color="'.$warnatext.'">KONDISI</font></strong></td>
		<td width="50"><strong><font color="'.$warnatext.'">KATEGORI</font></strong></td>
		<td width="50"><strong><font color="'.$warnatext.'">JML. STOCK</font></strong></td>
		<td width="50"><strong><font color="'.$warnatext.'">JML. DILIHAT</font></strong></td>
		<td width="50"><strong><font color="'.$warnatext.'">JML. PEMINAT</font></strong></td>
		<td width="50"><strong><font color="'.$warnatext.'">JML. TERJUAL</font></strong></td>
		<td width="50"><strong><font color="'.$warnatext.'">POSTDATE</font></strong></td>
		</tr>';
		
		do
			{
			if ($warna_set ==0)
				{
				$warna = $warna01;
				$warna_set = 1;
				}
			else
				{
				$warna = $warna02;
				$warna_set = 0;
				}
		
			$nomer = $nomer + 1;
			$i_kode = nosql($data['kodenya']);
			
			
			//detailnya
			$qku = mysql_query("SELECT * FROM bukalapak ".
									"WHERE lapaknya = '$kunci' ".
									"AND kode = '$i_kode'");
			$rku = mysql_fetch_assoc($qku);
			$i_nama = balikin($rku['nama']);
			$i_harga = balikin($rku['harga']);
			
			$i_beratx = balikin($rku['berat']);
			
			$pecahku = explode("<", $i_beratx);
			$i_berat = $pecahku[0];
			
			
			$i_kondisi = balikin($rku['kondisi']);
			$i_kategori = balikin($rku['kategori']);
			$i_jml_stock = balikin($rku['jml_stock']);
			$i_jml_peminat = balikin($rku['jml_peminat']);
			$i_jml_dilihat = balikin($rku['jml_dilihat']);
			$i_jml_terjual = balikin($rku['jml_terjual']);
			$i_postdate = $rku['postdate'];
			$i_gambar1 = $rku['url_gambar1'];
		
		
		
			echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
			echo '<td>
			<img src="'.$i_gambar1.'" width="100">
			</td>
			
			<td>'.$i_nama.'</td>
			<td>'.$i_harga.'</td>
			<td>'.$i_berat.'</td>
			<td>'.$i_kondisi.'</td>
			<td>'.$i_kategori.'</td>
			<td>'.$i_jml_stock.'</td>
			<td>'.$i_jml_dilihat.'</td>
			<td>'.$i_jml_peminat.'</td>
			<td>'.$i_jml_terjual.'</td>
			<td>'.$i_postdate.'</td>
		    </tr>';
			}
		while ($data = mysql_fetch_assoc($result));
		
		echo '</table>
		<table width="1000" border="0" cellspacing="0" cellpadding="3">
		<tr>
		<td>
		Total : <strong><font color="#FF0000">'.$count.'</font></strong> Data. 
		'.$pagelist.'
		</td>
		</tr>
		</table>
		<hr>
		
		
		<br>
		<br>';
		}
	
	}




//isi
$isi = ob_get_contents();
ob_end_clean();

require("inc/niltpl.php");


exit();
?>