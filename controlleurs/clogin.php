<?php
session_start();
include_once "conex.php";
include_once "../models/utilisateur.class.php";
function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'Qa4dvMP^d.=)d5';
    $secret_iv = '2dz684²Df';
    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

if(isset($_POST['btnLogin']))  
{            
    
        $login = $_POST['prenom'];
        $password = $_POST['password'];
        //cr�er un client juste avec login et password, pour utiliser la fonction login
        $utilisateur = new utilisateur($login,'',encrypt_decrypt("encrypt", $password),'','');
        $result = $utilisateur->logger($login,$password);
        if($result ==true)
        {
            header("Location:../views/menu.php");     
            $_SESSION["User"]=$login;
        }
        else  
        {                 
            header("Location: ../index.php");                     
        }
    
  
}
?>