<?php 
// CRUD Class db 


    class DbClass{
        // Configuration Db
        var $SERVERNAME = "localhost"; 
        var $USERNAME = "root";
        var $PASSWORD = "" ;
        var $DBNAME = "galeri_stiker";
        // Var Connection
        var $conn;
        // Table name in array

        public function __construct(){
            $connection = mysqli_connect($this->SERVERNAME, $this->USERNAME, $this->PASSWORD, $this->DBNAME);
            if($connection){
                $this->conn = $connection;
            }
        }
        
        // Method for view or select table
        public function selectTable(
            string $tableparam,
            string $field1 = null, 
            string $value1 = null, 
            string $field2 = null, 
            string $value2 = null, 
            string $field3 = null, 
            string $value3 = null, 
            string $field4 = null,
            string $value4 = null
            ){
            $connect = $this->conn;
            if(!is_null($value1) && !is_null($field1) && !is_null($value2) && !is_null($field2) && !is_null($value3) && !is_null($field3) && !is_null($value4) && !is_null($field4)){
                $query = "SELECT * FROM $tableparam WHERE ".$field1."='".$value1."' AND ".$field2."='".$value2."' AND ".$field3."='".$value3."' AND ".$field4."='".$value4."'";
                $result = mysqli_query($connect, $query);
                return $result;
            }elseif(!is_null($value1) && !is_null($field1) && !is_null($value2) && !is_null($field2) && !is_null($value3) && !is_null($field3)){
                $query = "SELECT * FROM $tableparam WHERE ".$field1."='".$value1."' AND ".$field2."='".$value2."' AND ".$field3."='".$value3."'";
                $result = mysqli_query($connect, $query);
                return $result;
            }elseif(!is_null($value1) && !is_null($field1) && !is_null($value2) && !is_null($field2)){
                $query = "SELECT * FROM $tableparam WHERE ".$field1."='".$value1."' AND ".$field2."='".$value2."'";
                $result = mysqli_query($connect, $query);
                return $result;
            }elseif(!is_null($value1) && !is_null($field1)){
                $query = "SELECT * FROM $tableparam WHERE ".$field1."='".$value1."'";
                $result = mysqli_query($connect, $query);
                return $result;
            }else{
                $query = "SELECT * FROM $tableparam";
                $result = mysqli_query($connect, $query);
                return $result;
            }
        }

        public function deleteTable($tableparam, $value, $field){
            $connect = $this->conn;
            $query = "DELETE FROM $tableparam WHERE $field='$value'";
            return mysqli_query($connect, $query);
        }

    }

    class ConfigClass extends DbClass{
        // format fullname
        public function nameFormater($name){
            $array_name = explode(" ", $name);
            $wordcount = count($array_name);
            $word = "";
            for($i=0; $i<$wordcount; $i++){
                $word .= ucfirst($array_name[$i])." ";
            }
            return $word;
        }

        public function boldText($txt){
            $t = explode(" | ", $txt);
            $end = "<b>".end($t)."</b>";
            $count = strlen($txt) - strlen(end($t));
            echo substr_replace($txt,$end,$count,strlen($txt));
        }

        public function formatJenis($param,$id,?string $spasi,$owner){
            if($param == "select"){
                $check = $this->selectTable("bahan_stiker","id_bahan",$id,"id_owner",$owner);
                if(mysqli_num_rows($check) > 0){
                    $row = mysqli_fetch_assoc($check);
                    echo '<option value="">'.$spasi.$this->nameFormater($row["nama_bahan"]).'</option>';
                    $sub = $this->selectTable("bahan_stiker","id_parent_bahan",$row["id_bahan"],"id_owner",$owner);
                    while($rowsub=mysqli_fetch_assoc($sub)){
                        $this->formatJenis("select",$rowsub['id_bahan'],"-".$spasi,$owner);
                    }
                }
            }else{
                $check = $this->selectTable("bahan_stiker","id_bahan",$id,"id_owner",$owner);
                if(mysqli_num_rows($check) > 0){
                    $row = mysqli_fetch_assoc($check);
                    if($row["id_parent_bahan"] == 0){
                        $cetak = $this->boldText($this->nameFormater($row["nama_bahan"]).$spasi);
                        echo $cetak;
                    }else{
                        $n = $this->nameFormater($row["nama_bahan"]);
                        $this->formatJenis($param,$row['id_parent_bahan']," | ".$n.$spasi,$owner);
                    }
                }
            }
        }

        public function formatKategori($param,$id,?string $spasi,$owner){
            if($param == "select"){
                $check = $this->selectTable("kategori_stiker","id_kategori",$id,"id_owner",$owner);
                if(mysqli_num_rows($check) > 0){
                    $row = mysqli_fetch_assoc($check);
                    if($row["id_parent_kategori"] == 0){
                        $cetak = $this->boldText($this->nameFormater($row["nama_kategori"]).$spasi);
                        echo $cetak;
                    }else{
                        $n = $this->nameFormater($row["nama_kategori"]);
                        $this->formatKategori($param,$row['id_parent_kategori']," / ".$n.$spasi,$owner);
                    }
                }
            }else{
                $check = $this->selectTable("kategori_stiker","id_kategori",$id,"id_owner",$owner);
                if(mysqli_num_rows($check) > 0){
                    $row = mysqli_fetch_assoc($check);
                    if($row["id_parent_kategori"] == 0){
                        $cetak = $this->boldText($this->nameFormater($row["nama_kategori"]).$spasi);
                        echo $cetak;
                    }else{
                        $n = $this->nameFormater($row["nama_kategori"]);
                        $this->formatKategori($param,$row['id_parent_kategori']," | ".$n.$spasi,$owner);
                    }
                }
            }
        }

        public function dataIndonesia(string $param, ?string $id){
            if($param == "prov"){

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://pro.rajaongkir.com/api/province",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "key: 3edd124529d0527e0cff142cd3ec17a6"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    return "error";
                } else {
                    $array = json_decode($response,TRUE);
                    return $array["rajaongkir"]["results"];
                    
                }
            }elseif($param == "kab_kota"){
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://pro.rajaongkir.com/api/city?province=".$id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "key: 3edd124529d0527e0cff142cd3ec17a6"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    return "error";
                } else {
                    $array = json_decode($response,TRUE);
                    return $array["rajaongkir"]["results"];
                    
                }
            }elseif($param == "kec"){
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://pro.rajaongkir.com/api/subdistrict?city=".$id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "key: 3edd124529d0527e0cff142cd3ec17a6"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    return "error";
                } else {
                    $array = json_decode($response,TRUE);
                    return $array["rajaongkir"]["results"];
                }
            }
        }

        public function statusColor($status){
            if($status == "Aktif"){
                echo '<span class="text-success">'.$status.'</span>';
            }else{
                echo '<span class="text-danger">'.$status.'</span>';
            }
        }

        // number wa format
        public function formatNumber($number){
            if(substr(trim($number), 0,1) == 0){
                return substr_replace($number,'',0,1);
            }else if(substr(trim($number), 0,2) == 62){
                return substr_replace($number,'',0,2);
            }else{
                return $number;
            }
        }

        public function compressFoto($sc, $newpath){
            $source_image = imagecreatefrompng($sc);

            return imagepng($source_image,$newpath,9);
        }

        public function saveFoto($path, $sc, $user){
            $check = $this->selectTable("user_galeri","username_user",$user);
            if(mysqli_num_rows($check) > 0){
                $row = mysqli_fetch_assoc($check);
                $old_foto = $row['foto_user'];
                $image_parts = explode(";base64,", $sc);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $ran = md5(rand());
                $path_img = $path.$ran.".".$image_type;
                file_put_contents($path_img, $image_base64);
                $path_img_convert = $path.$ran."compress.png";
                $compress = $this->compressFoto($path_img, $path_img_convert);
                if($compress){
                    unlink($path_img);
                    $query = "UPDATE user_galeri SET foto_user='$path_img_convert' WHERE username_user='$user'";
                    $result = mysqli_query($this->conn, $query);
                    if($result && $old_foto != ""){
                        unlink($old_foto);
                    }
                }
            }
        }

        public function compress_image($ext, $path, $to){
            $rand = md5(rand());
            $new_path = $to.$rand.".".$ext;
            $img = "";
            if($ext == "jpg" || $ext == "jpeg"){
                $img = imagecreatefromjpeg($path);
            }elseif($ext == "png"){
                $img = imagecreatefrompng($path);
            }
            $result['bol'] = imagejpeg($img,$new_path,20);
            $result['db'] = $new_path;
            return $result;
        }

        public function saveFoto2($folder, $foto_name, $foto_path, $produk_name=null){
            if(!file_exists($folder)){
                mkdir($folder);
            }

            if(!is_null($produk_name)){
                $produk_name = str_replace("/\s+/","_",$produk_name);
            }else{
                $produk_name = "";
            }
            $ranname = md5(rand());
            $format_foto = end(explode(".",$foto_name));
            $new_foto_name = $ranname.".".$format_foto;
            $new_foto_path = $folder."/".$new_foto_name;
            $move = move_uploaded_file($foto_path, $new_foto_path);
            if($move){
                $compress = $this->compress_image($format_foto, $new_foto_path, $folder."/compress".$produk_name);
                if($compress['bol']){
                    unlink($new_foto_path);
                    return $compress['db'];
                }
            }
        }

        public function createSpk($owner){
            $date = date("m");
            $cek_spk = "SELECT * FROM data_pemesanan WHERE month(tgl_order)='$date' AND id_owner='$owner' ORDER BY code_order DESC LIMIT 1";
            $result = mysqli_query($this->conn, $cek_spk);
            if(mysqli_num_rows($result) > 0){
                $row = mysqli_fetch_assoc($result);
                $spkdb = $row['code_order'];
                $spk = substr($spkdb, -3) + 1; 
                if(substr($spkdb,-3,2) == "00"){
                    return substr_replace($spkdb,"00".$spk,10,3);
                }elseif(substr($spkdb,-3,1) == "0"){
                    return substr_replace($spkdb,"0".$spk,10,3);
                }else{
                    return substr_replace($spkdb,$spk,10,3);
                }
            }else{
                $year = substr(date("Y"),-2);
                return "SPK-".date("d").date("m").$year."001";
            }

        }

        // update table user
        public function updateUser(
            string $user, 
            string $param, 
            string $value1=null, 
            string $value2=null,
            string $value3=null,
            string $value4=null
            ){
            if($param == "profil"){
                $query = "UPDATE user_galeri SET fullname_user='$value1', jk_user='$value2' WHERE username_user='$user'";
                return mysqli_query($this->conn, $query);
            }elseif($param == "pass"){
                $query = "UPDATE user_galeri SET pass_user='$value1' WHERE username_user='$user'";
                return mysqli_query($this->conn, $query);
            }else{
                $query = "UPDATE user_galeri SET jk_user='$value1', fullname_user='$value2', level_user='$value3', status_user='$value4' WHERE username_user='$user'";
                return mysqli_query($this->conn, $query);
            }
        }

        // insert to table user (register)
        public function insertUser(
            string $param,
            string $value1 = null,
            string $value2 = null,
            string $value3 = null,
            string $value4 = null,
            string $value5 = null,
            string $value6 = null,
            string $value7 = null,
            string $value8 = null
            ){
            $connect = $this->conn;
            if($param == "register"){
                $query = "INSERT INTO user_galeri (username_user,email_user,toko_user,pass_user) VALUES('$value1','$value2','$value3','$value4')";
                return mysqli_query($connect, $query);
            }else{
                $query = "INSERT INTO user_galeri (email_user,jk_user,username_user,fullname_user,level_user,status_user,pass_user,id_owner) VALUES('$value1','$value2','$value3','$value4','$value5','$value6','$value7','$value8')";
                return mysqli_query($connect, $query);
            }
        }

        // Insert to table store_galeri
        public function InsertStore($value1,$value2,$value3,$value4,$value5,$value6,$value7,$value8,$value9,$value10){
            $connect = $this->conn;
            $query = "INSERT INTO store_galeri (name_store,owner_store,address_store,email_store,telpn_store,id_owner,prov_id,kab_id,kec_id,kode_pos) VALUES('$value1','$value2','$value5','$value3','$value4','$value6','$value7','$value8','$value9','$value10')";
            return mysqli_query($connect, $query);
        }

        // update to table storw_galeri
        public function updateStore($value1,$value2,$value3,$value4,$value5,$value6,$value7,$value8,$value9,$value10){
            $connect = $this->conn;
            $query = "UPDATE store_galeri SET name_store='$value1', owner_store='$value2', address_store='$value5', email_store='$value3', telpn_store='$value4', prov_id='$value7', kab_id='$value8', kec_id='$value9', kode_pos='$value10' WHERE id_owner='$value6'";
            return mysqli_query($connect, $query);
        }

        public function insertCustomer(
            string $value1,
            string $value2,
            string $value3,
            string $value4,
            string $value5,
            string $value6,
            string $value7,
            string $value8,
            string $value9,
            string $value10,
            string $value11
        ){
            $query = "INSERT INTO customer_stiker (name_customer,username_customer,password_customer,email_customer,telpn_customer,prov_customer,kota_kab_customer,kec_customer,kode_pos_customer,address_customer,id_owner) VALUES('$value1','$value2','$value3','$value4','$value5','$value6','$value7','$value8','$value9','$value10','$value11')";
            return mysqli_query($this->conn, $query);
        }

        public function updateCustomer(
            string $user,
            string $value1,
            string $value2,
            string $value3,
            string $value4,
            string $value5,
            string $value6,
            string $value7,
            string $value8
        ){
            $query = "UPDATE customer_stiker SET name_customer='$value1', telpn_customer='$value2', status_customer='$value3', prov_customer='$value4', kota_kab_customer='$value5', kec_customer='$value6', kode_pos_customer='$value7', address_customer='$value8' WHERE username_customer='$user'";
            return mysqli_query($this->conn, $query);
        }

        public function insertMerk(
            string $owner,
            string $value1,
            string $value2
        ){
            $query = "INSERT INTO merek_galeri (jenis_merek,name_merek,id_owner) VALUES('$value1','$value2','$owner')"; 
            return mysqli_query($this->conn, $query);
        }

        public function updateMerk(
            string $edit,
            string $value1,
            string $value2
        ){
            $query = "UPDATE merek_galeri SET jenis_merek='$value1', name_merek='$value2' WHERE id_merek='$edit'";
            return mysqli_query($this->conn, $query);
        }

        public function insertBahan(
            string $owner,
            string $value1,
            string $value2
        ){
            $query = "INSERT INTO bahan_stiker (nama_bahan,id_parent_bahan,id_owner) VALUES('$value2','$value1','$owner')"; 
            return mysqli_query($this->conn, $query);
        }
        
        public function insertKategori(
            string $owner,
            string $value1,
            string $value2
        ){
            $query = "INSERT INTO kategori_stiker (nama_kategori,id_parent_kategori,id_owner) VALUES('$value2','$value1','$owner')"; 
            return mysqli_query($this->conn, $query);
        }

        public function deleteRekursif($id_parent_bahan){
            $sel = $this->selectTable("bahan_stiker","id_parent_bahan",$id_parent_bahan);
            if(mysqli_num_rows($sel) > 0){
                while($rowsel = mysqli_fetch_assoc($sel)){
                    $this->deleteRekursif($rowsel['id_bahan']);
                }
                $delete = $this->deleteTable("bahan_stiker",$id_parent_bahan,"id_parent_bahan");
            }
        }

        public function deleteRekursifOne($id_parent_kategori){
            $sel = $this->selectTable("kategori_stiker","id_parent_kategori",$id_parent_kategori);
            if(mysqli_num_rows($sel) > 0){
                while($rowsel = mysqli_fetch_assoc($sel)){
                    $this->deleteRekursif($rowsel['id_kategori']);
                }
                $delete = $this->deleteTable("kategori_stiker",$id_parent_kategori,"id_parent_kategori");
            }
        }

        public function updateBahan($param,$value){
            $query = "UPDATE bahan_stiker SET nama_bahan='$value' WHERE id_bahan='$param'";
            return mysqli_query($this->conn,$query);
        }
        
        public function updateKategori($param,$value){
            $query = "UPDATE kategori_stiker SET nama_kategori='$value' WHERE id_kategori='$param'";
            return mysqli_query($this->conn,$query);
        }

        public function insertUkuran($owner,$value){
            $query = "INSERT INTO ukuran_stiker (lebar_ukuran,id_owner) VALUES('$value','$owner')";
            return mysqli_query($this->conn, $query);
        }

        public function updateUkuran($param,$value){
            $query = "UPDATE ukuran_stiker SET lebar_ukuran='$value' WHERE id_ukuran='$param'";
            return mysqli_query($this->conn, $query);
        }

        public function InsertBank(
            string $owner,
            string $value1,
            string $value2,
            string $value3
        ){
            $query = "INSERT INTO bank_galeri (name_bank,pemilik_bank,rek_bank,id_owner) VALUES('$value1','$value2','$value3','$owner')";
            return mysqli_query($this->conn, $query);
        }

        public function updateBank(
            string $param,
            string $value1,
            string $value2,
            string $value3
        ){
            $query = "UPDATE bank_galeri SET name_bank='$value1', pemilik_bank='$value2', rek_bank='$value3' WHERE id_bank='$param'";
            return mysqli_query($this->conn, $query);
        }

        public function insertType(
            string $value1,
            string $value2,
            string $value3,
            string $value4,
            string $value5,
            string $owner
            ){
            $query = "INSERT INTO type_galeri (name_type,fullbodydash_harga_type,fullbody_harga_type,lite_harga_type,id_merek,id_owner) VALUES('$value1','$value2','$value3','$value4','$value5','$owner')";
            $result = mysqli_query($this->conn, $query);
            return $result;
        }

        public function updateType(
            string $value1,
            string $value2,
            string $value3,
            string $value4,
            string $value5,
            string $param
        ){
            $query = "UPDATE type_galeri SET name_type='$value1', fullbodydash_harga_type='$value2', fullbody_harga_type='$value3', lite_harga_type='$value4', id_merek='$value5' WHERE id_type='$param'";
            $result = mysqli_query($this->conn, $query);
            return $result;
        }

        public function insertProduct(
            string $owner,
            string $value1,
            string $value2,
            string $value3,
            string $value4,
            string $value5,
            string $value6,
            string $value7,
            string $value8
        ){
            $query = "INSERT INTO product_stiker(name_product,status_product,detail_product,stock_product,weight_product,harga_product,foto_product,id_kategori,id_owner) VALUES ('$value1','$value2','$value3','$value4','$value5','$value6','$value7','$value8','$owner')";

            $result = mysqli_query($this->conn,$query);
            return $result;
        }

        public function insertVarian(
            string $owner,
            string $value1,
            string $value2,
            string $value3
        ){
            $query = "INSERT INTO varian_warna (desk_warna,foto_warna,produk_name,id_owner) VALUES('$value1','$value2','$value3','$owner')";
            $result = mysqli_query($this->conn, $query);
            return $result;
        }

        public function insertOrder(
            string $owner,
            string $value1,
            string $value2,
            string $value3,
            string $value4,
            string $value5,
            string $value6,
            string $value7,
            string $value8,
            string $value9,
            string $value10,
            string $value11,
            string $value12,
            string $value13,
            string $value14,
            string $value16,
            string $value17,
            string $value18,
            string $value19,
            string $value20,
            string $value21,
            string $value22,
            string $value23,
            string $value24,
            string $value25,
            string $value26,
            string $value27,
            string $value28,
            string $value29,
            string $value30,
            string $value31,
            string $value32,
            string $value33
        ){
            $query = "INSERT INTO data_pemesanan ( jenis_produk_order,code_order,id_customer,status_pay_order,tgl_order,status_order,id_owner,kategori_produk_order,produk_order,harga_produk_order,model_stiker_order,status_desain_order,status_cetak_order,laminating_order,status_pasang_order,desk_desain_order,kategori_pemasang_order,harga_pasang_order,keterangan_order,diskon_order,status_pengiriman_order,kurir_pengiriman_order,prov_send_order,kab_send_order,kec_send_order,kode_pos_send_order,alamat_lengkap_send_order,berat_send_order,ongkir_send_order,nama_paket_send_order,estimasi_send_order,sisa_pembayaran_order) VALUES('".$value1."','".$value2."','".$value3."','".$value4."','".$value5."','".$value6."','".$owner."','".$value7."','".$value8."','".$value9."','".$value10."','".$value11."','".$value12."','".$value13."','".$value14."','".$value16."','".$value17."','".$value18."','".$value19."','".$value20."','".$value21."','".$value22."','".$value23."','".$value24."','".$value25."','".$value26."','".$value27."','".$value28."','".$value29."','".$value30."','".$value31."','".$value32."')";
            $result = mysqli_query($this->conn, $query);
            if(!$result){
                return mysqli_error($this->conn);
            }
            return $result;
        }

        public function updateOrder(
            string $owner,
            string $spk,
            string $jenis_produk,
            string $customer_id,
            string $status_pay,
            string $status_order,
            string $kategori_produk,
            string $produk_id,
            string $varian_harga,
            string $varian_model,
            string $desain_status,
            string $cetak_status,
            string $laminating,
            string $pemasangan_status,
            string $desk_desain,
            string $kategori_pemasang,
            string $harga_pasang,
            string $keterangan,
            string $diskon,
            string $status_pengiriman,
            string $kurir,
            string $prov_desti,
            string $kabkota_desti,
            string $kec_desti,
            string $kode_pos,
            string $alamat_lengkap,
            string $berat,
            string $cost,
            string $name_paket,
            string $etd,
            string $sisabayar
        ){
            $query = "UPDATE data_pemesanan SET jenis_produk_order='$jenis_produk', id_customer='$customer_id', status_pay_order='$status_pay', status_order='$status_order', sisa_pembayaran_order='$sisabayar', kategori_produk_order='$kategori_produk', produk_order='$produk_id', harga_produk_order='$varian_harga', model_stiker_order='$varian_model', status_desain_order='$desain_status', status_cetak_order='$cetak_status', laminating_order='$laminating', status_pasang_order='$pemasangan_status',  desk_desain_order='$desk_desain', kategori_pemasang_order='$kategori_pemasang', harga_pasang_order='$harga_pasang', keterangan_order='$keterangan', diskon_order='$diskon', status_pengiriman_order='$status_pengiriman', kurir_pengiriman_order='$kurir', prov_send_order='$prov_desti', kab_send_order='$kabkota_desti', kec_send_order='$kec_desti', kode_pos_send_order='$kode_pos', alamat_lengkap_send_order='$alamat_lengkap', berat_send_order='$berat', ongkir_send_order='$cost', nama_paket_send_order='$name_paket', estimasi_send_order='$etd' WHERE code_order ='$spk' AND id_owner='$owner'";
            $result = mysqli_query($this->conn, $query);
            return $result;
        }

        // format date
        public function dateFormatter($date){
            $array_date = explode("-", $date);
            $day = explode(" ",$array_date[2]);
            $month = $array_date[1];
            $year = $array_date[0];
            switch($month){
                case 1:
                    return $day[0]." Januari ".$year;
                    break;
                case 2:
                    return $day[0]." Februari ".$year;
                    break;
                case 3:
                    return $day[0]." Maret ".$year;
                    break;
                case 4:
                    return $day[0]." April ".$year;
                    break;
                case 5:
                    return $day[0]." Mei ".$year;
                    break;
                case 6:
                    return $day[0]." Juni ".$year;
                    break;
                case 7:
                    return $day[0]." Juli ".$year;
                    break;
                case 8:
                    return $day[0]." Agustus ".$year;
                    break;
                case 9:
                    return $day[0]." September ".$year;
                    break;
                case 10:
                    return $day[0]." Oktober ".$year;
                    break;
                case 11:
                    return $day[0]." November ".$year;
                    break;
                case 12:
                    return $day[0]." Desember ".$year;
                    break;
            }
        }

        public function formatHari($day){
            switch($day){
                case "Mon":
                    return "Senin";
                    break;
                case "Tue":
                    return "Selasa";
                    break;
                case "Wed":
                    return "Rabu";
                    break;
                case "Thu":
                    return "Kamis";
                    break;
                case "Fri":
                    return "Jumat";
                    break;
                case "Sat":
                    return "Sabtu";
                    break;
                case "Sun":
                    return "Minggu";
                    break;
            }
        }

        public function insertDetailTr(
            string $owner,
            string $v1,
            string $v2,
            string $v3
        ){
            $query = "INSERT INTO detail_transaksi (code_order,tgl_transaksi,jumlah_transaksi,id_owner) VALUES('$v1','$v2','$v3','$owner')";
            $result = mysqli_query($this->conn, $query);
            return $result;
        }

        public function updateDetailTr(
            string $id,
            string $fee,
            string $sisa,
            string $spk
        ){
            $query = "UPDATE detail_transaksi SET jumlah_transaksi='$fee' WHERE id='$id'";
            $result = mysqli_query($this->conn, $query);
            if($result){
                $query2 = "UPDATE data_pemesanan SET sisa_pembayaran_order='$sisa' WHERE code_order='$spk'";
                $result2 = mysqli_query($this->conn, $query2);
                return $result2;
            }
        }
    
    }

    


session_start();
error_reporting(0);
?>