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
include_once "../models/utilisateur.class.php";
$utilisateur=new utilisateur("","","","","");
$Allutilisateurs= $utilisateur->getall();




function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'Qa4dvMP^d.=)d5';
    $secret_iv = '2dz684²Df';
    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}
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

<div class="row">
  
  <div class="col-sm-10"></div>
  <div class="col-sm-1"> <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModaladd">Ajouter utilisateur</button></div>
</div>

                                                                            
                                                                            <!-- Modal -->
                                                                                                                                                    <div class="modal fade" id="myModaladd" role="dialog">
                                                                                                                                                        <div class="modal-dialog">
                                                                                                                                                        
                                                                                                                                                        <!-- Modal content-->
                                                                                                                                                        <div class="modal-content">
                                                                                                                                                            <div class="modal-header">
                                                                                                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                                                                                            <h4 class="modal-title">Ajouter </h4>
                                                                                                                                                            </div>
                                                                                                                                                            <div class="modal-body">
                                                                                                                                                            
                                                                
                                                                                                                                                            <form action="../controlleurs/utilisateur.controlleur.php" method="post">
                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                <label for="loginutilisateur">Login de l'utilisateur :</label>
                                                                                                                                                                                <input name="idutilisateur"  type="hidden" class="form-control" id="loginutilisateur"  >
                                                                                                                                                                                <input name="loginutilisateur"  type="text" class="form-control" id="loginutilisateur"  required>

                                                                                                                                                                                   <label for="nomutilisateur">Nom de l'utilisateur :</label>
                                                                                                                                                                              
                                                                                                                                                                                <input name="nomutilisateur" type="text" class="form-control" id="nomutilisateur"  required>
                                                                                                                                                                             
                                                                                                                                                                             
                                                                                                                                                                             
                                                                                                                                                                                <label for="Initialutilisateur">Initial de l'utilisateur :</label>
                                                                                                                                                                              <input name="Initialutilisateur"  type="text" class="form-control" id="Initialutilisateur"  required>
                                                                                                                                                                                </div>
                                                                                                                                                                           

                                                                                                                                                                                <label for="psw"><b>Mot de passe</b></label>
                                                                                                                                                                                <input type="password"  name="password" required>

                                                                                                                                                                              
                                                                                                                                                                              
                                                                                                                                                                      
                                                                                                                                                                                <label for="selutilisateurs"> Selectionné profil:</label>
                                                                                                                                                                                <select class="form-control input-sm" id="selutilisateurs" name="selutilisateurs">
                                                                                                                                                                                                    
                                                                                                                                                                                                    
                                                                                                                                                                                                        <option>  Administrateur  </option>
                                                                                                                                                                                                        <option  > Commercial   </option>
                                                                                                                                                                                                      

                                                                                                                                                                                </select>
                                                                                                                                                                                </br>
                                                                                                                                                                                
                                                                                                                                                                                
                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                
                                                                                                                                                                                <input value="Ajouter l'utilisateur" type="submit" class="form-control btn-info" id="submit" name="btn_add_utilisateur" >
                                                                                                                                                                                </div>
                                                                                                                                                                               
                                                                                                                                                                            </form>
                                                                
                                                                
                                                                
                                                                                                                                                            </div>
                                                                                                                                                            <div class="modal-footer">
                                                                                                                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        </div>         </div>



<?php

                                    print "  <table class='table table-striped table-hover'>
                                    <thead>
                                    <tr>
                                   
                                      <th class='text-center'>Nom</th>
                                      <th class='text-center'>Initial</th>
                                      <th class='text-center'>Login</th>
                                      <th class='text-center'>Droit</th>
                                      <th class='text-center'>Modifier</th>
                                    </tr>
                                  </thead>
                                  
                                            <tbody>";
                                                        foreach($Allutilisateurs as $utilisateur)
                                                        {
                                                            
                                                                ?> 
                                                                            <tr>
                                                                                <td> <?php print $utilisateur[5] ?></td>
                                                                                <td> <?php print $utilisateur[4] ?></td>
                                                                                <td> <?php print $utilisateur[1] ?></td>
                                                                                <td> <?php print $utilisateur[2] ?></td>
                                                                                <td> 
                                                                              
  <!-- Trigger the modal with a button -->                                                  
                                                                                                             <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?php print  str_replace(' ','',$utilisateur[0]) ?>">Modifier</button>
                                                                            
                                                                            <!-- Modal -->
                                                                                                                                                    <div class="modal fade" id="myModal<?php print  str_replace(' ','',$utilisateur[0]) ?>" role="dialog">
                                                                                                                                                        <div class="modal-dialog">
                                                                                                                                                        
                                                                                                                                                        <!-- Modal content-->
                                                                                                                                                        <div class="modal-content">
                                                                                                                                                            <div class="modal-header">
                                                                                                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                                                                                            <h4 class="modal-title">Modifier <?php print $utilisateur[1] ?></h4>
                                                                                                                                                            </div>
                                                                                                                                                            <div class="modal-body">
                                                                                                                                                            
                                                                
                                                                                                                                                            <form action="../controlleurs/utilisateur.controlleur.php" method="post">
                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                <label for="loginutilisateur">Login de l'utilisateur :</label>
                                                                                                                                                                                <input name="idutilisateurupdate" value="<?php print $utilisateur[0] ?>" type="hidden" class="form-control" id="loginutilisateur"  >
                                                                                                                                                                                <input name="loginutilisateurupdate" value="<?php print $utilisateur[1] ?>" type="text" class="form-control" id="loginutilisateur"  required>

                                                                                                                                                                                   <label for="nomutilisateur">Nom de l'utilisateur :</label>
                                                                                                                                                                              
                                                                                                                                                                                <input name="nomnutilisateurupdate" value="<?php print $utilisateur[5] ?>" type="text" class="form-control" id="nomutilisateur"  required>
                                                                                                                                                                             
                                                                                                                                                                             
                                                                                                                                                                             
                                                                                                                                                                                <label for="Initialutilisateurupdate">Initial de l'utilisateur :</label>
                                                                                                                                                                              <input name="Initialutilisateurupdate" value="<?php print $utilisateur[4] ?>" type="text" class="form-control" id="Initialutilisateurupdate"  required>
                                                                                                                                                                                </div>
                                                                                                                                                                           

                                                                                                                                                                                <label for="psw"><b>Mot de passe</b></label>
                                                                                                                                                                                <input type="password" value="<?php  print encrypt_decrypt('decrypt',$utilisateur[3]); ?>"  placeholder="Enter Password" name="pswupdate" required>

                                                                                                                                                                                
                                                                                                                                                                              
                                                                                                                                                                      
                                                                                                                                                                                <label for="selutilisateursupdate"> Selectionné profil:</label>
                                                                                                                                                                                <select class="form-control input-sm" id="selutilisateursupdate" name="selutilisateursupdate">
                                                                                                                                                                                                    
                                                                                                                                                                                                    
                                                                                                                                                                                                        <option <?php if($utilisateur[2]=="Administrateur"){print "selected='selected'";} ?>>  Administrateur  </option>
                                                                                                                                                                                                        <option  <?php if($utilisateur[2]=="Commercial"){print "selected='selected'";} ?>> Commercial   </option>
                                                                                                                                                                                                      

                                                                                                                                                                                </select>
                                                                                                                                                                                </br>
                                                                                                                                                                                
                                                                                                                                                                                
                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                
                                                                                                                                                                                <input value="Modifier l'utilisateur" type="submit" class="form-control btn-info" id="submit" name="btn_update_utilisateurs" >
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                
                                                                                                                                                                                <input onclick="return confirm('Etes vous certain de vouloir supprimer cet utilisateur');" value="Supprimer l'utilisateur" type="submit" class="form-control btn-warning" id="submit" name="btn_delete_utilisateur" >
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
                                                            print "</tbody> </table> "
                                                                ?>

                                                                  
                                                                                                                                                        </div>