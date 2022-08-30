<?php
  if(!isset($_SESSION)) 
  { 
      session_start(); 
  }
class Option
{
	private $Nom;
    private $sous_groupe;

    private $no_quantite;
    private $tarif_plan;
    private $cout;
    
    public function Option($Nom,$sous_groupe,$no_quantite,$tarif_plan,$cout)
    {  
   			$this->Nom = $Nom;
            $this->sous_groupe = $sous_groupe;
            $this->no_quantite = $no_quantite;
   			
            $this->tarif_plan = $tarif_plan;	
            $this->cout = $cout;	
    }
    public static  function Optionvide()
    {  
   			$this->Nom ="";
   			$this->sous_groupe = "";
   			$this->nom_corps_metier ="";	
       
               
  
        
    }

    public function isnew()
    {
        
        $ql = "SELECT * FROM options WHERE nom='$this->Nom';" ;
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
                    $ql = "INSERT INTO options (nom,sous_groupe,tariflieplan,sans_quantite,cout) VALUES "
                        . "('$this->Nom','$this->sous_groupe','$this->tarif_plan','$this->no_quantite','$this->cout');" ;

                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                
                    $_SESSION['resultquery']="L'option a été Enregistré ";
                    $dbCon=null; 
                    return true;

                } catch (Exception $ex) {
                    $_SESSION['resultquery']= "Erreur: L'option n'a pas été Enregistré ".$ex;
                    $dbCon=null; 
                    return false;
                }                             
           
        }
         else { $dbCon=null;
            $_SESSION['resultquery']="Erreur: le nom de l'option est déjà utilisé";}

    }

 
     
     public function getall()
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM options ";
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
     public function getopt($id)
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM options where id='$id' ";
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
      public function delete($OptionToDelete)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "DELETE FROM options where id='$OptionToDelete' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="L'Option a été Supprimé ";
      
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
                  
            $ql = "UPDATE  options SET nom='$Nom',sous_groupe='$sous_groupe',sans_quantite='$no_quantite',tariflieplan='$tarif_plan',cout='$cout' where id='$id' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="L'Option a été Mise à jour ";
      
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