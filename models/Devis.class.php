<?php
  if(!isset($_SESSION)) 
  { 
      session_start(); 
  }
class devis
{
	private $date;
    private $plan;

    private $client;
    private $commercial;
    private $adaptation_sol;
    private $amenagement_acces;
    private $options;
    private $metier_chargeclient;
    private $assainissement;
    private $prices;
    private $pdf;
    private $lotissement;
    public function devis($date,$plan,$client,$commercial,$adaptation_sol,$amenagement_acces,$options,$metier_chargeclient,$assainissement,$prices,$pdf,$lotissement)
    {  
        $this->date =$date;
        $this->plan =$plan ;
        $this->client = $client;
        $this->adaptation_sol =$adaptation_sol;
        $this->commercial =$commercial;
        $this->amenagement_acces =$amenagement_acces;
        $this->metier_chargeclient =$metier_chargeclient;
        $this->assainissement = $assainissement;
        $this->prices =$prices;
        $this->pdf =$pdf;
        $this->options =$options;
        $this->lotissement=$lotissement;
    }


    public function isnew()
    {
        
        $ql = "SELECT * FROM devis WHERE Nom_devis='$this->Nom_devis';" ;
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
                   
            //Inserer dans la BD
            include("../controlleurs/conex.php");
                try {                   
                    $ql = "INSERT INTO devis (date,plan,client,commercial,adaptation_sol,amenagement_acces,options,metier_chargeclient,assainissement,prices,pdf,lotissement) VALUES "
                        . "('$this->date','$this->plan','$this->client','$this->commercial','$this->adaptation_sol','$this->amenagement_acces','$this->options','$this->metier_chargeclient','$this->assainissement','$this->prices','$this->pdf','$this->lotissement');" ;

                    $prepTest = $dbCon->prepare($ql);
                    $prepTest->execute();
                
                    $_SESSION['resultquery']="Le devis a été Enregistré ";
                    $dbCon=null; 
                    return true;

                } catch (Exception $ex) {
                    $_SESSION['resultquery']= "Erreur: Le devis n'a pas été Enregistré ".$ex;
                    $dbCon=null; 
                    return false;
                }                             
           
       
    

    }

 
     
     public function getall()
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM devis";
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

     public function get($id)
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM devis where id ='$id';";
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
     public function getlast()
     {
        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "SELECT * FROM devis ORDER BY id DESC LIMIT 1";
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
      public function delete($devisToDelete)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "DELETE FROM devis where id='$devisToDelete' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="le devis a été Supprimé";
      
        } catch (Exception $ex) {
           //$dbCon=null;
            $e = oci_error();
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            $_SESSION['resultquery']=$ex;
            return false; 
        }
     }
     public function update($id,$Nom,$adaptation_sol,$no_quantite,$tarif_devis,$cout)
     {

        include("../controlleurs/conex.php");
        try {      
                  
            $ql = "UPDATE  devis SET nom='$Nom',adaptation_sol='$adaptation_sol',sans_quantite='$no_quantite',tarifliedevis='$tarif_devis',cout='$cout' where id='$id' ;";
            $prepTest = $dbCon->prepare($ql);
            $prepTest->execute();
               
            $_SESSION['resultquery']="Le devis a été Mise à jour ";
      
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