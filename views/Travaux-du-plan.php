
    <?php
                                    // ajouter les nom des corps de metier au select
         $nbcorpmetier=0;
       
         print "<div id='accordion1'>";
         foreach($Allcorps_metier as $corpmetier)
        {
            ?>  
            <div class="card">
           
            <div class="card-header backgroundmetier">
            <div class="row">
            
            <div class="col-xl-6">
              <a class="card-link " data-toggle="collapse" href="#collapse<?php print $corpmetier[0] ?>">
               <?php print $corpmetier[0] ?>
               </a>
               </div>
               <?php if( $corpmetier[1]=="1") { ?>
               <div ng-app="">
                <div class="col-xl-6">
                        <div class="checkbox">
                        <label  for="charclient<?php print $corpmetier[0] ?>">Peût etre à la charge du client :</label>
                        <input name="charclient<?php print str_replace(' ','*@!+-',$corpmetier[0]); ?>" id="charclient<?php print str_replace(' ','*@!+-',$corpmetier[0]); ?>" type="checkbox" />
                        </div>
                        <label ng-show="val<?php print $corpmetier[0] ?>" for="coutTravail<?php print $corpmetier[0] ?>">Coût pour le client:</label>
                        <input ng-show="val<?php print $corpmetier[0] ?>" name="coutmetier<?php print str_replace(' ','*@!+-',$corpmetier[0]); ?>" style="width:25%;" type="number" step="0.01" class="form-control" id="coutTravail<?php print $corpmetier[0] ?>"  >
                 </div>
                 </div>
        
               <?php } ?>
            

    </div>
    </div>
             
         
            <div id="collapse<?php print $corpmetier[0] ?>" class="collapse show" data-parent="">
              <div class="card-body">
              <?php
                                    // ajouter les nom des corps de metier au select
                                    print "<div id='accordion$corpmetier[0]'>";
                     foreach($Allregroupement as $regroupement1)
                     {      

                        if($corpmetier[0]==$regroupement1[0])
                        {
                            
                          
                            ?>  
                            <div class="card">
                                    <div class="card-header" data-toggle="collapse" href="#collapse<?php print $regroupement1[1] ?>">
                                    <a class="card-link" >
                                    <?php print $regroupement1[1] ?>
                                    
                                    </a>
                                    </div>
                                    <div id="collapse<?php print $regroupement1[1] ?>" class="collapse show" data-parent="#accordion<?php print $corpmetier[0] ?>">
                                    <div class="card-body">
                                    <?php

                                    print "  <table class='table table-responsivetable-hover table-primary'>
                                    <thead >
                                    <tr>
                                   
                                      <th class='text-center'>nom</th>
                                      <th class='text-center'>caracteristique</th>
                                      <th class='text-center'>Quantité</th>
                                      <th class='text-center'>Sélectionner pour le plan</th>
                                    </tr>
                                  </thead>
                                  
                                            <tbody>";
                                                        foreach($Alltravail as $travail)
                                                        {
                                                                if($regroupement1[1]==$travail[4])
                                                                {
                                                                ?> 
                                                                        <tr>
                                                                                <td > <?php print $travail[1] ?></td>
                                                                                <td> <?php print $travail[2] ?>
                                                                                            
                                                                                </td>

                                                                                <td> 
                                                                                <input type="number" min="1" name=<?php print("qte".$travail[6]); ?> id=<?php print("qte".$travail[6]); ?>>                
                                                                                </td>                                 

                                                                                 <td> 
                                                                                 <div class="custom-control custom-checkbox">
                                                                                      <input type="checkbox" name="<?php print($travail[6]); ?>" class="custom-control-input" id=<?php print($travail[6]); ?> >
                                                                                     <label class="custom-control-label" for=<?php print($travail[6]); ?>>Ajouter</label>
                                                                                </div>
                                  
                                                                                
                                                                                </td>
                                                                        </tr>

                                                                <?php
                                                                }




                                                    

                                                        } ?> 
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            <?php
                            
                            print "</div>";
                        }
                      
                    }
                         ?> 
              </div>
            </div>
          </div>
       
        
          <?php
          }   
         ?>  
  </div>
  