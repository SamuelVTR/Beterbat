<?php
session_start();
include_once "conex.php";
include_once "../models/Corps_de_metier.class.php";
include_once "../models/Regroupement.class.php";
include_once "../models/Travail.class.php";
function apostrophe($text) {
    $text =  str_replace('\'', '\'\'', $text);

    return $text;
}
if(isset($_POST['btn_add_corps_métier']))  
{            
    
        $nom =apostrophe($_POST['nom']);
        $rmq = apostrophe($_POST['rmq']);
    
        if(isset($_POST['charge_client']))
        {
            $charge_client = $_POST['charge_client'];
        }
        else{
            $charge_client="0";
        }
        //cr�er un client juste avec login et password, pour utiliser la fonction login
        $Corps_de_metier = new Corps_de_metier($nom,$rmq,$charge_client);
        $result = $Corps_de_metier->save();
        
    header("Location:../views/vreferentiel-travaux_corps-metier.php");
  
}


if(isset($_POST['btn_add_regroupement']))  
{            
    
        $nomRegroupement = apostrophe($_POST['nomRegroupement']);
        $rmqRegroupement = apostrophe($_POST['rmqRegroupement']);
        $selCorpsMetier=$_POST['selCorpsMetier'];
     
        //cr�er un client juste avec login et password, pour utiliser la fonction login
        $Regroupement = new Regroupement($nomRegroupement,$rmqRegroupement,$selCorpsMetier);
        $result = $Regroupement->save();
        
    header("Location:../views/vreferentiel-travaux_corps-metier.php");
  
}
if(isset($_POST['btn_delete_travail']))  
{
    $nomTravail = $_POST['nomTravaildelete'];

    $travail = new travail("","","","","","");
    $result = $travail->delete($nomTravail);
    header("Location:../views/vreferentiel-travaux_corps-metier.php");
}
if(isset($_POST['btn_add_travail']))  
{            
    
        $nomTravail = apostrophe($_POST['nomTravail']);
        $rmqTravail = apostrophe($_POST['rmqTravail']);
        
        $seltravail=apostrophe($_POST['seltravail']);
        if(isset($_POST['charclient']))
        {
            $charclient=1;
            if($_POST['coutTravailloti']!="")
            {
                $couttravail=$_POST['coutTravail']."/".$_POST['coutTravailloti'];
            }
            else
            {
                $couttravail=$_POST['coutTravail'];
            }
            
        }
        else{
            $charclient="0";
            $couttravail="0";
        }
  
        //trouver le metier du groupement 
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM regroupement where nom_regroupement='$seltravail' ";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
            $nom_metier = $prepTest->fetchAll();     
          

           
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
       
        }
//*********************************** */
        $travail = new travail($nomTravail,$rmqTravail,$nom_metier[0][0],$seltravail, $charclient,$couttravail);
        $result = $travail->save();
        
    header("Location:../views/vreferentiel-travaux_corps-metier.php");
  
}
?>