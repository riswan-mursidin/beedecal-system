<?php  

class RajaOngkir{
    private string $key = '3edd124529d0527e0cff142cd3ec17a6';

    public function checkOngkir(string $kurir, string $asal, string $tujuan, string $berat){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://pro.rajaongkir.com/api/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=$asal&originType=city&destination=$tujuan&destinationType=subdistrict&weight=$berat&courier=$kurir",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: 3edd124529d0527e0cff142cd3ec17a6"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if($err){
            return "error";
        }else{
            $data = json_decode($response);
            return $data->rajaongkir->results[0];
        }
                
    }
    
}

?>