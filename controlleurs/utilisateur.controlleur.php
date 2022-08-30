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

if(isset($_POST['btn_add_utilisateur']))  
{            
        $login = $_POST['loginutilisateur'];
         $nom = $_POST['nomutilisateur'];
        $statut = $_POST['selutilisateurs'];
       $passw=$_POST['password'];
       $initial=$_POST['Initialutilisateur'];

       $enc_pass=encrypt_decrypt('encrypt',$passw);
        
        //cr�er un client juste avec login et password, pour utiliser la fonction login
    
        $user = new Utilisateur($login,$statut,$enc_pass,$initial,$nom);
        $result = $user->save();
        
    header("Location:../views/Vuser.php");
  
}

if(isset($_POST['btn_update_utilisateurs']))  
{            
    $id = $_POST['idutilisateurupdate'];
    $login = $_POST['loginutilisateurupdate'];
    $nom = $_POST['nomnutilisateurupdate'];
   $statut = $_POST['selutilisateursupdate'];
  $passw=$_POST['pswupdate'];
  $initial=$_POST['Initialutilisateurupdate'];

  $enc_pass=encrypt_decrypt('encrypt',$passw);
   
   //cr�er un client juste avec login et password, pour utiliser la fonction login

   $user = new Utilisateur($login,$statut,$enc_pass,$initial,$nom);
   $result = $user->update($id,$login,$statut,$enc_pass,$initial,$nom);
   
header("Location:../views/Vuser.php");
}
if(isset($_POST['btn_delete_utilisateur']))  
{
    
    $id = $_POST['idutilisateurupdate'];
    $Utilisateur = new Utilisateur("","","","","");
    $result = $Utilisateur->delete($id);
                header("Location:../views/Vuser.php");
}
?>