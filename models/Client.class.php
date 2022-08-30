<?php
  if(!isset($_SESSION)) 
  { 
      session_start(); 
  }
class Client
{
	private $Nom;
    private $Adresse;
    private $civilite;
    private $code_postal;
    private $ville;
    private $pays;
    private $Ref_cadastral;
    private $Adresse_terrain;
    
    private $indice;

    public function Client($Nom,$civilite,$Adresse,$code_postal,$ville,$pays,$Ref_cadastral,$Adresse_terrain,$ville_terrain)
    {  
   			$this->Nom = $Nom;
            $this->Adresse = $Adresse;
            $this->code_postal = $code_postal;
   			
            $this->ville = $ville;	
            $this->pays = $pays;	
            $this->Ref_cadastral =$Ref_cadastral;
            $this->Adresse_terrain=  $Adresse_terrain;
            $this->ville_terrain=  $ville_terrain;
            $this->civilite=  $civilite;
         
    }


    public function isnew()
    {
        
        $ql = "SELECT * FROM client WHERE Nom='$this->Nom';" ;
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
                    $ql = "INSERT INTO client (Nom,civilite,Adresse,codepostal,Ville,Pays,ref_cadastral,adresse_terrain,ville_terrain) VALUES "
                        . "('$this->Nom','$this->civilite','$this->Adresse','$this->code_postal','$this->ville','$this->pays','$this->Ref_cadastral','$this->Adresse_terrain','$this->ville_terrain');" ;

                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                
                  //  $_SESSION['resultquery']="Le client a été Enregistré ";
                    $dbCon=null; 
                    return true;

                } catch (Exception $ex) {
                  //  $_SESSION['resultquery']= "Erreur: Le Client n'a pas été Enregistré ".$ex;
                    $dbCon=null; 
                    return false;
                }                             
           
        }
         else { $dbCon=null;
            $_SESSION['resultquery']="Erreur: le Nom du client est déjà utilisé";}

    }
    public function addindice($nom)
    {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "UPDATE  Client set indice=indice+1 where Nom='$nom' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="Le client a été Mise à jour ";
      
        } 
        catch (Exception $ex) 
        {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }
    }

    public function getindice($nom)
    {
       include("../controlleurs/conex.php");
       try {      
                 
           $ql = "SELECT indice FROM client where Nom='$nom';";
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

     
     public function getall()
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM client ";
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


     public function getclient($nom)
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM client where Nom='$nom'";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
            $result = $prepTest->fetchAll();     
          
            return $result;
        } 
        catch (Exception $ex) 
        {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }




     }


      public function delete($ClientToDelete)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "DELETE FROM client where id='$ClientToDelete' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="Le client a été Supprimé ";
      
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }
     }
     public function update($id,$Nom,$civilite,$Adresse,$code_postal,$ville,$pays,$Ref_cadastral,$Adresse_terrain,$ville_terrain,$indice)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "UPDATE  Client SET Nom='$Nom',civilite='$civilite',Adresse='$Adresse',codepostal='$code_postal',Ville='$ville',Pays='$pays',ref_cadastral='$Ref_cadastral',adresse_terrain='$Adresse_terrain',ville_terrain='$ville_terrain',indice='$indice' where id='$id' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="Le client a été Mise à jour ";
      
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