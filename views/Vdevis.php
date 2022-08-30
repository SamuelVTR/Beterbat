<?php
session_start();
include("head.php");
include_once "../controlleurs/conex.php";
include_once "../models/Corps_de_metier.class.php";
include_once "../models/Regroupement.class.php";
include_once "../models/plan_type.class.php";
include_once "../models/Client.class.php";
include_once "../models/Devis.class.php";
$plan_type = new plan("","","","","","","","","","");
$Allplan= $plan_type->getall();

$Clients = new Client("","","","","","","","","");
$AllClient= $Clients->getall();

$Devis = new Devis("","","","","","","","","","","","");
$AllDevis= $Devis->getall();

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
                            <div class="modal fade" id="Modalresultquery">
                                <div class="modal-dialog  modal-lg">
                                <div class="modal-content">
                                
                                    <!-- Modal Header -->
                                  
                                    <?php 
                                     print $_SESSION['resultquery']; 
                                   
                                    ?>
                                    
                                 
                                    
                                    
                                    <!-- Modal body -->
                                    <?php if($_SESSION['resultquery'] !="le devis a été Supprimé"){?>
                                    <iframe id="fred" style="border:1px solid #666CCC" target="_blank" title="PDF in an i-Frame" src="test.php" frameborder="1" scrolling="auto" height="1100" width="800" ></iframe>
                                    <?php } 
                                     unset($_SESSION['resultquery']);?>
                                    
                                    <!-- Modal footer -->
                                  
                                    
                                </div>
                                </div>
                            </div>
        <?php }?>

<div class="container">
    <?php  if($_SESSION["user"]=="Administrateur"){include("NavBaradmin.php");} else {     include("NavBarCommercial.php"); }         ?>
   
    <div class="rounded border border-success">



                <form action="../controlleurs/Cdevis.php" method="post">
               
                                        
                                    
            


    
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
        Créer un Devis
        
        <i class="fa fa-file-text"style="font-size:30px;color:black"></i>
        </button>

         <?php include("vajoutclient.php");        ?>  
    </div>

<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Rechercher Client">    
                            <table id="myTable" class="table table-striped table-hover">
                            <thead>
                            <tr class="header">
                                <th class="text-center">Date</th>
                                <th class="text-center" >Plan type</th>
                                <th class="text-center">Client</th>
                                <th class="text-center">Commercial</th>
                                <th class="text-center">Prix TTC</th>
                                <th class="text-center">Options</th>
                            </tr>
                                    </thead>
                                        <tbody>
                            <?php
                                foreach($AllDevis as $devis)
                                {
                                    ?> 
                                    
                                <tr>
                                <td hidden>  <?php print $devis[0]  ?> </td>
                                    <td>  <?php print $devis["date"]  ?> </td>
                                    <td > <?php print $devis["plan"]  ?> </td>
                                    <td > <?php print $devis["client"]  ?> </td>
                                    <td > <?php print $devis["commercial"]  ?> </td>
                                    <td > <?php
                                    //permet de séparer le prix et la remise
                                    $str_arr = explode ("/", $devis["prices"]); 
                                    print $str_arr["0"]    ?> </td>

                                        <td> 

                                       
                                        <button class="btn btn-primary" name='devispdf' value=<?php print($devis[0]); ?> onClick="removeRequired(this.form)"   width="30px">  <img src="image\adobe-acrobat-pdf-file-512.png" width="30px"> </button>
                                            
                                 <?php
                               if($devis["commercial"]==$_SESSION["User"] || $_SESSION["user"]=="Administrateur"  )
                                {
                                ?>
                                             <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModaldelete<?php print($devis[0]) ?>">
                                             <i class="far fa-trash-alt"></i> 
                                            </button>
                                            
                                            <div class="modal" id="myModaldelete<?php print($devis[0]) ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Supprimer ce devis ?</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>

                                                    <!-- Modal body -->
                                                    <div class="modal-body">
                                                        
                                                    <button name='deletepdf' class="btn btn-primary" value=<?php print($devis[0]) ?> onClick="removeRequired(this.form)"  > Supprimer </button> 
                                                    </div>

                                                    <!-- Modal footer -->
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                                                    </div>

                                                    </div>
                                                </div>
                                                </div>
                                <?php
                                }
                                ?>
                                         </td>
                                </tr>
                                <?php
                                }
                                ?>
                                     </tbody>
                                 </table >


<script>
function myFunction() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[3];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>

<!-- The Modal -->
                    <div class="modal" id="myModal">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title"></h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                            <label for="nom">Sélectionner Client:</label>
                                    <select class="form-control" name="selclient" style=" width:150px; " class="custom-select-sm">

                                    <?php
                                        foreach($AllClient as $Client ){

                                        print("<option  value='$Client[1]'>$Client[1]</option>");
                                        }
                                    ?>
                                
                                    </select>
                                    </br>
                                    
                                    <label > Sélectionner le Plan pour le devis :</label>
                                    <select class="form-control"  name="selPlantype" class="custom-select-sm">

                                    <?php
                                        foreach($Allplan as $plan ){

                                        print("<option  value='$plan[1]' > $plan[1]</option>");
                                        }
                                    ?>
                                
                                    </select>
                                    </div>  
                                    <input  type="submit" class="form-control btn btn-success" value="Commencer Devis" id="submit"  onClick="removeRequired(this.form)" name="adddev" >   
                     
                            

                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                            </div>

                            </div>
                        </div>   </div>
                 
                        </form>
</div>

