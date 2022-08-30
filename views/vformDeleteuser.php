

<div ng-app="myApp" ng-controller="utilisateurCtrl">
  
    <table class="table table-bordered table-hover">

         <tr>
            <th>Login</th>
            <th>Nom</th>
            <th>Initial</th>    
             <th>status</th>
            <th>Mots de passe</th>
            </tr>

            <tr  ng-repeat="x in utilisateur">
           
           <form method="post" action="./controlleurs/cadmin.php" >
                <td>  <input name="login<?php echo "{{ x.id }}";?>"   type="text" value="{{ x.login }}"> </td>
              
                <td>  <input name="nom<?php echo "{{ x.id }}";?>" type="text" value="{{ x.nom }}">   </td>
                <td>  <input name="initial<?php echo "{{ x.id }}";?>"  type="text" value="{{ x.initial }}">   </td>

                  <td> <input name="statut<?php echo "{{ x.id }}";?>" type="text" value="{{ x.statut }}"> </td>      
                <td>  <input name="password<?php echo "{{ x.id }}";?>" type="password" value="{{ x.password }}">   </td>      
                <td>  <input name="submit_modif_user<?php echo "{{ x.id }}";?>" type="submit"  value="confirmer modification">  </td>
                
                </form >  
            </tr>

    

</div>  


<script>
var app = angular.module('myApp', []);
app.controller('utilisateurCtrl', function($scope, $http) {
    $http.get("../controlleurs/sql_alluser.php")
    .then(function (response) {$scope.utilisateur = response.data.records;});


    $scope.admin = function($status) {
      
      if($status=="Commercial")
        return "selected";
    };
    });
</script>