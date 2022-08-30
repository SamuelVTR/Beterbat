<?php
session_start();
if($_SESSION["user"] !="Administrateur"){

  header("Location: ../index.php");
  }
include("head.php");
include_once "../controlleurs/conex.php";
include_once "../models/Corps_de_metier.class.php";
include_once "../models/Regroupement.class.php";
include_once "../models/Travail.class.php";
include_once "../models/plan_type.class.php";
$Corps_de_metier = new corps_de_metier("","","");
$Allcorps_metier= $Corps_de_metier->getall();
$regroupement=new Regroupement("","","");
$Allregroupement= $regroupement->getall();
$travail=new Travail("","","","","","");
$Alltravail= $travail->getall();
$plan_type = new plan("","","","","","","","","","");
$Allplan= $plan_type->getall();
$diver=$_SESSION["divers"];

    try {      
              
        $ql = "SELECT * FROM divers";
        $prepTest = $dbCon->prepare($ql);
        $prepTest->execute();
        $result = $prepTest->fetchAll();  
        
        $_SESSION["divers"]=$result;
        
    
    
    }
    catch (Exception $ex) {
        //$dbCon=null;
         $e = oci_error();
         trigger_error(htmlentities($e['message']), E_USER_ERROR);
         $_SESSION['resultquery']=$ex;
         return false; 
     }
 
 ?>
 
  <!-- Modal detat de lenregistrement -->
  <script type="text/javascript">
                            $(window).on('load',function(){
                                $('#Modalresultquery').modal('show');
                            });

function removeRequired(form){
    $.each(form, function(key, value) {
        if ( value.hasAttribute("required")){
            value.removeAttribute("required");
        }
    });
}
    </script>
    <?php if(isset($_SESSION['resultquery'])){?>
                                                <!-- The Modal -->
                            <div style="z-index:1000;" class="modal fade" id="Modalresultquery">
                                <div class="modal-dialog">
                                <div class="modal-content">
                                
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                    <h4 class="modal-title">
                                    <?php print $_SESSION['resultquery']; 
                                    
                                    unset($_SESSION['resultquery']);
                                    ?>
                                    
                                    </h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    
                                    <!-- Modal body -->
                                    
                                    
                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                                    </div>
                                    
                                </div>
                                </div>
                            </div>
        <?php }?>

<div class="container">
    <?php 
    
    if($_SESSION["user"]=="Administrateur")
    {
        include("NavBaradmin.php");
    } 
    
    else 
    {     include("NavBarCommercial.php");
     }         ?>


    <a type="button" class="btn btn-info btn-lg" href="Vadd_plan_type.php">Ajouter un plan type</a>
    <div class="container">


     

        <form action="../controlleurs/Cplan_type.php" enctype="multipart/form-data" method="post">
        </br>
        
        </br>
        <h3 class="bg-primary w3-round w3-blue">Modifier la notice:</h3>
        <div class="col-sm-5">
        
        <div class="form-group">
      
        <label class="custom-file-label"  required> Choisir fichier notice PDF </label>
<div class="custom-file">
<input style="width:33%;" name="myfile" type="file" class="custom-file-input" id="customFile">
    
  </div>

    <script>
            $('#customFile').on('change',function(){
                //get the file name
                var fileName = $(this).val();
                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fileName);
            })
        </script>
      
                    <input  type="submit" class="form-control btn btn-success" value="Enregistrer nouvelle notice" id="submit"  name="btnsavenotice" >  
              </div>     
              <div class="col-sm-7"></div>      
        
  
    
  
        </div>
        
       
        <div class="w3-example">
<h3 class="bg-primary w3-round w3-blue">Liste de plan</h3>
  <table id="myTable" class="table table-bordered">
                            <thead>
                            <tr class="header">
                                <th class="text-center">NOM</th>
                                <th class="text-center" >DESCRIPTION</th>
                                <th class="text-center">PRIX</th>
                                <th class="text-center">MODIFIER</th>
                            </tr>
                                    </thead>
                                        <tbody>
                            <?php
                                foreach($Allplan as $plantype)
                                {
                                    ?> 
                                    
                                <tr>
                                <td align="center" >  <?php print $plantype["Nom_plan"];  ?> </td>
                                    <td align="center">  <?php print $plantype["Description"];  ?> </td>
                                    <td align="center" > <?php print $plantype["prix_HT"];  ?> Eur</td>
                                    <td align="center" >  <button class="btn " name='plan_update' value=<?php print($plantype["Id"]) ?>    width="30px"> <img src="image\Update.png" width="30px"> </button></td>

               
                                </tr>
                                <?php
                                }
                                
                                ?>
                                     </tbody>
                                 </table >
                                 </div>
                                 </form>






















<!-- End container -->
</div>       