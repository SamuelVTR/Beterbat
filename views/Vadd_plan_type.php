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
<style>
.filterDiv {



 
  margin: 2px;
  display: none;
}

.show {
  display: block;
}

.container {
  margin-top: 20px;
  overflow: hidden;
}

/* Style the buttons */
.btn {
  border: none;
  outline: none;
  padding: 12px 16px;
  background-color: #f1f1f1;
  cursor: pointer;
}

.btn:hover {
  background-color: #ddd;
}

.btn.active {
  background-color: #656;
  color: white;
}
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
        <style>
  body {
      position: relative;
  }
  ul.nav-pills {
    top:200px;
      position: fixed;
  }

  </style>

    <?php  if($_SESSION["user"]=="Administrateur"){include("NavBaradmin.php");} else {     include("NavBarCommercial.php"); }         ?>
  

        <!-- FORM -->
    <form action="../controlleurs/Cplan_type.php" enctype="multipart/form-data" method="post">
    
<div class="container">
    <div class="form-group">
      <label for="nom_planom">Nom du plan type:</label>
      <input style="width:33%;" name="nom_plan"  class="form-control" id="nom_plan" type="text" required>
    </div>
    <div class="form-group">
      <label for="Description">Description:</label>
      <input style="width:33%;" name="Description" type="text" class="form-control" rows="5" id="Description" required>
    </div>
    <div class="form-group">
        <label  for="type">Type Maison</label>
        <select name="type" class="custom-select-sm">
           
            <option value="f3">F3</option>
            <option value="f4">F4</option>
            <option value="f5">F5</option>
        </select>
    </div>
    <div class="form-group">
      <label for="Enveloppe">Prix enveloppe sanitaire:</label>
      <input style="width:33%;" name="Enveloppe" type="number" min="0" class="form-control" rows="5" id="Enveloppe">
    </div>
    <div class="form-group">
        <label  for="TarifHT">Tarif HT:</label>
        <input style="width:33%;" name="TarifHT" type="number" step="0.01" class="form-control" id="TarifHT" required>
    </div>
    
Gros béton de fondation :
<input  style="width:25%;" name="fondation"  type="number" value="0"  step="0.01" class="form-control" min="0"  >
 fondation semelles fillante :
<input  style="width:25%;" name="semelle_fillante"  type="number" value="0"  step="0.01" class="form-control" min="0"  >
 fondation radier:
<input  style="width:25%;" name="radier"  type="number" value="0"  step="0.01" class="form-control" min="0"  >
  Vide sanitaire:
<input  style="width:25%;" name="Videsanitaire"  type="number" value="0" step="0.01" class="form-control" min="0"  >

    <div class="form-group">
    Sélectionner fichier PDF:
<div class="custom-file">
    <input style="width:33%;" name="myfile" type="file" class="custom-file-input" id="customFile">
    <label class="custom-file-label" for="customFile" required> Choisir PDF plan </label>
  </div>

    <script>
            $('#customFile').on('change',function(){
                //get the file name
                var fileName = $(this).val();
                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fileName);
            })
        </script>
  </div>   
  
  
      <div  class="form-group">
      <div class="container" style="z-index:1000;opacity:0.9;position: fixed; top:92%;  background-color:#42bcf4;  width:75%; height:30%" >
            <input class="btn btn-success" style="opacity:1;position: fixed; top:95%;left:48%;"  type="submit" name="btn_add_plan" value="Enregistrer ce plan"/>
            </div>
            
</div>


    <div id="myBtnContainer">

  <button type="button" class="btn" onclick="filterSelection('travaux')"> Travaux du plan </button>
  <button type="button" class="btn" onclick="filterSelection('options')"> Options</button>


</div>
   

<div class="filterDiv options"><?php include("Travaux-du-plan.1.php");?></div>
  <div class="filterDiv travaux"> <?php include("Travaux-du-plan.php");  ?></div>



  

</div>
   
 



</form>





<script>
filterSelection("travaux")
function filterSelection(c) {
  var x, i;
  x = document.getElementsByClassName("filterDiv");
  if (c == "all") c = "";
  for (i = 0; i < x.length; i++) {
    w3RemoveClass(x[i], "show");
    if (x[i].className.indexOf(c) > -1) w3AddClass(x[i], "show");
  }
}

function w3AddClass(element, name) {
  var i, arr1, arr2;
  arr1 = element.className.split(" ");
  arr2 = name.split(" ");
  for (i = 0; i < arr2.length; i++) {
    if (arr1.indexOf(arr2[i]) == -1) {element.className += " " + arr2[i];}
  }
}

function w3RemoveClass(element, name) {
  var i, arr1, arr2;
  arr1 = element.className.split(" ");
  arr2 = name.split(" ");
  for (i = 0; i < arr2.length; i++) {
    while (arr1.indexOf(arr2[i]) > -1) {
      arr1.splice(arr1.indexOf(arr2[i]), 1);     
    }
  }
  element.className = arr1.join(" ");
}

// Add active class to the current button (highlight it)
var btnContainer = document.getElementById("myBtnContainer");
var btns = btnContainer.getElementsByClassName("btn");
for (var i = 0; i < btns.length; i++) {
  btns[i].addEventListener("click", function(){
    var current = document.getElementsByClassName("active");
    current[0].className = current[0].className.replace(" active", "");
    this.className += " active";
  });
}
</script>




















<!-- End container -->
   