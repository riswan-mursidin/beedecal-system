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
        public function InsertStore($value1,$value2,$value3,$value4,$value5,$value6){
            $connect = $this->conn;
            $query = "INSERT INTO store_galeri (name_store,owner_store,address_store,email_store,telpn_store,id_owner) VALUES('$value1','$value2','$value5','$value3','$value4','$value6')";
            return mysqli_query($connect, $query);
        }

        // update to table storw_galeri
        public function updateStore($value1,$value2,$value3,$value4,$value5,$value6){
            $connect = $this->conn;
            $query = "UPDATE store_galeri SET name_store='$value1', owner_store='$value2', address_store='$value5', email_store='$value3', telpn_store='$value4' WHERE id_owner='$value6'";
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

        // public function insertOrder(
        //     string $owner,
        //     string $value1 = null,
        //     string $value2 = null,
        //     string $value3 = null,
        //     string $value4 = null,
        //     string $value5 = null,
        //     string $value6 = null
        // ){
        //     $query = "INSERT INTO data_pemesanan (status_produk_order,code_order,id_customer,status_pay_order,tgl_order,status_order,id_owner) VALUES('$value1','$value2','$value3','$value4','$value5','$value6','$owner')";
        //     $result = mysqli_query($this->conn,$query);

        //     return $result;
        // }
    
    }

    


session_start();
error_reporting(0);
?>