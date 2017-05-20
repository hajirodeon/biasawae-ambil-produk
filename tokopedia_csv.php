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
$kunci = balikin($_REQUEST['kunci']);
$filenya = "tokopedia_csv.php";



header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$kunci-asliya.csv");




$qdata = mysql_query("SELECT * FROM tokopedia ".
						"WHERE kategori <> '' ".
						"AND lapaknya = '$kunci' ".
						"ORDER BY kategori ASC, ".
						"nama ASC");
$rdata = mysql_fetch_assoc($qdata);
		


//isi *START
ob_start();

	
	

//sempurnakan 
function titelkuya44($str)
	{
    $str = trim($str);
	$search = array ("'<Berat</dt>'", "'< Merek </dt>  Lain-lain'", "'<Kondisi</dt>'", "' kilo'", "'\.'", "'\,'", "'gram'", "' gr'");
	$replace = array ("", "", "", "00", "", "", "", "");

	$str = preg_replace($search,$replace,$str);
	return $str;
  	}
	
		
		
echo "Kategori,Nama Barang,Stok(Minimal 1),Berat (gram),Harga (Rupiah),Kondisi(Baru/Bekas),Deskripsi,Wajib Asuransi?(Ya/Tidak),URL Gambar 1,URL Gambar 2,URL Gambar 3,URL Gambar 4,URL Gambar 5\n";


do
	{
	$nomer = $nomer + 1;
	$i_kd = nosql($rdata['kd']);
	
	
	$i_nama = trim(titelkuya44(balikin($rdata['nama'])));
	$i_deskripsi = balikin($rdata['isi']);
	$i_kategori = balikin($rdata['kategori']);
	$i_harga1 = balikin($rdata['harga']);
	$i_berat = trim(titelkuya44(balikin($rdata['berat'])));
	$i_kondisi = "Baru";
	$i_jml_stock = "5";


	$i_gambar1 = trim($rdata['img_url']);



	//echo "$i_gambar1; $i_nama; $i_harga; $i_berat; $i_kondisi; $i_jml_stock; $i_jml_dilihat; $i_jml_peminat; $i_jml_terjual \n";


	
	//asli
	$i_harga22 = $i_harga1;

	//cek
	$i_harga2i = substr($i_harga22,-2);

	//selisihnya
	$i_hargku = 100 - $i_harga2i;
	
	$i_harga2 = $i_harga22 + $i_hargku;

	echo "$i_kategori,$i_nama. BIASAWAE,$i_jml_stock,$i_berat,$i_harga2,$i_kondisi,\"$i_deskripsi\",Ya,$i_gambar1, , , , , \n";
	}
while ($rdata = mysql_fetch_assoc($qdata));



//isi
$content = ob_get_contents();
ob_end_clean();



echo $content;


exit();
?>