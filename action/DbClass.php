<?php 
// CRUD Class db 


    class DbClass{
        // Configuration Db
        var $SERVERNAME;
        var $USERNAME;
        var $PASSWORD;
        var $DBNAME;
        // Var Connection
        var $conn;
        // Table name in array

        public function __construct(){
            $this->SERVERNAME = "localhost"; 
            $this->USERNAME = "root"; 
            $this->PASSWORD = ""; 
            $this->DBNAME = "galeri_stiker";
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
                return mysqli_query($connect, $query);
            }elseif(!is_null($value1) && !is_null($field1) && !is_null($value2) && !is_null($field2) && !is_null($value3) && !is_null($field3)){
                $query = "SELECT * FROM $tableparam WHERE ".$field1."='".$value1."' AND ".$field2."='".$value2."' AND ".$field3."='".$value3."'";
                return mysqli_query($connect, $query);
            }elseif(!is_null($value1) && !is_null($field1) && !is_null($value2) && !is_null($field2)){
                $query = "SELECT * FROM $tableparam WHERE ".$field1."='".$value1."' AND ".$field2."='".$value2."'";
                return mysqli_query($connect, $query);
            }elseif(!is_null($value1) && !is_null($field1)){
                $query = "SELECT * FROM $tableparam WHERE ".$field1."='".$value1."'";
                return mysqli_query($connect, $query);
            }else{
                $query = "SELECT * FROM $tableparam";
                return mysqli_query($connect, $query);
            }
                
            
        }

        public function deleteTable($tableparam, $value, $field){
            $connect = $this->conn;
            $query = "DELETE FROM $tableparam WHERE $field='$value'";
            return mysqli_query($connect, $query);
        }

    }

    class ConfigClass extends DbClass{

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
    }

session_start();
error_reporting(0);
?>