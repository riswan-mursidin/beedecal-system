<?php  

class RajaOnkir{
    private string $key = '3edd124529d0527e0cff142cd3ec17a6';

    public function checkOngkir(string $kurir, string $tujuan, float $berat){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://pro.rajaongkir.com/api/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=&originType=city&destination=574&destinationType=subdistrict&weight=1700&courier=jne",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: $this->key"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $array = json_decode($response,TRUE);
        return $array["rajaongkir"]["results"];
    }
    
}

?>