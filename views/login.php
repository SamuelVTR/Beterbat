<?php
 if(!isset($_SESSION)) 
 { 
     session_start(); 
 }
include("head.php");
?>
<!--Le formulaire pour la connection de tous les utilisateurs-->
      
      					

               
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
              
                                    Connectez vous    
                                    </h4>
                                
                                    </div>
                             
                                    <!-- Modal body -->
                                 
  
  
                                    <form action="./controlleurs/clogin.php" method="post">                     
     
     
              
                   
                    
           
                 
                              <input name="prenom" placeholder="Login" class="form-control text-center zoneSaisie"  type="text"/>
                
                        
     
                        
                         <input name="password" placeholder="Mot de passe" class="form-control text-center zoneSaisie" type="password"/>
             
         

                         <input name="btnLogin" class="btn btn-success btn btn-success btn-lg" type="submit" value="Connexion"/>
             
        
               
     </form>    
                                    
                                </div>
                                </div>
                            </div>

 
 
