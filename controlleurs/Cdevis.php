<?php
session_start();
include_once "conex.php";
include_once "../models/Client.class.php";
include_once "../models/Devis.class.php";
include_once "../models/Options.class.php";
include_once "../controlleurs/conex.php";
include_once "../models/Corps_de_metier.class.php";
include_once "../models/Regroupement.class.php";
include_once "../models/plan_type.class.php";
if(isset($_POST['btn_add_Client']))  
{            
    
        $Nomclient = $_POST['Nomclient'];
        $Civilite = $_POST['Civilite'];
       $Adresse=$_POST['Adresse'];

       $codepostal = $_POST['codepostal'];
       $Ville = $_POST['Ville'];
      $Pays=$_POST['Pays'];
      $Villeterrain = $_POST['Villeterrain'];
      $Adressterrain = $_POST['Adressterrain'];
     $Refcadastral=$_POST['Refcadastral'];
      
        //cr�er un client juste avec login et password, pour utiliser la fonction login
    
        $client = new Client($Nomclient,$Civilite,$Adresse,$codepostal,$Ville,$Pays,$Refcadastral,$Adressterrain,$Villeterrain);
        $result = $client->save();
        
    header("Location:../views/Vdevis.php");
  
}

if(isset($_POST['deletepdf']))  
{
    $Devis = new Devis("","","","","","","","","","","","");
     //permet dempecher louverture du modal pour afficher un pdf l'or de la suppression
 unset($_SESSION['resultquery']);
 $Devis->delete($_POST['deletepdf']);

 header("Location:../views/Vdevis.php");
}
if(isset($_POST['devispdf']))  
{
    $Devis = new Devis("","","","","","","","","","","","");
$Devis= $Devis->get($_POST['devispdf']);

$_SESSION["devis"]=$Devis;
$_SESSION['resultquery']="<object hidden  type='application/pdf'>
<iframe src='createpdf.php' ></iframe>
</object>";
header("Location:../views/Vdevis.php");
}
if(isset($_POST['adddev']))  
{            
    $plan_type=$_POST['selPlantype'];
    $client=$_POST['selclient'];


    $_SESSION["plan_type"]=$plan_type;
    $_SESSION["client"]=$client ;

    include("../controlleurs/conex.php");
    try {      
              
        $ql = "SELECT * FROM divers";
        $prepTest = $dbCon->prepare($ql);
        $prepTest->execute();
        $result = $prepTest->fetchAll();  
        
        $_SESSION["divers"]=$result;
        
        header("Location:../views/Vadd_devis.php");
    
    }
    catch (Exception $ex) {
        //$dbCon=null;
         $e = oci_error();
         trigger_error(htmlentities($e['message']), E_USER_ERROR);
         $_SESSION['resultquery']=$ex;
         return false; 
     }
    header("Location:../views/Vadd_devis.php");
}





if(isset($_POST['savedevis']))  
{  

    $client = new Client("","","","","","","","","");
    $indice=$client->getindice($_POST['nomclient']);


    $Date=$_POST['Date']."+".$indice+1;
    $user=$_POST['user'];
    $Plan=$_POST['Plan'];
    $Client=$_POST['nomclient'];
    $type_terrain=$_POST['type_terrain'];


    $fondation=$_POST['fondation'];
    $semelle_fillante=$_POST['semelle_fillante'];
    $radier=$_POST['radier'];
    $videsanitaire=$_POST['videsanitaire'];

    $arrayofadaptationSol=array();
    $arrayofadaptationSol[]=$type_terrain;
    $arrayofadaptationSol[]=$fondation;
    $arrayofadaptationSol[]=$semelle_fillante;
    $arrayofadaptationSol[]=$radier;
    $arrayofadaptationSol[]=$videsanitaire;

    $arrayofamenagement=array();
    $arrayofacces=array();
    $arrayoftrottoir=array();
    if(isset($_POST['amenagement']))
        {
            
            if($_POST['amenagement']=="rdiencaillasement")
            {
               
                $arrayofacces[]="encaillassement";
                $arrayofacces[]=$_POST['largeurencaill'];
                $arrayofacces[]=$_POST['longueurencaill'];
                $arrayofacces[]=$_POST['prixMencaill'];
                $arrayofacces[]=$_POST['totalencaill'];

             
            }
            else if($_POST['amenagement']=="rdibeton")
            {
                $arrayofacces[]="betonnage";
                $arrayofacces[]=$_POST['largeurbetonnage'];
                $arrayofacces[]=$_POST['longueurbetonnage'];
                $arrayofacces[]=$_POST['prixMbetonnage'];
                $arrayofacces[]=$_POST['totalbétonnage'];
            }
            else
            {
                $arrayofacces[]="aucun";
            }

        }
        if(isset($_POST['opttrottoir']))
        {
            $arrayoftrottoir[]=$_POST['largtro'];
            $arrayoftrottoir[]=$_POST['longtro'];
            $arrayoftrottoir[]=$_POST['prixtro'];
            $arrayoftrottoir[]=$_POST['prixtrototal'];
        }
        else{
            $arrayoftrottoir[]="aucun";
        }
        $arrayofamenagement[]=$arrayoftrottoir;
        $arrayofamenagement[]=$arrayofacces;

     




  

// recuperations des optios selectionner et mise en tableau pour stockage dans la base de donnée
    $options = new Option("","","","","");

    $Alloption = $options->getall();
    $arrayofoption=array();
          
        foreach($Alloption as $option)
        {
         
        if(isset($_POST[$option[0]."chc"]))
        {

            $arrayofoption[]=array($option[0],$_POST["nbopt".$option[0]],$_POST["totalopt".$option[0]]);
        }


        }


        // recuperations des Corps_de_metier a la charge du client  selectionner et mise en tableau pour stockage dans la base de donnée
        $Corps_de_metier = new Corps_de_metier("","","");

        $Allmetier = $Corps_de_metier->getall();
        $Arrayofmetier=array();

    foreach($Allmetier as $metier)
        {
            $Lemetier=str_replace(' ','_',$metier[0]);
           if(isset($_POST["metier".$Lemetier]))
           {

            $Arrayofmetier[]=array($metier[0],$_POST["prixmetier".$Lemetier]);
           }

        }

        if(isset($_POST["fosse"]))
           {
               $assainissement="fosse-".$_POST["fosse"];
           }
        else
        {
            $assainissement="egout-".$_POST["egout"];

        }
        
        //permet de concatener la remise si il y en a une 
        if(isset($_POST["chcremise"]))
        {
            $totalttc=$_POST["totalttc"]."/".$_POST["prixremise"];
        }
        else{
            $totalttc=$_POST["totalttc"]."/"."0";
        }
        
        //permet dajouter loption de lotissement si il y en a un
        if(isset($_POST["chclotissement"]))
        {
            $lotissement=1;
        }
        else{
            $lotissement=0;
        }


        $pdf=$Date."_".$Plan."_".$Client."_".rand();
        $Devis = new devis($Date,$Plan,$Client,$user,json_encode($arrayofadaptationSol),json_encode($arrayofamenagement),json_encode($arrayofoption),json_encode($Arrayofmetier),$assainissement,$totalttc,$pdf,$lotissement);
        $Devis->save();

        $_SESSION["devis"]=$Devis->getlast();

        $Clients = new Client("","","","","","","","","");
        $Clients->addindice($_SESSION["devis"][0]["client"]);
    header("Location:../views/createpdf.php");

}

?>