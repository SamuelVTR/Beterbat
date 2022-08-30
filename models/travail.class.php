<?php
  if(!isset($_SESSION)) 
  { 
      session_start(); 
  }
class Travail
{
	private $Nom;
    private $Remarque;
    private $nom_corps_metier;
    private $nom_regroupement;
    private $charge_client;
    private $cout;
    
    public function Travail($Nom,$Remarque,$nom_corps_metier,$nom_regroupement,$charge_client,$cout)
    {  
   			$this->Nom = $Nom;
            $this->Remarque = $Remarque;
            $this->nom_regroupement = $nom_regroupement;
   			$this->nom_corps_metier = $nom_corps_metier;	
            $this->charge_client = $charge_client;	
            $this->cout = $cout;	
    }
    public static  function Travailvide()
    {  
   			$this->Nom ="";
   			$this->Remarque = "";
   			$this->nom_corps_metier ="";	
       
               
  
        
    }

    public function isnew()
    {
        
        $ql = "SELECT * FROM referentiel_travaux WHERE nom_travail='$this->Nom';" ;
        include("../controlleurs/conex.php");
        try{  
                //retourner true si le Corp_métiers est nouveau
        	 	$prepTest = $dbCon->prepare($ql);
             	$prepTest->execute();
                if($prepTest->rowCount() > 0)
                {
                        return false;
                }
               else {return true;}
                }
        catch(Exception $ex){
                 return "Erreur:  ".$ex;
        }
    }

    public function save()
    {
        if( $this->isnew())
        {             
            //Inserer dans la BD
            include("../controlleurs/conex.php");
                try {                   
                    $ql = "INSERT INTO referentiel_travaux (nom_corps_metier, nom_travail, caracteristique,chargeclient,nom_regroupement,cout) VALUES "
                        . "('$this->nom_corps_metier', '$this->Nom', '$this->Remarque', '$this->charge_client', '$this->nom_regroupement', '$this->cout');" ;

                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                
                    $_SESSION['resultquery']="Le Travail a été Enregistré ";
                    $dbCon=null; 
                    return true;

                } catch (Exception $ex) {
                    $_SESSION['resultquery']= "Erreur: Le Travail n'a pas été Enregistré ".$ex;
                    $dbCon=null; 
                    return false;
                }                             
           
        }
         else { $dbCon=null;
            $_SESSION['resultquery']="Erreur: le nom du Travail est déjà utilisé";}

    }

 
     
     public function getall()
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM referentiel_travaux ";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
            $result = $prepTest->fetchAll();     
          
            return $result;
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }




     }
     public function gettravaux($id)
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM referentiel_travaux where id='$id' ";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
            $result = $prepTest->fetchAll();     
          
            return $result;
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }




     }
      public function delete($travailToDelete)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "DELETE FROM referentiel_travaux where nom_travail='$travailToDelete' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="Le Travail a été Supprimé ";
      
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }
     }

}


?>