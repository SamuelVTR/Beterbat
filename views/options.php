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
include_once "../models/Options.class.php";
$option=new Option("","","","","");
$AllOptions= $option->getall();
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
                                    <button type="button" class="Fermer" data-dismiss="modal">&times;</button>
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
​<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
    Ajouter options
  </button>

  <!-- The Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Nouvelle option</h4>
          <button type="button" class="Fermer" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
        <form action="../controlleurs/Coptions.php" method="post">
    <div class="form-group">
      <label for="nomOption">Nom de l'option :</label>
      <input name="nomOption" type="text" class="form-control" id="nomOption" type="text" required>
    </div>
  
    <label for="selOptions"> Selectionné le sous-groupe:</label>
      <select class="form-control input-sm" id="selOptions" name="selOptions">
                           
                           
                             <option>  Volet bois par modèle   </option>
                             <option> Toiture   </option>
                             <option>  Sanitaire  </option>
                             <option>  Electricité  </option>
                             <option>  Charpente  </option>
                             <option> Predalle   </option>
                             <option> Divers   </option>

      </select>
      </br>
      <div class="form-group">
      <label for="cout">Coût :</label>
      <input name="cout" type="number" step="0.01" class="form-control" id="cout" type="text" required>
    </div>
      <div class="checkbox">
  <label><input name="no_quantité" type="checkbox" value="1">sans quantité</label>
    </div>
    <div class="checkbox">
  <label><input name="tarif_plan" type="checkbox" value="1">Tarif lié au plan</label>
    </div>
    <div class="form-group">
     
      <input  type="submit" class="form-control" id="submit" name="btn_add_options" >
    </div>

  </form>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
        </div>
        
      </div>
    </div>
  </div>





      
 <div id="accordion">
    <div class="card">
      <div class="card-header">
        <a class="card-link" data-toggle="collapse" href="#collapseOne">
          Volet bois par modèle
        </a>
      </div>
      <div id="collapseOne" class="collapse show">
        <div class="card-body">
        <?php

                                    print "  <table class='table table-striped table-hover'>
                                    <thead>
                                    <tr>
                                   
                                      <th class='text-center'>Descriptif</th>
                                      <th class='text-center'>coût</th>
                                      <th class='text-center'>Modifier</th>
                                    </tr>
                                  </thead>
                                  
                                            <tbody>";
                                                        foreach($AllOptions as $option)
                                                        {
                                                                if("Volet bois par modèle"==$option[2])
                                                                {
                                                                ?> 
                                                                            <tr>
                                                                                <td> <?php print $option[1] ?></td>
                                                                                <td> <?php print $option[3] ?></td>
                                                                                <td> 
                                                                              
  <!-- Trigger the modal with a button -->                                                  
                                                                                                             <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?php print  str_replace(' ','',$option[1]) ?>">Modifier</button>
                                                                            
                                                                            <!-- Modal -->
                                                                                                                                                    <div class="modal fade" id="myModal<?php print  str_replace(' ','',$option[1]) ?>" role="dialog">
                                                                                                                                                        <div class="modal-dialog">
                                                                                                                                                        
                                                                                                                                                        <!-- Modal content-->
                                                                                                                                                        <div class="modal-content">
                                                                                                                                                            <div class="modal-header">
                                                                                                                                                            <button type="button" class="Fermer" data-dismiss="modal">&times;</button>
                                                                                                                                                            <h4 class="modal-title">Modifier <?php print $option[1] ?></h4>
                                                                                                                                                            </div>
                                                                                                                                                            <div class="modal-body">
                                                                                                                                                            
                                                                
                                                                                                                                                            <form action="../controlleurs/Coptions.php" method="post">
                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                <label for="nomOption">Nom de l'option :</label>
                                                                                                                                                                                <input name="idOptionupdate" value="<?php print $option[0] ?>" type="hidden" class="form-control" id="nomOption"  >
                                                                                                                                                                                <input name="nomOptionupdate" value="<?php print $option[1] ?>" type="text" class="form-control" id="nomOption"  required>
                                                                                                                                                                                </div>
                                                                                                                                                                            
                                                                                                                                                                                <label for="selOptionsupdate"> Selectionné le sous-groupe:</label>
                                                                                                                                                                                <select class="form-control input-sm" id="selOptionsupdate" name="selOptionsupdate">
                                                                                                                                                                                                    
                                                                                                                                                                                                    
                                                                                                                                                                                                        <option <?php if($option[2]=="Volet bois par modèle"){print "selected='selected'";} ?>>  Volet bois par modèle   </option>
                                                                                                                                                                                                        <option  <?php if($option[2]=="Toiture"){print "selected='selected'";} ?>> Toiture   </option>
                                                                                                                                                                                                        <option  <?php if($option[2]=="Sanitaire"){print "selected='selected'";} ?>>  Sanitaire  </option>
                                                                                                                                                                                                        <option  <?php if($option[2]=="Electricité"){print "selected='selected'";} ?>>  Electricité  </option>
                                                                                                                                                                                                        <option  <?php if($option[2]=="Charpente"){print "selected='selected'";} ?>>  Charpente  </option>
                                                                                                                                                                                                        <option  <?php if($option[2]=="Predalle"){print "selected='selected'";} ?>> Predalle   </option>
                                                                                                                                                                                                        <option  <?php if($option[2]=="Divers"){print "selected='selected'";} ?>> Divers   </option>

                                                                                                                                                                                </select>
                                                                                                                                                                                </br>
                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                <label for="cout">Coût :</label>
                                                                                                                                                                                <input name="coutupdate" value="<?php print $option[3] ?>" type="number" step="0.01" class="form-control" id="cout"  required>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="checkbox">
                                                                                                                                                                            <label><input name="no_quantitéupdate" <?php  if($option[4]=="1"){print "checked";}  ?> type="checkbox" value="1">sans quantité</label>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="checkbox">
                                                                                                                                                                            <label><input name="tarif_planupdate" <?php  if($option[5]=="1"){print "checked";}?>  type="checkbox" value="1">Tarif lié au plan</label>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                
                                                                                                                                                                                <input value="Modifier l'option" type="submit" class="form-control btn-info" id="submit" name="btn_update_options" >
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                
                                                                                                                                                                                <input value="Supprimer l'option" type="submit" class="form-control btn-warning" id="submit" name="btn_delete_options" >
                                                                                                                                                                                </div>
                                                                                                                                                                            </form>
                                                                
                                                                
                                                                
                                                                                                                                                            </div>
                                                                                                                                                            <div class="modal-footer">
                                                                                                                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </td>                                                                           
                                                                                <tr>
                                                                                
                                                                <?php
                                                                }
                                                            }   
                                                            print "</tbody> </table> "
                                                                ?>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <a class="collapsed card-link" data-toggle="collapse" href="#collapseTwo">
    Toiture
      </a>
      </div>
      <div id="collapseTwo" class="collapse show">
        <div class="card-body">
        <?php

print "  <table class='table table-striped table-hover'>
<thead>
<tr>

  <th class='text-center'>Descriptif</th>
  <th class='text-center'>coût</th>
  <th class='text-center'>Modifier</th>
</tr>
</thead>

        <tbody>";
                    foreach($AllOptions as $option)
                    {
                            if("Toiture"==$option[2])
                            {
                            ?> 
                                        <tr>
                                            <td> <?php print $option[1] ?></td>
                                            <td> <?php print $option[3] ?></td>
                                            <td> 
                                          
<!-- Trigger the modal with a button -->                                                  
                                                                         <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?php print  str_replace(' ','',$option[1]) ?>">Modifier</button>
                                        
                                        <!-- Modal -->
                                                                                                                <div class="modal fade" id="myModal<?php print  str_replace(' ','',$option[1]) ?>" role="dialog">
                                                                                                                    <div class="modal-dialog">
                                                                                                                    
                                                                                                                    <!-- Modal content-->
                                                                                                                    <div class="modal-content">
                                                                                                                        <div class="modal-header">
                                                                                                                        <button type="button" class="Fermer" data-dismiss="modal">&times;</button>
                                                                                                                        <h4 class="modal-title">Modifier <?php print $option[1] ?></h4>
                                                                                                                        </div>
                                                                                                                        <div class="modal-body">
                                                                                                                        
                            
                                                                                                                        <form action="../controlleurs/Coptions.php" method="post">
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="nomOption">Nom de l'option :</label>
                                                                                                                                            <input name="idOptionupdate" value="<?php print $option[0] ?>" type="hidden" class="form-control" id="nomOption"  >
                                                                                                                                            <input name="nomOptionupdate" value="<?php print $option[1] ?>" type="text" class="form-control" id="nomOption"  required>
                                                                                                                                            </div>
                                                                                                                                        
                                                                                                                                            <label for="selOptionsupdate"> Selectionné le sous-groupe:</label>
                                                                                                                                            <select class="form-control input-sm" id="selOptionsupdate" name="selOptionsupdate">
                                                                                                                                                                
                                                                                                                                                                
                                                                                                                                                                    <option <?php if($option[2]=="Volet bois par modèle"){print "selected='selected'";} ?>>  Volet bois par modèle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Toiture"){print "selected='selected'";} ?>> Toiture   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Sanitaire"){print "selected='selected'";} ?>>  Sanitaire  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Electricité"){print "selected='selected'";} ?>>  Electricité  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Charpente"){print "selected='selected'";} ?>>  Charpente  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Predalle"){print "selected='selected'";} ?>> Predalle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Divers"){print "selected='selected'";} ?>> Divers   </option>

                                                                                                                                            </select>
                                                                                                                                            </br>
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="cout">Coût :</label>
                                                                                                                                            <input name="coutupdate" value="<?php print $option[3] ?>" type="number" step="0.01" class="form-control" id="cout"  required>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="no_quantitéupdate" <?php  if($option[4]=="1"){print "checked";}  ?> type="checkbox" value="1">sans quantité</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="tarif_planupdate" <?php  if($option[5]=="1"){print "checked";}?>  type="checkbox" value="1">Tarif lié au plan</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Modifier l'option" type="submit" class="form-control btn-info" id="submit" name="btn_update_options" >
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Supprimer l'option" type="submit" class="form-control btn-warning" id="submit" name="btn_delete_options" >
                                                                                                                                            </div>
                                                                                                                                        </form>
                            
                            
                            
                                                                                                                        </div>
                                                                                                                        <div class="modal-footer">
                                                                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>                                                                           
                                            <tr>
                                            
                            <?php
                            }
                        }   
                        print "</tbody> </table> "
                            ?>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <a class="collapsed card-link" data-toggle="collapse" href="#collapseThree">
          Sanitaire
        </a>
      </div>
      <div id="collapseThree" class="collapse show">
        <div class="card-body">
        <?php

print "  <table class='table table-striped table-hover'>
<thead>
<tr>

  <th class='text-center'>Descriptif</th>
  <th class='text-center'>coût</th>
  <th class='text-center'>Modifier</th>
</tr>
</thead>

        <tbody>";
                    foreach($AllOptions as $option)
                    {
                            if("Sanitaire"==$option[2])
                            {
                            ?> 
                                        <tr>
                                            <td> <?php print $option[1] ?></td>
                                            <td> <?php print $option[3] ?></td>
                                            <td> 
                                          
<!-- Trigger the modal with a button -->                                                  
                                                                         <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?php print  str_replace(' ','',$option[1]) ?>">Modifier</button>
                                        
                                        <!-- Modal -->
                                                                                                                <div class="modal fade" id="myModal<?php print  str_replace(' ','',$option[1]) ?>" role="dialog">
                                                                                                                    <div class="modal-dialog">
                                                                                                                    
                                                                                                                    <!-- Modal content-->
                                                                                                                    <div class="modal-content">
                                                                                                                        <div class="modal-header">
                                                                                                                        <button type="button" class="Fermer" data-dismiss="modal">&times;</button>
                                                                                                                        <h4 class="modal-title">Modifier <?php print $option[1] ?></h4>
                                                                                                                        </div>
                                                                                                                        <div class="modal-body">
                                                                                                                        
                            
                                                                                                                        <form action="../controlleurs/Coptions.php" method="post">
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="nomOption">Nom de l'option :</label>
                                                                                                                                            <input name="idOptionupdate" value="<?php print $option[0] ?>" type="hidden" class="form-control" id="nomOption"  >
                                                                                                                                            <input name="nomOptionupdate" value="<?php print $option[1] ?>" type="text" class="form-control" id="nomOption"  required>
                                                                                                                                            </div>
                                                                                                                                        
                                                                                                                                            <label for="selOptionsupdate"> Selectionné le sous-groupe:</label>
                                                                                                                                            <select class="form-control input-sm" id="selOptionsupdate" name="selOptionsupdate">
                                                                                                                                                                
                                                                                                                                                                
                                                                                                                                                                    <option <?php if($option[2]=="Volet bois par modèle"){print "selected='selected'";} ?>>  Volet bois par modèle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Toiture"){print "selected='selected'";} ?>> Toiture   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Sanitaire"){print "selected='selected'";} ?>>  Sanitaire  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Electricité"){print "selected='selected'";} ?>>  Electricité  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Charpente"){print "selected='selected'";} ?>>  Charpente  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Predalle"){print "selected='selected'";} ?>> Predalle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Divers"){print "selected='selected'";} ?>> Divers   </option>

                                                                                                                                            </select>
                                                                                                                                            </br>
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="cout">Coût :</label>
                                                                                                                                            <input name="coutupdate" value="<?php print $option[3] ?>" type="number" step="0.01" class="form-control" id="cout"  required>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="no_quantitéupdate" <?php  if($option[4]=="1"){print "checked";}  ?> type="checkbox" value="1">sans quantité</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="tarif_planupdate" <?php  if($option[5]=="1"){print "checked";}?>  type="checkbox" value="1">Tarif lié au plan</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Modifier l'option" type="submit" class="form-control btn-info" id="submit" name="btn_update_options" >
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Supprimer l'option" type="submit" class="form-control btn-warning" id="submit" name="btn_delete_options" >
                                                                                                                                            </div>
                                                                                                                                        </form>
                            
                            
                            
                                                                                                                        </div>
                                                                                                                        <div class="modal-footer">
                                                                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>                                                                           
                                            <tr>
                                            
                            <?php
                            }
                        }   
                        print "</tbody> </table> "
                            ?>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <a class="collapsed card-link" data-toggle="collapse" href="#collapse4">
       Electricité 
      </a>
      </div>
      <div id="collapse4" class="collapse show">
        <div class="card-body">
        <?php

print "  <table class='table table-striped table-hover'>
<thead>
<tr>

  <th class='text-center'>Descriptif</th>
  <th class='text-center'>coût</th>
  <th class='text-center'>Modifier</th>
</tr>
</thead>

        <tbody>";
                    foreach($AllOptions as $option)
                    {
                            if("Electricité"==$option[2])
                            {
                            ?> 
                                        <tr>
                                            <td> <?php print $option[1] ?></td>
                                            <td> <?php print $option[3] ?></td>
                                            <td> 
                                          
<!-- Trigger the modal with a button -->                                                  
                                                                         <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?php print  str_replace(' ','',$option[1]) ?>">Modifier</button>
                                        
                                        <!-- Modal -->
                                                                                                                <div class="modal fade" id="myModal<?php print  str_replace(' ','',$option[1]) ?>" role="dialog">
                                                                                                                    <div class="modal-dialog">
                                                                                                                    
                                                                                                                    <!-- Modal content-->
                                                                                                                    <div class="modal-content">
                                                                                                                        <div class="modal-header">
                                                                                                                        <button type="button" class="Fermer" data-dismiss="modal">&times;</button>
                                                                                                                        <h4 class="modal-title">Modifier <?php print $option[1] ?></h4>
                                                                                                                        </div>
                                                                                                                        <div class="modal-body">
                                                                                                                        
                            
                                                                                                                        <form action="../controlleurs/Coptions.php" method="post">
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="nomOption">Nom de l'option :</label>
                                                                                                                                            <input name="idOptionupdate" value="<?php print $option[0] ?>" type="hidden" class="form-control" id="nomOption"  >
                                                                                                                                            <input name="nomOptionupdate" value="<?php print $option[1] ?>" type="text" class="form-control" id="nomOption"  required>
                                                                                                                                            </div>
                                                                                                                                        
                                                                                                                                            <label for="selOptionsupdate"> Selectionné le sous-groupe:</label>
                                                                                                                                            <select class="form-control input-sm" id="selOptionsupdate" name="selOptionsupdate">
                                                                                                                                                                
                                                                                                                                                                
                                                                                                                                                                    <option <?php if($option[2]=="Volet bois par modèle"){print "selected='selected'";} ?>>  Volet bois par modèle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Toiture"){print "selected='selected'";} ?>> Toiture   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Sanitaire"){print "selected='selected'";} ?>>  Sanitaire  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Electricité"){print "selected='selected'";} ?>>  Electricité  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Charpente"){print "selected='selected'";} ?>>  Charpente  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Predalle"){print "selected='selected'";} ?>> Predalle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Divers"){print "selected='selected'";} ?>> Divers   </option>

                                                                                                                                            </select>
                                                                                                                                            </br>
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="cout">Coût :</label>
                                                                                                                                            <input name="coutupdate" value="<?php print $option[3] ?>" type="number" step="0.01" class="form-control" id="cout"  required>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="no_quantitéupdate" <?php  if($option[4]=="1"){print "checked";}  ?> type="checkbox" value="1">sans quantité</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="tarif_planupdate" <?php  if($option[5]=="1"){print "checked";}?>  type="checkbox" value="1">Tarif lié au plan</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Modifier l'option" type="submit" class="form-control btn-info" id="submit" name="btn_update_options" >
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Supprimer l'option" type="submit" class="form-control btn-warning" id="submit" name="btn_delete_options" >
                                                                                                                                            </div>
                                                                                                                                        </form>
                            
                            
                            
                                                                                                                        </div>
                                                                                                                        <div class="modal-footer">
                                                                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>                                                                           
                                            <tr>
                                            
                            <?php
                            }
                        }   
                        print "</tbody> </table> "
                            ?>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <a class="collapsed card-link" data-toggle="collapse" href="#collapse5">
        Charpente
      </a>
      </div>
      <div id="collapse5" class="collapse show">
        <div class="card-body">
        <?php

print "  <table class='table table-striped table-hover'>
<thead>
<tr>

  <th class='text-center'>Descriptif</th>
  <th class='text-center'>coût</th>
  <th class='text-center'>Modifier</th>
</tr>
</thead>

        <tbody>";
                    foreach($AllOptions as $option)
                    {
                            if("Charpente"==$option[2])
                            {
                            ?> 
                                        <tr>
                                            <td> <?php print $option[1] ?></td>
                                            <td> <?php print $option[3] ?></td>
                                            <td> 
                                          
<!-- Trigger the modal with a button -->                                                  
                                                                         <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?php print  str_replace(' ','',$option[1]) ?>">Modifier</button>
                                        
                                        <!-- Modal -->
                                                                                                                <div class="modal fade" id="myModal<?php print  str_replace(' ','',$option[1]) ?>" role="dialog">
                                                                                                                    <div class="modal-dialog">
                                                                                                                    
                                                                                                                    <!-- Modal content-->
                                                                                                                    <div class="modal-content">
                                                                                                                        <div class="modal-header">
                                                                                                                        <button type="button" class="Fermer" data-dismiss="modal">&times;</button>
                                                                                                                        <h4 class="modal-title">Modifier <?php print $option[1] ?></h4>
                                                                                                                        </div>
                                                                                                                        <div class="modal-body">
                                                                                                                        
                            
                                                                                                                        <form action="../controlleurs/Coptions.php" method="post">
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="nomOption">Nom de l'option :</label>
                                                                                                                                            <input name="idOptionupdate" value="<?php print $option[0] ?>" type="hidden" class="form-control" id="nomOption"  >
                                                                                                                                            <input name="nomOptionupdate" value="<?php print $option[1] ?>" type="text" class="form-control" id="nomOption"  required>
                                                                                                                                            </div>
                                                                                                                                        
                                                                                                                                            <label for="selOptionsupdate"> Selectionné le sous-groupe:</label>
                                                                                                                                            <select class="form-control input-sm" id="selOptionsupdate" name="selOptionsupdate">
                                                                                                                                                                
                                                                                                                                                                
                                                                                                                                                                    <option <?php if($option[2]=="Volet bois par modèle"){print "selected='selected'";} ?>>  Volet bois par modèle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Toiture"){print "selected='selected'";} ?>> Toiture   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Sanitaire"){print "selected='selected'";} ?>>  Sanitaire  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Electricité"){print "selected='selected'";} ?>>  Electricité  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Charpente"){print "selected='selected'";} ?>>  Charpente  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Predalle"){print "selected='selected'";} ?>> Predalle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Divers"){print "selected='selected'";} ?>> Divers   </option>

                                                                                                                                            </select>
                                                                                                                                            </br>
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="cout">Coût :</label>
                                                                                                                                            <input name="coutupdate" value="<?php print $option[3] ?>" type="number" step="0.01" class="form-control" id="cout"  required>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="no_quantitéupdate" <?php  if($option[4]=="1"){print "checked";}  ?> type="checkbox" value="1">sans quantité</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="tarif_planupdate" <?php  if($option[5]=="1"){print "checked";}?>  type="checkbox" value="1">Tarif lié au plan</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Modifier l'option" type="submit" class="form-control btn-info" id="submit" name="btn_update_options" >
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Supprimer l'option" type="submit" class="form-control btn-warning" id="submit" name="btn_delete_options" >
                                                                                                                                            </div>
                                                                                                                                        </form>
                            
                            
                            
                                                                                                                        </div>
                                                                                                                        <div class="modal-footer">
                                                                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>                                                                           
                                            <tr>
                                            
                            <?php
                            }
                        }   
                        print "</tbody> </table> "
                            ?>
        </div>
      </div>
    </div>
    <div class="card">
    <div class="card-header">
        <a class="collapsed card-link" data-toggle="collapse" href="#collapse6">
        Predalle 
      </a>
      </div>
      <div id="collapse6" class="collapse show">
        <div class="card-body">
        <?php

print "  <table class='table table-striped table-hover'>
<thead>
<tr>

  <th class='text-center'>Descriptif</th>
  <th class='text-center'>coût</th>
  <th class='text-center'>Modifier</th>
</tr>
</thead>

        <tbody>";
                    foreach($AllOptions as $option)
                    {
                            if("Predalle"==$option[2])
                            {
                            ?> 
                                        <tr>
                                            <td> <?php print $option[1] ?></td>
                                            <td> <?php print $option[3] ?></td>
                                            <td> 
                                          
<!-- Trigger the modal with a button -->                                                  
                                                                         <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?php print  str_replace(' ','',$option[1]) ?>">Modifier</button>
                                        
                                        <!-- Modal -->
                                                                                                                <div class="modal fade" id="myModal<?php print  str_replace(' ','',$option[1]) ?>" role="dialog">
                                                                                                                    <div class="modal-dialog">
                                                                                                                    
                                                                                                                    <!-- Modal content-->
                                                                                                                    <div class="modal-content">
                                                                                                                        <div class="modal-header">
                                                                                                                        <button type="button" class="Fermer" data-dismiss="modal">&times;</button>
                                                                                                                        <h4 class="modal-title">Modifier <?php print $option[1] ?></h4>
                                                                                                                        </div>
                                                                                                                        <div class="modal-body">
                                                                                                                        
                            
                                                                                                                        <form action="../controlleurs/Coptions.php" method="post">
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="nomOption">Nom de l'option :</label>
                                                                                                                                            <input name="idOptionupdate" value="<?php print $option[0] ?>" type="hidden" class="form-control" id="nomOption"  >
                                                                                                                                            <input name="nomOptionupdate" value="<?php print $option[1] ?>" type="text" class="form-control" id="nomOption"  required>
                                                                                                                                            </div>
                                                                                                                                        
                                                                                                                                            <label for="selOptionsupdate"> Selectionné le sous-groupe:</label>
                                                                                                                                            <select class="form-control input-sm" id="selOptionsupdate" name="selOptionsupdate">
                                                                                                                                                                
                                                                                                                                                                
                                                                                                                                                                    <option <?php if($option[2]=="Volet bois par modèle"){print "selected='selected'";} ?>>  Volet bois par modèle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Toiture"){print "selected='selected'";} ?>> Toiture   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Sanitaire"){print "selected='selected'";} ?>>  Sanitaire  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Electricité"){print "selected='selected'";} ?>>  Electricité  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Charpente"){print "selected='selected'";} ?>>  Charpente  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Predalle"){print "selected='selected'";} ?>> Predalle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Divers"){print "selected='selected'";} ?>> Divers   </option>

                                                                                                                                            </select>
                                                                                                                                            </br>
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="cout">Coût :</label>
                                                                                                                                            <input name="coutupdate" value="<?php print $option[3] ?>" type="number" step="0.01" class="form-control" id="cout"  required>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="no_quantitéupdate" <?php  if($option[4]=="1"){print "checked";}  ?> type="checkbox" value="1">sans quantité</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="tarif_planupdate" <?php  if($option[5]=="1"){print "checked";}?>  type="checkbox" value="1">Tarif lié au plan</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Modifier l'option" type="submit" class="form-control btn-info" id="submit" name="btn_update_options" >
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Supprimer l'option" type="submit" class="form-control btn-warning" id="submit" name="btn_delete_options" >
                                                                                                                                            </div>
                                                                                                                                        </form>
                            
                            
                            
                                                                                                                        </div>
                                                                                                                        <div class="modal-footer">
                                                                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>                                                                           
                                            <tr>
                                            
                            <?php
                            }
                        }   
                        print "</tbody> </table> "
                            ?>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <a class="collapsed card-link" data-toggle="collapse" href="#collapse7">
        Divers
      </a>
      </div>
      <div id="collapse7" class="collapse show">
        <div class="card-body">
        <?php

print "  <table class='table table-striped table-hover'>
<thead>
<tr>

  <th class='text-center'>Descriptif</th>
  <th class='text-center'>Coût</th>
  <th class='text-center'>Modifier</th>
</tr>
</thead>

        <tbody>";
                    foreach($AllOptions as $option)
                    {
                            if("Divers"==$option[2])
                            {
                            ?> 
                                        <tr>
                                            <td> <?php print $option[1] ?></td>
                                            <td> <?php print $option[3] ?></td>
                                            <td> 
                                          
<!-- Trigger the modal with a button -->                                                  
                                                                         <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?php print  str_replace(' ','',$option[1]) ?>">Modifier</button>
                                        
                                        <!-- Modal -->
                                                                                                                <div class="modal fade" id="myModal<?php print  str_replace(' ','',$option[1]) ?>" role="dialog">
                                                                                                                    <div class="modal-dialog">
                                                                                                                    
                                                                                                                    <!-- Modal content-->
                                                                                                                    <div class="modal-content">
                                                                                                                        <div class="modal-header">
                                                                                                                        <button type="button" class="Fermer" data-dismiss="modal">&times;</button>
                                                                                                                        <h4 class="modal-title">Modifier <?php print $option[1] ?></h4>
                                                                                                                        </div>
                                                                                                                        <div class="modal-body">
                                                                                                                        
                            
                                                                                                                        <form action="../controlleurs/Coptions.php" method="post">
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="nomOption">Nom de l'option :</label>
                                                                                                                                            <input name="idOptionupdate" value="<?php print $option[0] ?>" type="hidden" class="form-control" id="nomOption"  >
                                                                                                                                            <input name="nomOptionupdate" value="<?php print $option[1] ?>" type="text" class="form-control" id="nomOption"  required>
                                                                                                                                            </div>
                                                                                                                                        
                                                                                                                                            <label for="selOptionsupdate"> Selectionné le sous-groupe:</label>
                                                                                                                                            <select class="form-control input-sm" id="selOptionsupdate" name="selOptionsupdate">
                                                                                                                                                                
                                                                                                                                                                
                                                                                                                                                                    <option <?php if($option[2]=="Volet bois par modèle"){print "selected='selected'";} ?>>  Volet bois par modèle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Toiture"){print "selected='selected'";} ?>> Toiture   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Sanitaire"){print "selected='selected'";} ?>>  Sanitaire  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Electricité"){print "selected='selected'";} ?>>  Electricité  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Charpente"){print "selected='selected'";} ?>>  Charpente  </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Predalle"){print "selected='selected'";} ?>> Predalle   </option>
                                                                                                                                                                    <option  <?php if($option[2]=="Divers"){print "selected='selected'";} ?>> Divers   </option>

                                                                                                                                            </select>
                                                                                                                                            </br>
                                                                                                                                            <div class="form-group">
                                                                                                                                            <label for="cout">Coût :</label>
                                                                                                                                            <input name="coutupdate" value="<?php print $option[3] ?>" type="number" step="0.01" class="form-control" id="cout"  required>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="no_quantitéupdate" <?php  if($option[4]=="1"){print "checked";}  ?> type="checkbox" value="1">sans quantité</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="checkbox">
                                                                                                                                        <label><input name="tarif_planupdate" <?php  if($option[5]=="1"){print "checked";}?>  type="checkbox" value="1">Tarif lié au plan</label>
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Modifier l'option" type="submit" class="form-control btn-info" id="submit" name="btn_update_options" >
                                                                                                                                            </div>
                                                                                                                                            <div class="form-group">
                                                                                                                                            
                                                                                                                                            <input value="Supprimer l'option" type="submit" class="form-control btn-warning" id="submit" name="btn_delete_options" >
                                                                                                                                            </div>
                                                                                                                                        </form>
                            
                            
                            
                                                                                                                        </div>
                                                                                                                        <div class="modal-footer">
                                                                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>                                                                           
                                            <tr>
                                            
                            <?php
                            }
                        }   
                        print "</tbody> </table> "
                            ?>
        </div>
      </div>
    </div>
  </div>
</div>