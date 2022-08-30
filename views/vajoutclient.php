<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalajoutclient">
    Créer Client
    <i class="fa fa-address-card" style="font-size:30px;color:black" aria-hidden="true"></i>

  </button>

  <!-- The Modal -->
  <div class="modal" id="myModalajoutclient">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Création d'un client</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">

    <div class="form-group">
      <label for="Nomclient">Nom Complet:</label>
      <input name="Nomclient" type="text" class="form-control" id="Nomclient" type="text" required>
    </div>
  
    <label for="Civilite">Civilité :</label>
      <select class="form-control input-sm" id="Civilite" name="Civilite">
                           
                            <option>  M. et Mme  </option>
                             <option> M.   </option>
                             <option> Mme   </option>
                             <option>  Mlle  </option>
                            
      </select>
      </br>
      <div class="form-group">
      <label for="Adresse">Adresse :</label>
      <input name="Adresse" type="text"  class="form-control" id="Adresse" type="text" required>
    </div>
    <div class="form-group">
      <label for="codepostal">Code postal :</label>
      <input name="codepostal" type="text"  class="form-control" id="codepostal" type="text" required>
    </div>
    <div class="form-group">
      <label for="Ville">Ville :</label>
      <input name="Ville" type="text"  class="form-control" id="Ville" type="text" required>
    </div>
    <div class="form-group">
      <label for="Pays">Pays :</label>
      <input name="Pays" type="text"  class="form-control" id="Pays" type="text" required>
    </div>
    <div class="form-group">

      <div class="form-group">
      <label for="Refcadastral">Réf. cadastral :</label>
      <input name="Refcadastral" type="text"  class="form-control" id="Refcadastral" type="text" >
    </div>
    <div class="form-group">
      <label for="Adressterrain">Adresse terrain :</label>
      <input name="Adressterrain" type="text"  class="form-control" id="Adressterrain" type="text" >
    </div>
    <div class="form-group">
      <label for="Ville">Ville du terrain :</label>
      <input name="Villeterrain" type="text"  class="form-control" id="Ville" type="text" >
    </div>
    <div class="form-group">
     
    <input  type="submit" class="form-control btn btn-success" id="submit" name="btn_add_Client" >
    </div>

 
 </div>  </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger success" data-dismiss="modal">Fermer</button>
        </div>
        
      </div>
    </div>
  </div>
