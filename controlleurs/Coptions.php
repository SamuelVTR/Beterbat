<?php
session_start();
include_once "conex.php";
include_once "../models/Options.class.php";
if(isset($_POST['btn_add_options']))  
{            
    
        $nom = $_POST['nomOption'];
        $selOptions = $_POST['selOptions'];
       $cout=$_POST['cout'];



        if(isset($_POST['no_quantité']))
        {
            $no_quantité = $_POST['no_quantité'];
        }
        else{
            $no_quantité="0";
        }
        if(isset($_POST['tarif_plan']))
        {
            $tarif_plan = $_POST['tarif_plan'];
        }
        else{
            $tarif_plan="0";
        }
        //cr�er un client juste avec login et password, pour utiliser la fonction login
    
        $option = new Option($nom,$selOptions,$no_quantité,$tarif_plan,$cout);
        $result = $option->save();
        
    header("Location:../views/options.php");
  
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

?>