<?php
session_start();
include_once "conex.php";
include_once "../models/plan_type.class.php";
include_once "../controlleurs/conex.php";
                            include_once "../models/Corps_de_metier.class.php";
                            include_once "../models/Regroupement.class.php";
                            include_once "../models/Travail.class.php";
                            include_once "../models/Options.class.php";
if(isset($_POST['btn_add_plan']))  
{            
    
        $nom_plan = $_POST['nom_plan'];
        $Description = $_POST['Description'];
       $type=$_POST['type'];
    
       $TarifHT=$_POST['TarifHT'];
     $sousgroupe = 1;
       $Enveloppe= $_POST['Enveloppe'];
       $fondation= $_POST['fondation'];
       $semelle_fillante= $_POST['semelle_fillante'];
       $radier= $_POST['radier'];
       $Videsanitaire= $_POST['Videsanitaire'];
      $Arraytofoptionspe=array();
      $Arraytofoptionspe=[$fondation,$semelle_fillante,$radier,$Videsanitaire];
        $Travail = new Travail("","","","","","");
        $Alltravail = $Travail->getall();
        


        $Arraytoftravaux=array();
        $Arraytqte=array();
        $ArrayQteEtTrav=array();
        foreach($Alltravail as $travail)
        {
            $travSelect=str_replace(' ','*@!+-',$travail[6]);
           if(isset($_POST[$travSelect])){

            $Arraytoftravaux[]=str_replace('*@!+-',' ',$travSelect);

            if(isset($_POST["qte".$travSelect])){

                if($_POST["qte".$travSelect]==""){
                    $Arraytqte[]="X";
                }
                else{
                    $Arraytqte[]=$_POST["qte".$travSelect];
                }
               

            }
           
         
           }
            
        }

        $ArrayQteEtTrav[]=$Arraytoftravaux;
        $ArrayQteEtTrav[]=$Arraytqte;
        

        $Corps_de_metier = new Corps_de_metier("","","");

        $Allmetier = $Corps_de_metier->getall();
        $Arrayofmetier=array();

    foreach($Allmetier as $metier)
        {
            $charclient="charclient".str_replace(' ','*@!+-',$metier[0]);
           if(isset($_POST[$charclient])){

            $Arrayofmetier[]=array($metier[0],$_POST["coutmetier".str_replace(' ','*@!+-',$metier[0])]);
           }

        }


        $options = new Option("","","","","");

        $Alloption = $options->getall();
        $arrayofoption=array();
              
    foreach($Alloption as $option)
    {
        
       if(isset($_POST[$option[0]])){

        $arrayofoption[]=array($option[0],$_POST["coutoption".$option[0]]);
       }

    }

    $file=$_FILES['myfile'];

    $file_name=$file['name'];
    $file_tmp=$file['tmp_name'];
    $file_size=$file['size'];
    $file_error=$file['error'];

    $file_ext=explode('.',$file_name);
    $file_ext=strtolower(end($file_ext));

    $allowed = array('pdf');

    

    
 
    if(in_array($file_ext,$allowed))
    {
        if($file_error===0)
        {
            if($file_size<= 10000000 )
            {
                $file_name_new =uniqid('',true).'.'.$file_ext;
                $file_destination= '../uploads/'.$file_name_new;
                $plan = new plan($nom_plan,$Description,$type."/".$Enveloppe,$TarifHT,$sousgroupe,json_encode($ArrayQteEtTrav),json_encode($Arrayofmetier),$file_destination,json_encode($Arraytofoptionspe),json_encode($arrayofoption));
                if ($plan->save())
                {


              
                            if (move_uploaded_file($file_tmp, $file_destination)) 
                            {
                                header("Location:../views/Vplan_type.php");
                                }
                }
                else{
                    header("Location:../views/Vplan_type.php");
                }
            }
        }
    }
    else{
    header("Location:../views/Vadd_plan_type.php");
    $_SESSION['resultquery']="Erreur: Le fichier PDF n'a pas pu être chargé";
    }
   
    
  
}

if(isset($_POST['btn_update_options']))  
{            
    
        $nom = $_POST['nomOptionupdate'];
        $id = $_POST['idOptionupdate'];
        $selOptions = $_POST['selOptionsupdate'];
       $cout=$_POST['coutupdate'];



        if(isset($_POST['no_quantitéupdate']))
        {
            $no_quantité = $_POST['no_quantitéupdate'];
        }
        else{
            $no_quantité="0";
        }
        if(isset($_POST['tarif_planupdate']))
        {
            $tarif_plan = $_POST['tarif_planupdate'];
        }
        else{
            $tarif_plan="0";
        }
        //cr�er un client juste avec login et password, pour utiliser la fonction login
    
        $option = new Option($nom,$selOptions,$no_quantité,$tarif_plan,$cout);
        $result = $option->update($id,$nom,$selOptions,$no_quantité,$tarif_plan,$cout);
        
    header("Location:../views/options.php");
  
}
if(isset($_POST['btn_delete_options']))  
{
    
    $id = $_POST['idOptionupdate'];
    $option = new Option("","","","","");
    $result = $option->delete($id);
    header("Location:../views/options.php");
}

if(isset($_POST['btnsavenotice'])) 
{
    
    $file=$_FILES['myfile'];

    $file_name=$file['name'];
    $file_tmp=$file['tmp_name'];
    $file_size=$file['size'];
    $file_error=$file['error'];

    $file_ext=explode('.',$file_name);
    $file_ext=strtolower(end($file_ext));

    $allowed = array('pdf');

    

    
 
    if(in_array($file_ext,$allowed))
    {
        if($file_error===0)
        {
            if($file_size<= 10000000 )
            {
                $file_name_new ='notice.'.$file_ext;
                $file_destination= '../uploads/'.$file_name_new;
               
                include("../controlleurs/conex.php");
                try {                   
                    $ql = "UPDATE  divers SET notice='$file_destination';";
            
                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                    $_SESSION['resultquery']="La notice a été enregistrée ";
                    $dbCon=null; 
                   
            
                } catch (Exception $ex) {
                    $_SESSION['resultquery']= "Erreur: La notice n'a pas été enregistrée suite à une erreur... Réessayé ".$ex;
                    $dbCon=null; 
                   
                }   

              
                            if (move_uploaded_file($file_tmp, $file_destination)) 
                            {
                                header("Location:../views/Vplan_type.php");
                                }
                
            }
        }
    }



 

    header("Location:../views/Vplan_type.php");
}
if(isset($_POST['plan_update']))  
{            
    $plan = new plan("","","","","","","","","","");
    $plan= $plan->getplanid($_POST['plan_update']);
    
    $_SESSION["plantoupdate"]=$plan;
   
    header("Location:../views/vupdatePlan.php");
}
?>