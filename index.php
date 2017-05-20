<?php
session_start();


ini_set('max_execution_time', 0);



//ambil nilai
require("inc/config.php");
require("inc/fungsi.php");
require("inc/koneksi.php");
$tpl = LoadTpl("template/cp_depan.html");

	


nocache;

//nilai
$filenya = "index.php";
$judul = "BiasaWae-Ambil-Produk";
$judulku = $judul;
$kunci = balikin($_REQUEST['kunci']);
$s = nosql($_REQUEST['s']);
$page = nosql($_REQUEST['page']);
if ((empty($page)) OR ($page == "0"))
	{
	$page = "1";
	}



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

<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td align="center">
<h3>
Selamat Datang di BIASAWAE-Ambil-Produk
</h3>

<p>
Aplikasi ini bermanfaat untuk ambil daftar produk dari suatu toko yang ada di suatu marketplace.
</p>


<hr>
<p>
[<a href="bukalapak.php">BUKALAPAK</a>]. 
[<a href="tokopedia.php">TOKOPEDIA</a>].
</p>

<hr>



</td>
</tr>
</table>
</form>';


//isi
$isi = ob_get_contents();
ob_end_clean();

require("inc/niltpl.php");


exit();
?>