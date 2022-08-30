<?php
session_start();
include("head.php");

include_once "../controlleurs/conex.php";
include_once "../models/Corps_de_metier.class.php";
include_once "../models/Regroupement.class.php";
include_once "../models/plan_type.class.php";
include_once "../models/Travail.class.php";
$plan_type = new plan("","","","","","","","","","");
$Allplan= $plan_type->getall();
include_once "../models/Options.class.php";
$option=new Option("","","","","");
$AllOptions= $option->getall();  

$diver=$_SESSION["divers"];

?>

<style>
</style>
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
                                    <h3 class="modal-title">
                                    <?php print $_SESSION['resultquery']; 
                                    
                                    unset($_SESSION['resultquery']);
                                    ?>
                                    
                                    </h3>
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


<div ng-app="" class="container">
    <?php  if($_SESSION["user"]=="Administrateur"){include("NavBaradmin.php");} else {     include("NavBarCommercial.php"); }         ?>
   
    <form action="../controlleurs/Cdevis.php" method="post">
    <div  class="form-group">
      <div class="container" style="z-index:1000;opacity:0.9;position: fixed; top:95%;  background-color:#42bcf4;  width:75%; height:30%" >
         
            <button class="btn btn-success" style="opacity:1;position: fixed; top:95%;left:48%;" name="savedevis"> Enregistrer le devis </button>
            </div>
            

     <div class="row">
    <div class="col-sm-12" style="background-color:lavender;">
        <div class="row">
    
         <div class="col-sm-6" style="background-color:lavender;">
                 <div class="form-group">
                    <label for="Date">Date :</label>
                     <input name="Date" readonly type="text" value="<?php print( date('d/m/Y'));?>"  class="form-control" id="Date" type="text" required>
                </div>
                <div class="form-group">
                    <label for="user">Commercial :</label>
                    <input name="user" readonly type="text"  value="<?php print($_SESSION["User"]);?>"   class="form-control" id="user" type="text" required>
                </div>
         </div>
            <div class="col-sm-6" style="background-color:lavender;">  
                <div class="form-group">
                <label for="Plan">Plan :</label>
                <input name="Plan" readonly type="text" value="<?php print($_SESSION["plan_type"]);?>"  class="form-control" id="Plan" type="text" required>
                </div>
                <div class="form-group">
                <label for="nomclient">Client :</label>
                <input name="nomclient" readonly type="text"  value="<?php print($_SESSION["client"]);?>"   class="form-control" id="nomclient" type="text" required>
         </div>
    </div>

        </div>
    
    
    
    </div>
 

  </div>
     <div class="row">
    
    <div class="border col-sm-6 " style=""><h3 class="bg-primary w3-round w3-blue"><u>Adaptation au sol</u></h3>
                            <div class="form-group selDiv ">
                                <label  for="type_terrain">Sélectionner le type de terrain:</label>
                                <select class="total selectter form-control" name="type_terrain" class="custom-select-sm">
                                <option value="0">Montant Terrassement précis</option>
                                    <option value="3000">Terrain plat / 3000 €</option>
                                    <option value="4000">Terrain pentu / 4000 € </option>
                                    <option value="5000">Terrain très pentu / 5000 €</option>
                                </select>
                        </div> 

                        
                        <script>
                      //permet de desactiver le montant pour laptation si lon veu un montant plus précis
                                 
                        $('.selectter').change(function() 
                        {
                            var str = "";
                            $( "select option:selected" ).each(function() {
                         str = $( this ).val();
                             
                                  if(str=="0")
                                  {
                                    $("#montantterrassement").removeAttr('disabled');
                                    $("#montantterrassement").val(0);
                                  }  
                                  else {
                                    $("#montantterrassement").prop('disabled','true');
                                    $("#montantterrassement").val(str);
                                  }
                                  str="";
                                });
                         });
                        
                        </script>
                        <?php  
                        
                        foreach($Allplan as $plan)
                        {
                            //récuperer le bon plan
                            if($plan[1]==$_SESSION["plan_type"])
                            {

                               
                        $optionspe=json_decode( $plan[9]); 
                        
                        
                      
                        
                        ?>
                          <!-- prix du plan,  mis la pour eviter de refaire un for each du plan type -->
                          <input type="hidden" class="total" name="prix_plan_nu" value="<?php  print($plan[4]); ?>">
                                        <label for="montatadaptation">Montant terrassement précis :</label>
                                        <input style="width:25%;"  name="montantterrassement" type="number"  step="0.01" value="0.00"  min="0" class="form-control total" id="montantterrassement" type="text" >
                                        <hr>
                        Gros béton de fondation :
                        <input  style="width:25%;" name="fondation" value="<?php  print($optionspe[0]); ?>" type="number" step="0.01" min="0" class="form-control total">
                        fondation semelles fillante :
                        <input  readonly style="width:25%;" name="semelle_fillante" value="<?php  print($optionspe[1]); ?>"  type="number" min="0" step="0.01" class="form-control total"   >
                        fondation radier :
                        <input readonly style="width:25%;" name="radier" value="<?php  print($optionspe[2]); ?>" type="number" step="0.01" min="0" class="form-control total"   >
                        Vide sanitaire :
                        <input readonly style="width:25%;" name="videsanitaire" value="<?php  print($optionspe[3]); ?>" type="number" step="0.01" min="0" class="form-control total"   >
                     
                            <?php }}?>
                        <hr>
                        <h3 class="bg-primary w3-round w3-blue"><u>Aménagement accès</u></h3>
                        <div  class="row">
                            <div  class="col-sm-6">
                            
                            <div class="custom-control custom-radio">
                                <input type="radio" required checked="checked" ng-model="myVar" class="custom-control-input" id="rdiAucun" name="amenagement" value="rdiAucun">
                                <label class="custom-control-label" for="rdiAucun">Aucun</label>
                            </div> 
                            <div class="custom-control custom-radio">
                                <input type="radio" ng-model="myVar" class="custom-control-input" id="rdiencaillasement" name="amenagement" value="rdiencaillasement">
                                <label class="custom-control-label" for="rdiencaillasement">Empierrement</label>
                            </div> 
                            <div class="custom-control custom-radio">
                                <input type="radio" ng-model="myVar" class="custom-control-input" id="rdibeton" name="amenagement" value="rdibeton">
                                <label class="custom-control-label" for="rdibeton">Bétonnage</label>
                            </div> 
                            
                            
                            </div>
                            <div class="col-sm-6">
                            
                            <div   ng-switch="myVar">
                                <div ng-switch-when="rdiAucun">
                                          
                                </div>
                                <div ng-switch-when="rdiencaillasement">
                                            <label for="montatadaptation">Largeur accès :</label>
                                            <input style="width:40%;" ng-model="Largeurencail" min="1"  name="largeurencaill" type="number"   value="0.00"  required class="form-control" id="montatadaptation" type="text" >
                                                    
                                            Longueur accès :
                                            <input  style="width:40%;" ng-model="Longueurencail" min="1" name="longueurencaill"  type="number"  required class="form-control"   >
                                            Prix m^2 :
                                            <input  style="width:40%;" ng-model="prixencail=<?php  print($diver[0][1]); ?>" name="prixMencaill"  type="number"  class="form-control"   >
                                            Montant total empierrement :
                                            <input class="total" style="width:40%;"  readonly value="{{Largeurencail * Longueurencail * prixencail}}" name="totalencaill"  type="number"  class="form-control"   >
                                </div>
                                <div ng-switch-when="rdibeton">
                                <label for="montatadaptation">Largeur accès :</label>
                                            <input style="width:40%;" min="1" ng-model="largeurbetonnage" name="largeurbetonnage" type="number" required   value="0.00"   class="form-control" id="montatadaptation" type="text" >
                                                    
                                            Longueur accès :
                                            <input  style="width:40%;" min="1" ng-model="longueurbetonnage" name="longueurbetonnage"  type="number" required class="form-control"   >
                                            Prix m^2 :
                                            <input  style="width:40%;" ng-model="prixbetonnage=<?php  print($diver[0][2]); ?>" name="prixMbetonnage"  type="number" class="form-control"   >
                                            montant total bétonnage :
                                          
                                            <input class="total" readonly style="width:40%;" value="{{largeurbetonnage * longueurbetonnage * prixbetonnage  }}" name="totalbétonnage"  type="number" class="form-control"   >
                                </div>
                                </div>
                                        
                        </div>
                              <hr>
                              <hr>
                                          
                                            <hr>
                                            <br>
                                  
                                                    <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="opttrottoir" name="opttrottoir" value="">
                                                    <label class="custom-control-label" for="opttrottoir">Trottoir de propreté</label>
                                                </div>
                                               
                                       <br>
                                       <div class="hidden"   id="trottoir">
                                       <br>
                                             Largeur :
                                             <input id="largtro" style="width:40%;" name="largtro"  type="number" min=0  class="form-control"   >
                                             Longueur :
                                             <input id="longtro" style="width:40%;" name="longtro"  type="number" min=0   class="form-control"   >
                                             Prix m² :
                                             <input id="prixtro" value="<?php  print($diver[0][8]); ?>" min=0  readonly style="width:40%;" name="prixtro"  type="number"  class="form-control"   >
                                             Prix total :
                                             <input id="prixtrototal" style="width:40%;" name="prixtrototal" min=0   type="number"  class="form-control total"   >
                                        </div>
                                        <hr>
                                        <script>
                                        //cache les textbox pour le load de la page
                                            $(document).ready(function(){  
                                           
                                                $('#trottoir').hide();
                                            
                                            });

                         time=setInterval(function()
                                            {
                                            //affiche les texbox pour la remise
                                                    $("#opttrottoir").click(function () {
                                                        if ($(this).is(":checked")) {
                                                            $("#trottoir").show();
                                                  
                                                        } else {
                                                            $("#trottoir").hide();
                                                        
                                                        }
                                                    });
                                                    var longtro= $('#longtro').val();
                                                    var largtro= $('#largtro').val();
                                                    var prixtro= $('#prixtro').val();
                                                    $('#prixtrototal').val(Number(longtro*largtro*prixtro));


                             },1);
                                        </script>
                              <hr>  
                   
                              <div class="">
                              <hr>
                              <h3 class="bg-primary w3-round w3-blue"><u>Sélections options et travaux supplémentaires</u></h3>
                                            <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Description</th>
                                                    <th scope="col"></th>
                                                    <th scope="col">Quantité</th>
                                                    <th scope="col">Montant</th>
                                        
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 <?php
                                                    foreach($Allplan as $plan)
                                                        {
                                                            //récuperer le bon plan
                                                            if($plan[1]==$_SESSION["plan_type"])
                                                            {
                                                                //décode json option
                                                                $optionplan=json_decode( $plan[10]);
                                                                $nombreoption=1;
                                                                foreach($AllOptions as $touteopt)
                                                                {
                                                                  
                                                                            foreach($optionplan as $options)
                                                                                {
                                                                                
                                                                                    if($options[0]==$touteopt[0])
                                                                                    {

                                                                                        print("<tr>");
                                                                                            print("<td>");
                                                                                                print($touteopt[1]);
                                                                                            print("</td>");

                                                                                            print("<td>");
                                                                                            //retire l'espace de l'id
                                                                                                print("<input id='$touteopt[0]chc' name='$touteopt[0]chc' type='checkbox' >");
                                                                                            print("</td>");
                                                                                        
                                                                                            if($touteopt[4]==0)
                                                                                            {
                                                                                            print("<td>");
                                                                                                print("<input style='width:80%;' id='nb$touteopt[0]'  ng-init=nb$touteopt[0]='1' value='{{nb$touteopt[0]}}'  ng-model='nb$touteopt[0] ' name='nbopt$touteopt[0]' type='number' min='0'>");
                                                                                            print("</td>");
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                print("<td>");
                                                                                                print("<input style='width:80%;' id='nb$touteopt[0]'  ng-model='nb$touteopt[0]'  ng-init=nb$touteopt[0]='1' value='{{nb$touteopt[0]}}' name='nbopt$touteopt[0]' type='number' min='1' max='1'>");
                                                                                                print("</td>");
                                                                                           
                                                                                            }
                                                                                            
                                                                                            print("<td>");
                                                                                                print("<input type='text' style='width:100%;' class='sumoptions' id='total$touteopt[0]' ng-model='nb$touteopt[0]*$touteopt[3]'   name='totalopt$touteopt[0]'   >");
                                                                                            print("</td>");

                                                                                            print("<script>");
                                                                                            print("
                                                                                            var sumoptions=0;
                                                                         $('#".str_replace(" ","","$touteopt[0]chc")."').change(function() 
                                                                         {
                                                                             if($(this).is(':checked'))
                                                                             {  

                                                                                $('#".str_replace(" ","","nb$touteopt[0]")."').prop('disabled', true);                        
                                                                                sumoptions+=Number($('#".str_replace(" ","","total$touteopt[0]")."').val()); 
                                                                            $('#totaloptions').text(sumoptions.toFixed(2)+' €');
                                                                            $('#totaloptions').val(sumoptions.toFixed(2));
                                                                             }
                                                                             else{
                                                                                $('#".str_replace(" ","","nb$touteopt[0]")."').prop('disabled', false); 
                                                                                sumoptions-=Number($('#".str_replace(" ","","total$touteopt[0]")."').val()); 
                                                                                $('#totaloptions').text(sumoptions.toFixed(2)+' €');
                                                                                $('#totaloptions').val(sumoptions.toFixed(2));
                                                                                $('#".str_replace(" ","","nb$touteopt[0]")."').val(1);
                                                                             
                                                                             }
                                                                             if(sumoptions<0)
                                                                                {
                                                                                   
                                                                                    $('#totaloptions').text(sumoptions.toFixed(2)+' €');
                                                                                    $('#totaloptions').val(sumoptions.toFixed(2));
                                                                                }
                                                                         });


                                                                    
                                                                                            
                                                                                            
                                                                                            
                                                                                            ");
                                                                                               print("</script>");
                                                                                        print("</tr>");
                                                                                        $nombreoption++;
                                                                                    }


                                                                                }

                                                                }


                                                            }
                                                         




                                                        }

                                                    ?>
                                                    <tr>
                                                    <td>
                                                    TOTAL
                                                    </td>
                                                    <td>
                                                  
                                                    </td
                                                    ><td>
                                                 
                                                    </td>
                                                    <td>
                                                        <p class="total" id="totaloptions" >0 € </p>
                                                   
                                                    </td>
                                                    </tr>
                                            </tbody>
                                            </table>
                                        </div>
                </div>
   
    </div>
    <div class="border col-sm-6" style="">
    <h3 class="bg-primary w3-round w3-blue"><u>Travaux à la charge du client</u></h3>
    <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Description</th>
                                                    <th scope="col"></th>
                                                 
                                                    <th scope="col">Montant</th>
                                        
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 <?php
                                                 $type_maison;
                                                    foreach($Allplan as $plan)
                                                        {
                                                            //récuperer le bon plan
                                                            if($plan[1]==$_SESSION["plan_type"])
                                                            {
                                                                //décode json option
                                                                $metiersplan=json_decode( $plan[7]);
                                                                $type_maison=$plan[3];
                                                                foreach($metiersplan as $metier)
                                                                {
                                                                        print("<tr>");
                                                                            print("<td>");
                                                                                print($metier[0]);
                                                                            print("</td>");

                                                                        print("<td>");
                                                                        
                                                                              print("<input class='metierchargeclient' value='$metier[1]'  id='".str_replace(" ","","$metier[0]chc") ."' name='metier$metier[0]' type='checkbox' >");
                                                                         print("</td>");

                                                                         print("<td>");
                                                                             print("<input class='calculfinal calculchargeclient' readonly value='$metier[1] €'  name='prixmetier$metier[0]' type='text' >");
                                                                          print("</td>");


                                                                        print("<tr>");                 

                                                                        print("<tr> 
                                                                           <script>
                                                                        var summétier=0;
                                                                         $('#".str_replace(" ","","$metier[0]chc")."').change(function() 
                                                                         {
                                                                             if($(this).is(':checked'))
                                                                             {
                                                                                summétier+=Number($(this).val()); 
                                                                            $('#displaysum').text(summétier+' €');
                                                                            $('#displaysum').val(summétier);
                                                                             }
                                                                             else{
                                                                                summétier-=Number($(this).val()); 
                                                                                $('#displaysum').text(summétier+' €');
                                                                                $('#displaysum').val(summétier);
                                                                             }
                                                                         });
                                                                     
                                                                     </script>"); 
                                                                                       
                                                                }

                                                                print("<tr>");
                                                                print("<td>  TOTAL");
                                                                  
                                                                print("</td>");

                                                     

                                                             print("<td>");
                                                                 print("");
                                                              print("</td>");

                                                              print("<td>");
                                                              print("<p class='totalminus' id='displaysum'>0 €</p>");
                                                           print("</td>");


                                                            print("<tr>"); 

                                                            }
                                                         




                                                        }

                                                    ?>

                                            </tbody>
                                            </table>
                                            <h5><u><a href="#" data-toggle="tooltip" title="Sélectionner si la maison se trouve dans un lotissement et bénéficie de remise sur le prix des bornes EDF/SME ">Option lotissement</a></u></h5>

                                                       <input type="checkbox" id="chclotissement" name="chclotissement" value="">
                                            <hr>
                                            <h3 class="bg-primary w3-round w3-blue"><u>Assainissement</u></h3>
                                            <div ng-init="assainissement=<?php  print($diver[0][3]); ?>" > 
                                                                <div class="custom-control custom-radio">
                                                    <input type="radio" required ng-model="assainissement" class="custom-control-input" id="egout" name="assainissement" value="egout">
                                                    <label class="custom-control-label" for="egout">Egoût</label>
                                                </div> 
                                                <div class="custom-control custom-radio">
                                                    <input type="radio"  ng-model="assainissement" class="custom-control-input" id="fosse" name="assainissement" value="fosse">
                                                    <label class="custom-control-label" for="fosse">Fosse septique</label>
                                                </div> 
                                            
                                            </div>    
                            <div   ng-switch="assainissement">
                                                <div ng-switch-when="egout">
                                                <input style="width:40%;" readonly ng-model="egout" class="total"  name="egout" type="number" ng-init=ngegout='<?php  print($diver[0][3]); ?>'  value="{{ngegout}}"   class="form-control" id="egout1" type="text" >
                                                </div>
                                                <div ng-switch-when="fosse">
                                                <input style="width:40%;" readonly ng-model="fosse"  class="total" name="fosse" type="number"  ng-init=ngfosse='<?php $prixfosse=explode("/",$diver[0][4]); 

                                                if(strpos($type_maison,'f3') !== false)
                                                {
                                                    print($prixfosse[0]);
                                                }
                                                else if(strpos($type_maison,'f4') !== false)
                                                {
                                                    print($prixfosse[1]);
                                                }
                                                else if(strpos($type_maison,'f5') !== false)
                                                {
                                                    print($prixfosse[2]);
                                                } ?>'  value="{{ngfosse}}"    class="form-control" id="fossechargeclient" type="text" >
                                                </div>
                                                
                                            </div>

                                            <script>
                                            
                                            time=setInterval(function()
                                            {
                                                var total=0;
                                                var aChrclient=0;
                                                $(".total").each(function()
                                                 {
                                                     
                                                        total += Number($(this).val());

                                                    
                                                });
                                         

                                               
                                                        if ($("#chcremise").is(":checked")) {

                                                            aChrclient= $(".totalminus").val();
                                                var prixremise= $("#prixremise").val();
                                                $("#totalHt").val(Number(total-aChrclient-prixremise)+ " €");
                                                $("#totalhthidden").val(Number(total-aChrclient));
                                                $("#tva").val((Number(total-aChrclient-prixremise)*Number(<?php  print($diver[0][0]); ?>)/100).toFixed(2)+ " €" );

                                                var ttc = Number(total-aChrclient-prixremise)+Number(total-aChrclient-prixremise)*Number(<?php  print($diver[0][0]); ?>)/100;
                                                $("#TTC").val(ttc.toFixed(2)+" €");
                                                  
                                                        }
                                                        else{

                                                 aChrclient= $(".totalminus").val();
                                                
                                                $("#totalHt").val(Number(total-aChrclient)+ " €");
                                                $("#totalhthidden").val(Number(total-aChrclient));
                                                $("#tva").val((Number(total-aChrclient)*Number(<?php  print($diver[0][0]); ?>)/100).toFixed(2)+ " €" );

                                                var ttc = Number(total-aChrclient)+Number(total-aChrclient)*Number(<?php  print($diver[0][0]); ?>)/100;
                                                $("#TTC").val(ttc.toFixed(2)+" €");
                                                        }   
                                                        


                                            },1);
                                            $(document).ready(function(){
                                             $("#pdfbut").click(function() {
                                              $("#myContainer").append("<b>Appended text</b>.");
                                                        });

                                             });
                                       </script>

                                                 <hr>
                                            <h3 class="bg-primary w3-round w3-blue"><u>Prix</u></h3>
                                            Remise exeptionnel:
                                            <br>
                                                 <input type="checkbox" id="chcremise" name="chcremise" value="">
                                                 <br>
                                                 Code accès remise :
                                             <input id="code_remise" style="width:25%;" type="password" name="code_remise"  class="form-control"   >

                                       <br>
                                       <div class="hidden"   id="remise">

                                            
                                             POURCENTAGE :
                                             <input id="pourcentageremise" style="width:25%;" name="pourcentageremise"  type="number" min="0s" max="100" class="form-control"   >
                                             EUROS :
                                             <input id="prixremise" style="width:25%;" name="prixremise"  type="number"  class="form-control"   >
                                             <input  id="totalhthidden" style="width:25%;display: none;"    type="text"  class="form-control"   >
                                        </div>
                                        <hr>

                                        <script>


                                        $(document).ready(function(){
                                        $('[data-toggle="tooltip"]').tooltip();   
                                        });
                                        </script>
                                        <script>
                                        //cache les textbox pour le load de la page
                                            $(document).ready(function(){  
                                           
                                                $('#remise').hide();
                                            
                                            });


                                            //affiche les texbox pour la remise
                                                    $("#code_remise").keyup(function () {
                                                        if ($("#chcremise").is(":checked") &&  $("#code_remise").val()==<?php print("'".$diver[0]["code_remise"]."'"); ?> ) {
                                                            $("#remise").show();
                                                  
                                                        } else {
                                                            $("#remise").hide();
                                                        
                                                        }
                                                    });

                                                    $("#prixremise").keypress(function() 
                                                    {
                                                        $("#pourcentageremise").val(Number($("#prixremise").val())*1000/Number($("#totalhthidden").val()));
                                                    });

                                                         $("#pourcentageremise").change(function() 
                                                    {
                                                        $("#prixremise").val(Number($("#pourcentageremise").val())*Number($("#totalhthidden").val()/100));
                                                    });
                                        </script>

                                            Total HT :
                                             <input id="totalHt" style="width:25%;" name="totalHt"  type="text"   class="form-control"   >
                                             TVA :
                                             <input id="tva" style="width:25%;" name="tva"  type="text"  class="form-control"   >
                                             Total TTC :
                                            
                                             <input  id="TTC" style="width:25%;" name="totalttc"  type="text"  class="form-control"   >
                                             <hr>
                                             <h3 class="bg-primary w3-round w3-blue"><u>Montant Final :</u></h3>
 
                                        <?php
                                   //recuperre prix des travaux a charge du client
                                         $plan_type = new plan("","","","","","","","","","");
                                        $plan= $plan_type->getplan( $_SESSION["plan_type"] );

                                        $travaux_Du_Plan=json_decode($plan[0]["Travaux"]);

                                        $Travail = new Travail("","","","","","");
                                        $cout_charge_client=0;
                                        $cout_charge_clientloti=0;
                                foreach($travaux_Du_Plan[0] as $travail)
                                    {              
                                        $T=$Travail->gettravaux($travail);
                                                  
                                               

                                 if($T[0]["chargeclient"]==1){

                              
                                        //si loption lotissement est ativé
                                       
                                            $coutloti=explode("/",$T[0]["cout"]);
                                            $coutloti=$coutloti[1];
                                            $cout_charge_clientloti+=$coutloti;
                                       
                                            $cout=explode("/",$T[0]["cout"]);
                                            $cout=$cout[0];
                                            $cout_charge_client+=$cout;
                                        
                                
                                 }                                          
                      
                            }
       
                                            ?> 
                            
                            Total charge pour client :
                            <input  id="charge_client" style="width:25%;" name="charge_client"  type="text"  class="form-control"   >
                            Option dommage Ouvrage :
                            <input  id="option_dmg" style="width:25%;" name="option_dmg"  type="text"  class="form-control"   >
                            Côut total de l'ouvrage :
                            <input  id="cout_total_ouvrage" style="width:25%;" name="cout_total_ouvrage"  type="text"  class="form-control"   >
                            <script>
                                    
                                    time=setInterval(function()
                                            {
                                            //affiche les texbox pour la remise
                                            var charge_client=0;

                                                 
                                                   
                                                       if ($("#chclotissement").is(":checked")) {

                                                         
                                                                if ($("#fosse").is(":checked")) 
                                                                {
                                                                    charge_client= Number($("#fossechargeclient").val())+Number($(".totalminus").val())+Number(<?php print($cout_charge_clientloti); ?>) ;
                                                                    var ttc_charge = Number(charge_client)+Number(charge_client)*Number(<?php  print($diver[0]["tva"]); ?>)/100;
                                                                    $("#charge_client").val(ttc_charge.toFixed(2)+" €");
                                                                }     
                                                                else{
                                                                    charge_client= Number($("#egout1").val())+Number($(".totalminus").val())+Number(<?php print($cout_charge_clientloti); ?>) ;
                                                                    var ttc_charge = Number(charge_client)+Number(charge_client)*Number(<?php  print($diver[0]["tva"]); ?>)/100;
                                                                    $("#charge_client").val(ttc_charge.toFixed(2)+" €");
                                                                }      
                                                             
                                                        
                                                            
                                                        } 
                                                        else
                                                        {
                                                            if ($("#fosse").is(":checked")) 
                                                                {
                                                                    charge_client= Number($("#fossechargeclient").val())+Number($(".totalminus").val())+Number(<?php print($cout_charge_client); ?>) ;
                                                                    var ttc_charge = Number(charge_client);
                                                                    $("#charge_client").val(ttc_charge.toFixed(2)+" €");
                                                                }
                                                                else
                                                                {
                                                                    charge_client= Number($("#egout1").val())+Number($(".totalminus").val())+Number(<?php print($cout_charge_client); ?>) ;
                                                                    var ttc_charge = Number(charge_client);
                                                                    $("#charge_client").val(ttc_charge.toFixed(2)+" €");
                                                                }
                                                                
                                                        }

                 
                                                     var result = $("#TTC").val().split(' ');
                                                        $("#option_dmg").val((Number(result[0]).toFixed(2)*Number(<?php  print($diver[0]["tauxdommage"]); ?>)/100).toFixed(2)+" €");
                                                    var dmgouvrage=$("#option_dmg").val().split(' ');
                                                    var chgclient=$("#charge_client").val().split(' ');
                                                         $("#cout_total_ouvrage").val((Number(result[0])+(Number(dmgouvrage[0])+(Number(chgclient[0])))).toFixed(2)+" €")
                                                    },1);
                                                    </script>
                             
                              <hr>

                                
             
</div>
           


    </form>    
 </div>


