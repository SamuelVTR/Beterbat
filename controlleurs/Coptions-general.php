<?php
session_start();
include_once "conex.php";
if(isset($_POST["save-opt-gene"]))  
{ 

    $option_tva=$_POST["option_tva"];
    $option_encaillassement=$_POST["option_encaillassement"];
    $option_betonnage=$_POST["option_betonnage"];
    $option_egout=$_POST["option_egout"];
    $option_fosse1=$_POST["option_fosse1"];
    $option_fosse2=$_POST["option_fosse2"];
    $option_fosse3=$_POST["option_fosse3"];
    $option_fosse=$option_fosse1."/".$option_fosse2."/".$option_fosse3;
    $option_taux_dommage=$_POST["option_taux_dommage"];
    $trottoir=$_POST["trottoir"];
   $code_remise=$_POST["code_remise"];
        try {      
            include("conex.php");
            $ql = "UPDATE  divers SET tva='$option_tva',encaillassement='$option_encaillassement',betonnage='$option_betonnage',egout='$option_egout',fosse='$option_fosse',tauxdommage='$option_taux_dommage',trottoir='$trottoir',code_remise='$code_remise' where id=1 ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="Les options générales ont été Mises à jours";
      
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }
        header("Location:../views/menu.php");
}
?>