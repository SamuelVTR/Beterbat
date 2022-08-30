<?php
session_start();
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

                                    // ajouter les nom des corps de metier au select
         $nbcorpmetier=0;
       
         print "<div id='accordion1'>";
         foreach($Allcorps_metier as $corpmetier)
        {
            ?>  
            <div class="card">
            <div class="card-header" data-toggle="collapse" href="#collapse<?php print $corpmetier[0] ?>">
              <a class="card-link" >
               <?php print $corpmetier[0] ?>
              </a>
            </div>
            <div id="collapse<?php print $corpmetier[0] ?>" class="collapse show" data-parent="#accordion1">
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
                                    foreach($Alltravail as $travail)
                            {
                                if($corpmetier[0]==$travail[0])
                                {
                                    ?>  
                                    <div class="card">
                                            <div class="card-header" data-toggle="collapse" href="#collapse<?php print $travail[1] ?>">
                                            <a class="card-link" >
                                            <?php print $travail[1] ?>
                                            </a>
                                            </div>
                                            <div id="collapse<?php print $travail[1] ?>" class="collapse show" data-parent="#accordion<?php print $corpmetier[0] ?>">
                                            <div class="card-body">
                                            </div>
        
        
                                    <?php
                                    }
                                    print "</div>";

                            } ?> 
                                    </div>


                            <?php
                            
                            print "</div>";
                        }
                        else
                        {     print "<div id='accordion$regroupement1[1]'>";
                            foreach($Alltravail as $travail)
                            {
                                if($corpmetier[0]==$travail[0])
                                {
                                    ?>  
                                    <div class="card">
                                            <div class="card-header" data-toggle="collapse" href="#collapse<?php print $travail[1] ?>">
                                            <a class="card-link" >
                                            <?php print $travail[1] ?>
                                            </a>
                                            </div>
                                            <div id="collapse<?php print $travail[1] ?>" class="collapse show" data-parent="#accordion<?php print $corpmetier[0] ?>">
                                            <div class="card-body">
                                            </div>
        
        
                                    <?php
                                    }
                                    print "</div>";

                            }

                        }
                    }
                         ?> 
              </div>
            </div>
          </div>


          <?php
          }   
         ?>  