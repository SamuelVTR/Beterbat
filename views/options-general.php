 <!-- The Modal -->
 <div class="modal fade" id="myModaloptiongrl">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Options général</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">


        <form action="../controlleurs/Coptions-general.php" method="post">   
<?php
$diver=$_SESSION["divers"];
?>

           <!-- TVA -->
            <div class="form-group">
            <label for="option_tva">TVA :</label>
            <input type="number" value="<?php print($diver[0]["tva"]); ?>" step="0.01" class="form-control" id="option_tva" min="0" max="100"  name="option_tva">
            </div>
       
          <!-- Encaillqssement -->
           <div class="form-group">
            <label for="option_encaillassement">Prix Encaillassement :</label>
            <input type="Number" value="<?php print($diver[0]["encaillassement"]); ?>" step="0.01" class="form-control" id="option_encaillassement" min="0" name="option_encaillassement">
            </div>
 <!-- beton -->
 <div class="form-group">
            <label for="option_betonnage">Prix betonnage :</label>
            <input type="Number" value="<?php print($diver[0]["betonnage"]); ?>" step="0.01" class="form-control" id="option_betonnage" min="0" name="option_betonnage">
            </div>



           <!-- trottoir -->
           <div class="form-group">
            <label for="troittoir">Trottoir :</label>
            <input type="Number" value="<?php print($diver[0]["trottoir"]); ?>" step="0.01" class="form-control" id="trottoir" min="0" name="trottoir">
            </div>
        
             <!-- egout -->
 <div class="form-group">
            <label for="option_égoût">Prix liaison égoût :</label>
            <input type="Number" value="<?php print($diver[0]["egout"]); ?>" step="0.01" class="form-control" id="option_egout" min="0" name="option_egout">
            </div>

  <!-- fosse -->
  <div class="form-group">
  <?php
  
  $fosse=explode("/",$diver[0]["fosse"])

  ?>
            <label for="fausse">Prix fosse F3:</label>
            <input type="Number" value="<?php print $fosse[0]; ?>" step="0.01" class="form-control" id="option_fosse1" min="0" name="option_fosse1">
            <label for="fausse2">Prix fosse F4:</label>
            <input type="Number" value="<?php print $fosse[1]; ?>" step="0.01" class="form-control" id="option_fosse2" min="0" name="option_fosse2">
            <label for="fausse3">Prix fosse F5:</label>
            <input type="Number" value="<?php print $fosse[2]; ?>" step="0.01" class="form-control" id="option_fosse3" min="0" name="option_fosse3">
            </div>

             <!-- taux dommage -->
  <div class="form-group">
            <label for="option_taux_dommage">Taux dommage ouvrage :</label>
            <input type="Number" value="<?php print($diver[0]["tauxdommage"]); ?>" step="0.01" class="form-control" id="option_taux_dommage" min="0" name="option_taux_dommage">
            </div>
    <!-- code remise -->
    <div class="form-group">
            <label for="option_taux_dommage">Code remise :</label>
            <input type="text" value="<?php print($diver[0]["code_remise"]); ?>" class="form-control" id="code_remise"  name="code_remise">
            </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
          <input type="submit" class="btn btn-success" class="form-control" id="save-opt-gene"  name="save-opt-gene">
        </div>
        </form>
      </div>
    </div>
  </div>