<?php  
require_once "action/DbClass.php";
$db = new ConfigClass();
$conn = $db->conn;

if(isset($_POST['action'])){
    $owner = $_POST["id_owner"];
    $year = date("Y"); $month = date("m");
    $count_product_custome_order = 0;
    $data = array();
    $get_product_custome = $db->selectTable("type_galeri","id_owner",$owner);
    while($row_product_custome = mysqli_fetch_assoc($get_product_custome)){
        $id_product_custome = $row_product_custome['id_type'];

        $sql_order_this_month = "SELECT * FROM data_pemesanan WHERE id_owner='$owner' AND year(tgl_order)='$year' AND month(tgl_order)='$month' AND produk_order='$id_product_custome' AND jenis_produk_order='Custom'";
        $get_order_this_month = mysqli_query($conn, $sql_order_this_month);
        if(mysqli_num_rows($get_order_this_month) > 0){
            $name_product_custome = ucfirst($row_product_custome['name_type']);
            $jumlah_product_custome = mysqli_num_rows($get_order_this_month);
            $color_product_custome = '#'.rand(100000,999999).'';
            $count_product_custome_order += mysqli_num_rows($get_order_this_month);

            $data[] = array(
                'product' => $name_product_custome,
                'total' => $jumlah_product_custome,
                'color' => $color_product_custome
            );
        }
    }
    $sqlOrder = "SELECT * FROM data_pemesanan WHERE id_owner='$owner' AND year(tgl_order)='$year' AND month(tgl_order)='$month' AND jenis_produk_order='Custom'";
    $getOrder = mysqli_query($conn, $sqlOrder);

    $all_order_this_month = mysqli_num_rows($getOrder);
    $other_product_custome = $all_order_this_month - $count_product_custome_order;
    $color_product_custome = '#'.rand(100000,999999).'';
    $data[] = array(
        'product' => 'Other',
        'total' => $other_product_custome,
        'color' => $color_product_custome
    );

    echo json_encode($data);
}
?>