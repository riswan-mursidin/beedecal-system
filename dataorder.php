<?php  
require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
    header('Location: auth-login');
    exit();
}

$db = new ConfigClass();

$userselect = $db->selectTable("user_galeri","id_user",$_SESSION['login_stiker_id']);
$row = mysqli_fetch_assoc($userselect);
$usernamelogin = $row['username_user'];
$id = $row['id_owner'];
// jika yang login buka owner ambil data owner dari id owner
if($row['id_owner'] == "0"){
    $id = $row['id_user'];
}
function showProduk($id_produk){
    global $db;
    $querydb = $db->selectTable("type_galeri","id_type",$id_produk);
    $rowdb=mysqli_fetch_assoc($querydb);
    $result = $rowdb['name_type'];
    return $result;
}
function showCustomer($id_customer, $pengiriman, $id_order=null){
    global $db;
    // name customer
    $querydb = $db->selectTable("customer_stiker","id_customer",$id_customer);
    $rowdb=mysqli_fetch_assoc($querydb);
    // alamat customer
    
    $result['name'] = $rowdb['name_customer'];
    $result['prov'] = $rowdb['prov_customer'];
    $result['kab'] = $rowdb['kota_kab_customer'];
    $result['kec'] = $rowdb['kec_customer'];
    $result['kodepos'] = $rowdb['kode_pos_customer'];
    return $result;
}
function resultDiskon($owner,$spk,$harga,$disk,$satuan){
    global $db;
    $count = 0;
    $tamby = $db->selectTable("biaya_tambahan_order","id_owner",$owner,"code_order",$spk);
    if(mysqli_num_rows($tamby) > 0){
        while($rowtamby=mysqli_fetch_assoc($tamby)){
            $count += $rowtamby['harga_ketbiaya'];
        }
        $diskon = ($harga + $count) * ($disk/100);
        $result['hasil'] = ($harga + $count) - $diskon;
        $result['tamby'] = $count;
        if($satuan == "rupiah"){
            $result['tamby'] = $count;
            $result['hasil'] = ($harga + $count) - $disk;
        }
        return $result;
    }else{
      $diskon = $harga * ($disk/100);
        $result['hasil'] = $harga - $diskon;
        $result['tamby'] = $count;
        if($satuan == "rupiah"){
            $result['hasil'] = $harga - $disk;
            $result['tamby'] = 0;
        }
        return $result;
    }
}

function statusBadge($txt,$sisa){

    if($txt == "Belum Lunas"){
        $result = '<h9><span class="badge rounded-pill bg-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Sisa Rp.'.number_format($sisa,2,",",".").'">Belum Lunas</span></h9>';
        return $result;
    }else{
        $result = '<h9><span class="badge rounded-pill bg-success">Lunas</span></h9>';
        return $result;
    }
}
function statusBadge2($txt){
    if($txt == "Tidak"){
        $result = '<h9><span class="badge rounded-pill bg-danger">Belum Lunas</span></h9>';
        return $result;
    }else{
        $result = '<h9><span class="badge rounded-pill bg-success">Lunas</span></h9>';
        return $result;
    }
}

if(isset($_POST['action'])){
    $dari = $_POST['dari'];
    $sampai = $_POST['sampai'];

    $sql = "SELECT * FROM data_pemesanan WHERE id_owner='$id' AND tgl_order BETWEEN '$dari' AND '$sampai'";
    $result = mysqli_query($db->conn, $sql);
    $total_order = 0;
    if(mysqli_num_rows($result) > 0){
        while($roworder = mysqli_fetch_assoc($result)){
            $resultdisk = resultDiskon($id,$roworder['code_order'],$roworder['harga_produk_order'],$roworder['diskon_order'],$roworder['satuan_potongan']);

            // total pendapatan
            $total_order += $resultdisk['hasil'];
            
            $status = $roworder['jenis_produk_order'] == 'Custom' ? '<span class="badge bg-light">Custom</span>' : 'No Custom';
            $product = $roworder['jenis_produk_order'] == 'Custom' && $roworder['kategori_produk_order'] == "Other" ? $db->nameFormater($roworder['produk_order']) : $db->nameFormater(showProduk($roworder['produk_order']));
            
            $customer = showCustomer($roworder['id_customer'],$roworder['status_pengiriman_order'],$roworder['id_order']);
            $prov = 'Prov: '.$customer['prov'].'<br>';
            $kab = 'Kab/Kota: '.$customer['kab'].'<br>';
            $kec = 'Kec: '.$customer['kec'].'<br>';
            $pos = 'Kode Pos: '.$customer['kodepos'].'<br>';
            
            if($roworder['status_pengiriman_order'] == "Ya"){
                $prov = 'Prov: '.$roworder['prov_send_order'].'<br>';
                $kab = 'Kab/Kota: '.$roworder['kab_send_order'].'<br>';
                $kec = 'Kec: '.$roworder['kec_send_order'].'<br>';
                $pos = 'Kode Pos: '.$roworder['kode_pos_send_order'].'<br>';
            }
    
            $diskonshow = $roworder['diskon_order'] != "" ? '<span style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Dari Harga Rp.'.number_format(($roworder['harga_produk_order']+$resultdisk['tamby']),2,",",".").'" class="badge bg-secondary">Diskon Rp.'.number_format($roworder['diskon_order'],2,",",".").'</span><br>' : '' ;
    
            if($roworder['satuan_potongan'] == "persen"){ 
                $diskonshow = $roworder['diskon_order'] != "" ? '<span style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Dari Harga Rp.'.number_format(($roworder['harga_produk_order']+$resultdisk['tamby']),2,",",".").'" class="badge bg-secondary">Diskon '.$roworder['diskon_order'].'%</span><br>' : '' ;
            }
    
            $pasang = $roworder['status_pasang_order'] == "Ya" ? ' Rp.'.number_format($roworder['harga_pasang_order'],2,",",".") : 'Tidak Dipasang';
            
            $badge = "";
            if($roworder['status_pengiriman_order'] == "Ya"){
                if($roworder['ongkir_cod_order'] == "COD"){
                    $badge = "bg-danger";
                }else{
                    $badge = "bg-success";
                }
            }
    
            $pengirimanshow = $roworder['status_pengiriman_order'] == "Ya" ? " Rp.".number_format($roworder['ongkir_send_order'],2,",",".").' <h9><span class="badge rounded-pill '.$badge.'">'.$roworder['ongkir_cod_order'].'</span></h9>' : '-,-';
            
            $data[] = '<tr>
                            <td>'.$roworder['code_order'].'<br>'.$product.'</td>
                            <td><b>'.$db->nameFormater($customer['name']).'</b>'.$status.'<br>'.$prov.$kab.$kec.$pos.'</td>
                            <td>'.$diskonshow.'Harga Produk: Rp.'.number_format($resultdisk['hasil'],2,",",".").statusBadge($roworder['status_pay_order'],$roworder['sisa_pembayaran_order']).'<br>Harga Pasang:'.$pasang.'<br>'.$pengirimanshow.'</td>
                            <td>Desain: <b>'. $roworder['status_desain_order'] .'</b><br>Cetak: <b>'. $roworder['status_cetak_order'] .'</b><br>Laminating: <b>'. $roworder['laminating_order'] .'</b><br>Pasang: <b>'. $roworder['status_pasang_order'] .'</b><br></td>
                            <td>'.$db->dateFormatter($roworder['tgl_order']).'</td>
                            <td><h5><span class="badge bg-warning">'.$roworder['status_order'].'</span></h5></td>
                        </tr>';
        }
    
    }
    $theends['hasil'] = $total_order;
    $theends['table'] = $data;
    echo json_encode($theends);
}
?>