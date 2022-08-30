<?php
 session_start();


 include("head.php");
include_once "../controlleurs/conex.php";
 include_once "../models/utilisateur.class.php";

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

<!-- Modal detat des requettes sql -->
<script type="text/javascript">
                            $(window).on('load',function(){
                                $('#Modalresultquery').modal('show');
                            });


                        </script>
                            <?php if(isset($_SESSION['resultquery'])){?>
                                                <!-- The Modal -->
                            <div class="modal fade" id="Modalresultquery">
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
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    </div>
                                    
                                </div>
                                </div>
                            </div>
                            <?php }?>
  <!-- Modal detat de lenregistrement -->
  <script type="text/javascript">
                            $(window).on('load',function(){
                                $('#Modalmenu').modal('show');
                                $('#Modalmenu').modal({backdrop: 'static', keyboard: false}) ;
                            });


                        </script>
                         
                                                <!-- The Modal -->
                            <div class="modal fade" data-backdrop="static" id="Modalmenu">
                                <div class="modal-dialog">
                                <div class="modal-content">
                                
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                    <h4 class="modal-title" >
              
                                    MENU    
                                    </h4>
                                
                                    </div>
                             
                                    <!-- Modal body -->
                                 
  
  
                                    
                                    <a class="btn btn-success" href="Vdevis.php">  Gestion des Devis </br> <i class="fa fa-file-text"style="font-size:36px;color:black"></i></a>
                                    <?php if($_SESSION["user"] =="Administrateur"){ ?>
                                    <a class="btn btn-info" href="Vplan_type.php">   Paramétrage des plans types </br> <i class="fa fa-home" style="font-size:36px;color:black"></i> </a>
                                    <a class="btn btn-warning" href="Vuser.php">   Gestion des Utilisateurs </br> <i class="fa fa-address-card" style="font-size:36px"></i> </a>
                                    <a class="btn btn-danger" href="vreferentiel-travaux_corps-metier.php">   Gestion du Référentiel des Travaux </br><i class="fas fa-hard-hat"  style="font-size:36px;color:black"></i><i class="fas fa-ruler-vertical"  style="font-size:36px;color:black" ></i></a>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModaloptiongrl">
    Gestion des options générales</br>
    <i class="fas fa-cogs" style="font-size:36px;color:black"></i>


  </button>
                                    <!-- Modal footer -->
                                    <?php }?>
                                    
                                </div>
                                </div>
                            </div>
                            <?php
                                //Modal pour les options

                            include("options-general.php");
                            ?>