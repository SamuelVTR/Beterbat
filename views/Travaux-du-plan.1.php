<?php
                              if(!isset($_SESSION)) 
                              { 
                                  session_start(); 
                              }
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
                            
<script>
function myFunction() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
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

</br> <hr>
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Rechercher par nom">    
                            <table id="myTable" class="table table-bordered">
                            <thead>
                            <tr class="header">
                                <th class='text-center'>Nom</th>
                                <th class='text-center'>Sous-groupe</th>
                                <th class='text-center'>Incluse</th>
                            </tr>
                                    </thead>
                                        <tbody>
                            <?php
                                foreach($AllOptions as $option)
                                {
                                    ?> 
                                    
                                <tr>
                                    <td>  <?php print $option[1]  ?></td>
                                    <td ><?php print $option[2]  ?></td>
                                    <td >
            <div ng-app="">

      
                  <input type="checkbox" name="<?php print $option[0] ; ?>"  >
                    <label >Ajouter</label>
            

            <input  style="width:50%;" name="coutoption<?php print $option[0] ; ?>" value="<?php print $option[3]  ?>" type="number" step="0.01" class="form-control"  >
            </div>
                                    
                                    </td>
                                </tr>
                                <?php
                                }
                                
                                ?>
                                     </tbody>
                                 </table >