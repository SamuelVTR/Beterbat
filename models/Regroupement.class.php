<?php
  if(!isset($_SESSION)) 
  { 
      session_start(); 
  }
class Regroupement
{
	private $Nom;
    private $Remarque;
    private $nom_corps_metier;
  
    
    public function Regroupement($Nom,$Remarque,$nom_corps_metier)
    {  
   			$this->Nom = $Nom;
   			$this->Remarque = $Remarque;
   			$this->nom_corps_metier = $nom_corps_metier;	
       
               
    }
    public static  function Regroupementvide()
    {  
   			$this->Nom ="";
   			$this->Remarque = "";
   			$this->nom_corps_metier ="";	
       
               
  
        
    }

    public function isnew()
    {
        
        $ql = "SELECT * FROM Regroupement WHERE nom_regroupement='$this->Nom';" ;
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
                    $ql = "INSERT INTO regroupement (nom_corps_metier, nom_regroupement, remarque) VALUES "
                        . "('$this->nom_corps_metier', '$this->Nom', '$this->Remarque');" ;

                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                
                    $_SESSION['resultquery']="Le Regroupement a été enregistré ";
                    $dbCon=null; 
                    return true;

                } catch (Exception $ex) {
                    $_SESSION['resultquery']= "Erreur: Le Regroupement n'a pas été enregistré ".$ex;
                    $dbCon=null; 
                    return false;
                }                             
           
        }
         else { $dbCon=null;
            $_SESSION['resultquery']="Erreur: le nom du Regroupement est déjà utilisé";}

    }

     //fonction pour se logger
     public function logger()
     {     
         include("../controlleurs/conex.php");
        // $dbCon->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  
       try {            
             $ql = "SELECT * FROM Regroupement WHERE nom_corps_metier ='$this->nom_corps_metier' AND password= '$this->motPasse' ";
             $prepTest = $dbCon->prepare($ql);
             $prepTest->execute();
             if($prepTest->rowCount()==1)
             {
                 $user =$prepTest->fetch(PDO::FETCH_ASSOC);
                 $this->Nom= $user['Nom'];
        
                 $this->nom_corps_metier = $user['nom_corps_metier'];
     
                 $this->Remarque = $user['Remarque'];
                 $Regroupement=new Regroupement($user['Nom'],$user['nom_corps_metier'],$user['password'],$user['Remarque']);
                 //$dbCon=null;
                 $this->message = "Regroupement autentifie";
                 
                 $_SESSION['user']=serialize($Regroupement);
                 return  true;         
             }                 
               else { 
                 //$dbCon=null;
                 $this->message = "Erreur: Le nom d'Regroupement ou le mot de passe n'est pas correct";
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
                  
            $ql = "SELECT * FROM regroupement ";
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
     
}


?>