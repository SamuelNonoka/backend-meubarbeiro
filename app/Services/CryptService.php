<?php

namespace App\Services;

class CryptService 
{
  public static function encrypt($texto) 
  {
    return (new self)->encrypt_decrypt('encrypt', $texto);
  }

  public static function decrypt($texto) 
  {
    return (new self)->encrypt_decrypt('decrypt', $texto);
  }

  // Encrpitar usando open ssl
  private function encrypt_decrypt($action, $string)
  {    
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'APP_CRYPT_SECRET_KEY=MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA35wtqknjM2oIMPRu8+h8 xJ3dDT1AVzpfBHCTeEBWo55SofyG5gX3XQ29M3PaL6kSz1TRrfe4tNVc5o+tHSZC 6T8lpOwS5xkgD5DWsHyTXDxAR3WWQr5eUR4TcLz2c+K+a4159dGkjaP5hwseIaHh pZ4iuxOtS03V1u/2FYmK7qnRXin8kr8tWUZyae5+lqemMkIS9o0SWeyttUSGlm0i h0iur+AOA9cMISbY4+uhmU/y1P4kuqVJ4lVL2tNGRUcfPOOlFqdBABK2y4xrT4uB rAosw1vxkjlrKXJKdgC0HVC8qVOAtOfHADuKEphpUpoJEUOHmkx2Gkskzl6I+aC1 +pO4VviCbRXtKgjnC8zHj9Gs0Ysc1XuwKdIVDpwh7BUjB4fUcvU6Zdd4K2u4RP/6 sCFSCA/eytVPF3LA2TnIFW8GBWq/BwmAJvTKXdi24u4FGEDltf04f9/aRtHUtjmp rrYLISXofE693n7RJRNVcfMVLnQA6oeDDsk+FHLRxbWAmbJYKCjDPEFLcFfJQQDJ kH2AWLkYL+0LwPHo5rpeXWCFUH+/OnSYYZ3jPxC0uOx9TpDpOxX7W/SXrCoLHAH3 t0L/P44OK1tY84F1zxTuqzumrMAfY8G4BzLVT85oz4F7qPbwiX4QBGOHFepOPpN4 KC3R+pLprDoKqkDCiCPuW/0CAwEAAQ==';
    $secret_iv  = 'base64:dV3pzjJ3+WXUDfKu8Vpx2VZyuC1b5TCJpTFwtJiHY8M=';
    $key        = hash('sha256', $secret_key); // hash

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if($action == 'encrypt') {      
      $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
      $output = base64_encode($output);
    } else if($action == 'decrypt') {
      $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv); 
        
      if (!$output)           
        $output = $string;        
    }  
    
    return $output;
  } // Fim do método encrypt_decrypt
} 