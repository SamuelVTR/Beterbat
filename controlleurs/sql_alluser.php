<?php
 include_once "../models/utilisateur.class.php";
 
try {      
    include("conex.php");
        $ql =  "SELECT * FROM utilisateur ";
       
        $prepTest = $dbCon->prepare($ql);
        $prepTest->execute();
        $utilisateurInfo=$prepTest->fetchAll(PDO::FETCH_ASSOC);
       
       
        //met les utilisateur de categrie livre dans un tableau 
      
        //le tableau de livre est mis dans une session
         
        echo "{ \"records\":" . json_encode($utilisateurInfo). "}";


    }
catch(Exception $ex)
    {
        return "Erreur";
    }


?>