<?php
  if(!isset($_SESSION)) 
  { 
      session_start(); 
  }
class plan
{
	private $Nom_plan;
    private $sous_groupe;

    private $Branchement;
    private $prix_HT;
    private $Description;
    private $Travaux;
    private $cout_par_corps_metier;
    private $pdf;
    private $cout_option_specifique;
    private $options;
    public function plan($Nom_plan,$Description,$Branchement,$prix_HT,$sous_groupe,$Travaux,$cout_par_corps_metier,$pdf,$cout_option_specifique,$options)
    {  
        $this->Nom_plan =$Nom_plan;
        $this->sous_groupe =$sous_groupe ;
        $this->Branchement = $Branchement;
        $this->Description =$Description;
        $this->prix_HT =$prix_HT;
        $this->Travaux =$Travaux;
        $this->cout_par_corps_metier =$cout_par_corps_metier;
        $this->pdf = $pdf;
        $this->cout_option_specifique =$cout_option_specifique;
        $this->options =$options;
    }


    public function isnew()
    {
        
        $ql = "SELECT * FROM plan WHERE Nom_plan='$this->Nom_plan';" ;
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
                    $ql = "INSERT INTO plan (Nom_plan,Description,Branchement,prix_HT,Sous_groupe,Travaux,cout_par_corps_metier,pdf,cout_option_specifique,options) VALUES "
                        . "('$this->Nom_plan','$this->Description','$this->Branchement','$this->prix_HT','$this->sous_groupe','$this->Travaux','$this->cout_par_corps_metier','$this->pdf','$this->cout_option_specifique','$this->options');" ;

                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                
                    $_SESSION['resultquery']="Le plan a été Enregistré ";
                    $dbCon=null; 
                    return true;

                } catch (Exception $ex) {
                    $_SESSION['resultquery']= "Erreur: Le plan n'a pas été Enregistré ".$ex;
                    $dbCon=null; 
                    return false;
                }                             
           
        }
         else { $dbCon=null;
            $_SESSION['resultquery']="Erreur: le nom du plan est déjà utilisé";}

    }

 
     
     public function getall()
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM plan";
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
     public function getplan($nomplan)
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM plan where Nom_plan ='$nomplan';";
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
     public function getplanid($id)
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM plan where Id ='$id';";
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
      public function delete($planToDelete)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "DELETE FROM plan where id='$planToDelete' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="Le plan a été Supprimé ";
      
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }
     }
     public function update($id,$Nom,$sous_groupe,$no_quantite,$tarif_plan,$cout)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "UPDATE  plans SET nom='$Nom',sous_groupe='$sous_groupe',sans_quantite='$no_quantite',tariflieplan='$tarif_plan',cout='$cout' where id='$id' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="Le plan a été Mise à jour ";
      
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