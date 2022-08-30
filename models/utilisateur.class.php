<?php
class Utilisateur
{
	private $id;
    private $login;
    private $initial;
    private $nom;
    private $motPasse;
    private $statut;
    
    public function Utilisateur($login,$statut,$passw,$initial,$nom)
    {  
   			
   			$this->statut = $statut;
   			$this->login = $login;	
       		$this->password =$passw;
               $this->initial =$initial;
               $this->nom =$nom;
  
                //connecter au serveur
       // $dbCon = new PDO($this->DBinfo, $this->U, $this->P);
        //afficher tous les erreurs, pour la classe PDO
       // $dbCon->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
    }

    public function isnew()
    {
        
        $ql = "SELECT * FROM utilisateur WHERE login='$this->login';" ;
        include("../controlleurs/conex.php");
        try{  
                //retourner true si le utilisateurs est nouveau
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
                    $ql = "INSERT INTO utilisateur ( login, statut, password,initial,nom) VALUES "
                        . "('$this->login', '$this->statut', '$this->password', '$this->initial', '$this->nom');" ;

                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                    $_SESSION['resultquery']="L'utilisateurs a été enregistré ";
                    $dbCon=null; 
                    return true;

                } catch (Exception $ex) {
                    $_SESSION['resultquery']= "Erreur: Le utilisateurs n'a pas été enregistré ".$ex;
                    $dbCon=null; 
                    return false;
                }                             
           
        }
         else { $dbCon=null;
            $_SESSION['resultquery'] ="Erreur: le nom d'utilisateur est déjà utilisé";}

    }

     //fonction pour se logger
     public function logger()
     {     
         include("../controlleurs/conex.php");
        // $dbCon->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  
       try {            
             $ql = "SELECT * FROM utilisateur WHERE login ='$this->login' AND password= '$this->password' ";
             $prepTest = $dbCon->prepare($ql);
             $prepTest->execute();
             if($prepTest->rowCount()==1)
             {
                 $user =$prepTest->fetch(PDO::FETCH_ASSOC);
         
                 
                 $_SESSION['user']= $user['statut'];;
                 return  true;         
             }                 
               else { 
                 //$dbCon=null;
                 $this->message = "Erreur: Le nom d'utilisateur ou le mot de passe n'est pas correct";
                 return false; } 
         } catch (Exception $ex) {
            //$dbCon=null;
             $e = oci_error();
             trigger_error(htmlentities($e['message']), E_USER_ERROR);
             return false; 
         }
     }
     public function get_Statut()
     { 
         return $this->statut;
     }
     public function getall()
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM utilisateur ";
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
     public function delete($userTodelete)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "DELETE FROM utilisateur where id='$userTodelete' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="L'utilisateur a été Supprimé ";
      
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }
     }
     public function update($id,$login,$statut,$password,$initial,$nom)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "UPDATE  utilisateur SET login='$login',statut='$statut',password='$password',initial='$initial',nom='$nom' where id='$id' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="L'utilisateur a été mis à jour ";
      
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