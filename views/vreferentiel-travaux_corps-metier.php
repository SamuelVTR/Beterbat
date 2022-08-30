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
$Corps_de_metier = new corps_de_metier("","","");
$Allcorps_metier= $Corps_de_metier->getall();
$regroupement=new Regroupement("","","");
$Allregroupement= $regroupement->getall();
$travail=new Travail("","","","","","");
$Alltravail= $travail->getall();
 ?>
                        <!-- Modal detat de lenregistrement -->
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
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                                    </div>
                                    
                                </div>
                                </div>
                            </div>
                            <?php }?>




<div class="container">
<?php  if($_SESSION["user"]=="Administrateur"){include("NavBaradmin.php");} else {     include("NavBarCommercial.php"); }         ?>
<!-- Trigger the modal with a button -->
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#ModaladdCorpmetier">Ajouter un corps de métier</button>

<!-- Modal -->
<div id="ModaladdCorpmetier" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Corps de métier</h4>
      </div>
      <div class="modal-body">
      
      <form action="../controlleurs/CrefTravaux.php" method="post">
    <div class="form-group">
      <label for="nom">Nom Corps de Métier:</label>
      <input name="nom" type="text" class="form-control" id="nom" type="text" required>
    </div>
    <div class="form-group">
      <label for="rmq">Remarques:</label>
      <textarea name="rmq" class="form-control" rows="5" id="rmq"></textarea>
    </div>
    <div class="checkbox">
  <label><input name="charge_client" type="checkbox" value="1">Charge du client</label>
    </div>
    <div class="form-group">
     
      <input type="submit" class="form-control" id="submit" name="btn_add_corps_métier">
    </div>

  </form>





      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>

  </div>
</div>


<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#ModaladdRegroupement">Ajouter un regroupement</button>

<!-- Modal -->
<div id="ModaladdRegroupement" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Regroupement du corps de métier</h4>
      </div>
      <div class="modal-body">
      
      <form action="../controlleurs/CrefTravaux.php" method="post">
    <div class="form-group">
      <label for="nomRegroupement">Nom du regroupement :</label>
      <input name="nomRegroupement" type="text" class="form-control" id="nomRegroupement" type="text" required>
    </div>
    <div class="form-group">
      <label for="rmqRegroupement">Remarques:</label>
      <textarea name="rmqRegroupement" class="form-control" rows="5" id="rmqRegroupement"></textarea>
    </div>
    <label for="selCorpsMetier"> Selectionné le corps de métier associé au regroupement:</label>
      <select class="form-control input-sm" id="selCorpsMetier" name="selCorpsMetier">
                            <?php
                                    // ajouter les nom des corps de metier au select
                            foreach($Allcorps_metier as $item)
                             {
                                print "<option>" . $item['nom_corps_metier']. "</option>";
                            }
                            ?>  



      </select>
      </br>
    <div class="form-group">
     
      <input  type="submit" class="form-control" id="submit" name="btn_add_regroupement" >
    </div>

  </form>




      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>

  </div>
</div>
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#ModaladdTravail">Ajouter un nouveaux travail</button>

<!-- Modal -->
<div id="ModaladdTravail" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Travail</h4>
      </div>
      <div class="modal-body">
      
      <form action="../controlleurs/CrefTravaux.php" method="post">
    <div class="form-group">
      <label for="nomTravail">Nom du Travail :</label>
      <input name="nomTravail" type="text" class="form-control" ng-model="myInput" id="nomTravail" type="text" required>
   
    </div>
    <div class="form-group">
      <label for="rmqTravail">Remarques:</label>
      <textarea name="rmqTravail" class="form-control" rows="5" id="rmqTravail"></textarea>
    </div>
    <label for="seltravail"> Selectionné regroupement associé au travail:</label>
    <select class="form-control input-sm" id="seltravail" name="seltravail">
                            <?php
                                    // ajouter les nom des corps de metier au select
                            foreach($Allregroupement as $item2)
                             {
                                print "<option>" . $item2['nom_regroupement']. "</option>";
                            }
                            ?>  



    </select>
    </br>
    <div ng-app="">
            <div class="checkbox">
            <label  for="charclient">Peut être à la charge du client :</label>
                <label><input name="charclient" id="charclient" type="checkbox" ng-model="val" />
            </div>
            <label ng-show="val" for="coutTravail">cout du travail (HT):</label>
            <input ng-show="val" name="coutTravail" type="number" step="0.01" class="form-control" id="coutTravail"  >

            <label ng-show="val" for="coutTravailloti">cout du travail (HT) si dans lotissement (rien mettre si ne s'applique pas):</label>
            <input ng-show="val" name="coutTravailloti" type="number" step="0.01" class="form-control" id="coutTravailloti"  >                
    </div>
    </br>
    <div class="form-group">
      <input  type="submit" class="form-control" id="submit" name="btn_add_travail" >
    </div>

  </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>

  </div>
</div>



<!--********************************** TABLEAU******************************** -->

<div class="">
    <?php
                                    // ajouter les nom des corps de metier au select
         $nbcorpmetier=0;
       
         print "<div id='accordion1'>";
         foreach($Allcorps_metier as $corpmetier)
        {
            ?>  
            <div class="card">
            <div class="card-header backgroundmetier">
              <a class="card-link " data-toggle="collapse<?php print $corpmetier[0] ?>" href="#collapse<?php print $corpmetier[0] ?>">
               <?php print $corpmetier[0] ?>
              </a>
            </div>
            <div id="collapse<?php print $corpmetier[0] ?>" class="collapse show" data-parent="">
              <div class="card-body">
              <?php
                                    // ajouter les nom des corps de metier au select
                                    print "<div id='accordion$corpmetier[0]'>";
                     foreach($Allregroupement as $regroupement1)
                     {      

                        if($corpmetier[0]==$regroupement1[0])
                        {
                            
                          
                            ?>  
                            <div class="card">
                                    <div class="card-header" data-  ="collapse<?php print $regroupement1[1] ?>" href="#collapse<?php print $regroupement1[1] ?>">
                                    <a class="card-link" >
                                    <?php print $regroupement1[1] ?>
                                    </a>
                                    </div>
                                    <div id="collapse<?php print $regroupement1[1] ?>" class="collapse show" data-parent="#accordion<?php print $corpmetier[0] ?>">
                                    <div class="card-body">
                                    <?php

                                    print "  <table class='table table-striped table-hover'>
                                    <thead>
                                    <tr>
                                   
                                      <th class='text-center'>nom</th>
                                      <th class='text-center'>caracteristique</th>
                                      <th class='text-center'>option</th>
                                    </tr>
                                  </thead>
                                  
                                            <tbody>";
                                                        foreach($Alltravail as $travail)
                                                        {
                                                                if($regroupement1[1]==$travail[4])
                                                                {
                                                                ?> 
                                                                        <tr>
                                                                                <td> <?php print $travail[1] ;?></td>
                                                                                <td> <?php print $travail[2] ;?></td>
                                                                                <td> 
                                                                              
  <!-- Trigger the modal with a button -->                                                  
                                                                              <?php 
                                                                              
                                                                              if($travail[1]=="enveloppe sanitaire")          
                                                                              {
                                                                                print("(obligatoire)");
                                                                              }

                                                                              else{

                                                                              
                                                                                ?> 
                                                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?php print  str_replace(' ','',$travail[1]) ?>">Supprimer</button>

                                                                                    <!-- Modal -->
                                                                                    <div class="modal fade" id="myModal<?php print  str_replace(' ','',$travail[1]) ?>" role="dialog">
                                                                                        <div class="modal-dialog">
                                                                                        
                                                                                        <!-- Modal content-->
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                            <h4 class="modal-title">Modifier <?php print $travail[1] ?></h4>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                          

                                                                                                                    <form action="../controlleurs/CrefTravaux.php" method="post">
                                                                                                                    <div class="form-group">
                                                                                                                    <label for="nomTravail">Nom du Travail :</label>
                                                                                                                    <input name="nomTravaildelete" value="<?php print $travail[1] ?>"  readonly="readonly" type="text" class="form-control" ng-model="myInput" id="nomTravaildelete" type="text" required>
                                                                                                                
                                                                                                                    
                                                                                                                    </br>
                                                                                                                    <div class="form-group">
                                                                                                                    <input  type="submit" value="Supprimer" class="form-control btn-danger" id="submit" name="btn_delete_travail" >
                                                                                                                    </div>

                                                                                                                </form>



                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                                                            </div>
                                                                                        </div>
                                                                                        
                                                                                        </div>
                                                                                    </div>
                                                                                    <?php } ?> 
                                                                                </td>
                                                                        </tr>

                                                                <?php
                                                                }




                                                    

                                                        } ?> 
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            <?php
                            
                            print "</div>";
                        }
                      
                    }
                         ?> 
              </div>
            </div>
          </div>
          </div>
     <br>  <br>  
          <?php
          
          }   
         ?> 
      
</div>

    
</div>