<?php
  if(!isset($_SESSION)) 
  { 
      session_start(); 
  }
class corps_de_metier
{
	private $Nom;
    private $Remarque;
    private $charge_client;
  
    
    public function corps_de_metier($Nom,$Remarque,$charge_client)
    {  
   			$this->Nom = $Nom;
   			$this->Remarque = $Remarque;
   			$this->charge_client = $charge_client;	
       
               
    }
    public static  function corps_de_metiervide()
    {  
   			$this->Nom ="";
   			$this->Remarque = "";
   			$this->charge_client ="";	
       
               
  
        
    }

    public function isnew()
    {
        
        $ql = "SELECT * FROM corps_de_metier WHERE nom_corps_metier='$this->Nom';" ;
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
                    $ql = "INSERT INTO corps_de_metier (nom_corps_metier, charge_client, remarque) VALUES "
                        . "('$this->Nom', '$this->charge_client', '$this->Remarque');" ;

                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                
                    $_SESSION['resultquery']="Le Corps de métier a été enregistré ";
                    $dbCon=null; 
                    return true;

                } catch (Exception $ex) {
                    $_SESSION['resultquery']= "Erreur: Le Corp_métiers n'a pas été enregistré ".$ex;
                    $dbCon=null; 
                    return false;
                }                             
           
        }
         else { $dbCon=null;
            $_SESSION['resultquery']="Erreur: le nom du Corps de métier est déjà utilisé";}

    }

     //fonction pour se logger
     public function logger()
     {     
         include("../controlleurs/conex.php");
        // $dbCon->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  
       try {            
             $ql = "SELECT * FROM corps_de_metier WHERE charge_client ='$this->charge_client' AND password= '$this->motPasse' ";
             $prepTest = $dbCon->prepare($ql);
             $prepTest->execute();
             if($prepTest->rowCount()==1)
             {
                 $user =$prepTest->fetch(PDO::FETCH_ASSOC);
                 $this->Nom= $user['Nom'];
        
                 $this->charge_client = $user['charge_client'];
     
                 $this->Remarque = $user['Remarque'];
                 $corps_de_metier=new corps_de_metier($user['Nom'],$user['charge_client'],$user['password'],$user['Remarque']);
                 //$dbCon=null;
                 $this->message = "corps_de_metier autentifie";
                 
                 $_SESSION['user']=serialize($corps_de_metier);
                 return  true;         
             }                 
               else { 
                 //$dbCon=null;
                 $this->message = "Erreur: Le nom d'corps_de_metier ou le mot de passe n'est pas correct";
                 return false; } 
         } catch (Exception $ex) {
            //$dbCon=null;
             $e = oci_error();
             trigger_error(htmlentities($e['message']), E_USER_ERROR);
             return false; 
         }
     }
     public function getall()
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM corps_de_metier ";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
            $result = $prepTest->fetchAll();     
            $_SESSION['corps_metier']=serialize($result);
            return $result;
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