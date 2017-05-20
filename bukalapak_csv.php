<?php
session_start();


sleep(1);
ini_set('max_execution_time', 0);
error_reporting(0);


//ambil nilai
require("inc/config.php");
require("inc/fungsi.php");
require("inc/koneksi.php");

	


nocache;







//nilai
$filenya = "bukalapak_csv.php";
$kunci = balikin($_REQUEST['kunci']);



header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$kunci.csv");




$qdata = mysql_query("SELECT DISTINCT(kode) AS kodenya ".
						"FROM bukalapak ".
						"WHERE lapaknya LIKE '$kunci' ".
						"ORDER BY round(jml_terjual) DESC,  ".
						"round(jml_peminat) DESC,  ".
						"round(jml_dilihat) DESC");
$rdata = mysql_fetch_assoc($qdata);
		


//isi *START
ob_start();

	
	
	

//sempurnakan 
function titelkuya44($str)
	{
    $str = trim($str);
	$search = array ("'<Berat</dt>'", "'< Merek </dt>  Lain-lain'", "'<Kondisi</dt>'", "' kilo'", "'\.'", "'\,'", "'gram'");
	$replace = array ("", "", "", "00", "", "", "");

	$str = preg_replace($search,$replace,$str);
	return $str;
  	}
	
	
	
		

echo "Kategori,Nama Barang,Stok,Berat (gram),Harga (Rupiah),Kondisi,Deskripsi,Wajib Asuransi?,URL Gambar 1,URL Gambar 2,URL Gambar 3,URL Gambar 4,URL Gambar 5\n";

do
	{
	$nomer = $nomer + 1;
	$i_kode = nosql($rdata['kodenya']);
	
	
	//detailnya
	$qku = mysql_query("SELECT * FROM bukalapak ".
							"WHERE lapaknya = '$kunci' ".
							"AND kode = '$i_kode'");
	$rku = mysql_fetch_assoc($qku);
	$i_nama = trim(titelkuya44(balikin($rku['nama'])));
	$i_deskripsi = balikin($rku['deskripsi']);
	$i_harga = balikin($rku['harga']);
	$i_berat = trim(titelkuya44(balikin($rku['berat'])));
	$i_kondisi = trim(titelkuya44(balikin($rku['kondisi'])));
	$i_kategori = trim(titelkuya44(balikin($rku['kategori'])));
	$i_jml_stock = trim(balikin($rku['jml_stock']));
	$i_jml_peminat = trim(balikin($rku['jml_peminat']));
	$i_jml_dilihat = trim(balikin($rku['jml_dilihat']));
	$i_jml_terjual = trim(balikin($rku['jml_terjual']));
	$i_postdate = $rku['postdate'];
	$i_gambar1 = trim($rku['url_gambar1']);



	//echo "$i_gambar1; $i_nama; $i_harga; $i_berat; $i_kondisi; $i_jml_stock; $i_jml_dilihat; $i_jml_peminat; $i_jml_terjual \n";


	
		

		
//	echo "\"$i_nama\", $i_jml_stock, $i_berat,  $i_harga, $i_kondisi, \"$i_deskripsi\", \"Ya\", \"$i_gambar1\", \"\", \"\", \"\", \"\", \n";

	echo "$i_kategori,$i_nama. OmahBIASAWAE,$i_jml_stock,$i_berat,$i_harga,$i_kondisi,\"$i_deskripsi\",Ya,$i_gambar1, , , , , \n";
	}
while ($rdata = mysql_fetch_assoc($qdata));



//isi
$content = ob_get_contents();
ob_end_clean();



echo $content;


exit();
?>