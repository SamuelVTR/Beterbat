<?php
  if(!isset($_SESSION)) 
  { 
      session_start(); 
  }
class Divers
{
	private $Nom;
    private $sous_groupe;

    private $no_quantite;
    private $tarif_plan;
    private $cout;
    
    public function Divers($Nom,$sous_groupe,$no_quantite,$tarif_plan,$cout)
    {  
   			$this->Nom = $Nom;
            $this->sous_groupe = $sous_groupe;
            $this->no_quantite = $no_quantite;
   			
            $this->tarif_plan = $tarif_plan;	
            $this->cout = $cout;	
    }
    public static  function Diversvide()
    {  
   			$this->Nom ="";
   			$this->sous_groupe = "";
   			$this->nom_corps_metier ="";	
       
               
  
        
    }

    public function isnew()
    {
        
        $ql = "SELECT * FROM Diverss WHERE nom='$this->Nom';" ;
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
                    $ql = "INSERT INTO Diverss (nom,sous_groupe,tariflieplan,sans_quantite,cout) VALUES "
                        . "('$this->Nom','$this->sous_groupe','$this->tarif_plan','$this->no_quantite','$this->cout');" ;

                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                
                    $_SESSION['resultquery']="L'Divers a été Enregistré ";
                    $dbCon=null; 
                    return true;

                } catch (Exception $ex) {
                    $_SESSION['resultquery']= "Erreur: L'Divers n'a pas été Enregistré ".$ex;
                    $dbCon=null; 
                    return false;
                }                             
           
        }
         else { $dbCon=null;
            $_SESSION['resultquery']="Erreur: le nom de l'Divers est déjà utilisé";}

    }

 
     
     public function getall()
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM Diverss ";
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

      public function delete($DiversToDelete)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "DELETE FROM Diverss where id='$DiversToDelete' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="L'Divers a été Supprimé ";
      
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
                  
            $ql = "UPDATE  Diverss SET nom='$Nom',sous_groupe='$sous_groupe',sans_quantite='$no_quantite',tariflieplan='$tarif_plan',cout='$cout' where id='$id' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="L'Divers a été Mise à jour ";
      
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }
     }

}
