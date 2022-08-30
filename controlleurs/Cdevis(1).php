<?php
session_start();
include_once "conex.php";
include_once "../models/plan_type.class.php";
include_once "../controlleurs/conex.php";
                            include_once "../models/Corps_de_metier.class.php";
                            include_once "../models/Regroupement.class.php";
                            include_once "../models/Travail.class.php";
                            include_once "../models/Options.class.php";
if(isset($_POST['btn_creer_devis']))  
{            
    $plan_type=$_POST['selPlantype'];
    $client=$_POST['selclient'];


    $_SESSION["plan_type"]=$plan_type;
    $_SESSION["client"]=$client ;
    header("Location:../views/Vadd_devis.php");
}

?>