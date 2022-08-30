

<div ng-app="" ng-init="lien=[
{nom:'Jani',address:'Norway'},
{nom:'Gestion des référentiel de travaux',address:'vreferentiel-travaux_corps-metier.php'},
{nom:'Gérer utilisateurs',address:'vcollapseadmin.php'}]">
<div class="row"> 
<div class="col-sm-4"></div>
<div class="col-sm-4">
  <ul class="list-group">
    <li class="list-group-item" ng-repeat="x in lien">
   
       <a href={{x.address}} class="btn btn-info" role="button"> {{x.nom}} </a>
    </li>
  </ul>
  </div>
  <div class="col-sm-4"></div>
</div>