<?php
session_start();
include_once "../models/plan_type.class.php";
include_once "../models/Client.class.php";
include_once "../models/Devis.class.php";
include_once "../models/Options.class.php";
include_once "../controlleurs/conex.php";
include_once "../models/Corps_de_metier.class.php";
include_once "../models/Travail.class.php";
include_once "../models/Regroupement.class.php";


include('../TCPDF/tcpdf.php');

// Extend the TCPDF class to create custom Header and Footer


class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $html = '';
     $this->writeHTMLCell( 0,  0, '',  '', $html,  0,  1,  0, true,  '',  true);
  }
    

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 0, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);



// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 003');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data


// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)


// ---------------------------------------------------------

// set font
$pdf->SetFont('times', 'BI', 12);

// add a page



try {      
              
    $ql = "SELECT * FROM divers";
    $prepTest = $dbCon->prepare($ql);
    $prepTest->execute();
    $result = $prepTest->fetchAll();  
    
   
    


}
catch (Exception $ex) {
    //$dbCon=null;
     $e = oci_error();
     trigger_error(htmlentities($e['message']), E_USER_ERROR);
     $_SESSION['resultquery']=$ex;
     return false; 
 }


 $tva=$result[0]["tva"];

$plan_type = new plan("","","","","","","","","","");
$plan= $plan_type->getplan($_SESSION["devis"][0]["plan"]);
$assainissement= $_SESSION["devis"][0]["assainissement"];
//envellope sanitaire
$prixenvellope_sanitaire=$plan[0]["Branchement"];
$prixenvellope_sanitaire=explode("/",$prixenvellope_sanitaire);

$travaux_Du_Plan=json_decode($plan[0]["Travaux"]);
$metier_chargeclient=json_decode($_SESSION["devis"][0]["metier_chargeclient"]);

$Corps_de_metier = new Corps_de_metier("","","");
$Allmetier = $Corps_de_metier->getall();

$Regroupement = new Regroupement("","","");
$Allregroupement = $Regroupement->getall();


//page 1
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();








$pdf->SetPrintHeader(true);
$pdf->SetPrintFooter(true);
$Travail = new Travail("","","","","","");
$html="<style>

div {
    margin: 0px;
    padding: 0px;
}
</style>
<br><br><br>
";
$hearder = '
                     <div  style="text-align:center;font-size: 1.3em;" >NOTICE DESCRIPTIVE</div>
                     <div  style="text-align:center;font-size: 0.8em;" >'.$_SESSION["devis"][0]["client"].'--'.$_SESSION["devis"][0]["plan"].'     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.$_SESSION["devis"][0]["date"]      .'</div>
        <table    style="background-color:#aaaaaa;" border-collapse: collapse; padding: 5px; border="1">
            <tr>
                <td valign="center"  style="text-align:center;" rowspan="3">DESIGNATION DES OUVRAGES ET FOURNITURE</td>
                <td valign="center"  style="text-align:center;">DISPOSITIONS ADOPTEES ET INDICATIONS A DONNER</td>
                <td valign="center" style="text-align:center;">    COMPRIS DANS LE PRIX CONVENU  </td>
                <td valign="center" style="text-align:center;">   Non COMPRIS DANS LE PRIX CONVENU  </td>
                <td valign="center" style="text-align:center;">  Cout des ouvrages et fournitures non COMPRIS DANS LE PRIX CONVENU  </td>
            </tr>
        </table> <br>';
        $html =$hearder;
$cout_charge_client=0;
foreach($Allmetier as $metier)
{

    //permet decrire le cout du metier si il est a la charge du client 
    $trouvé=false;
                foreach($metier_chargeclient as $metiercharge)
            {
                if($metiercharge[0]==$metier["nom_corps_metier"])
                {


                    $prix_metier_charge=explode(" ",$metiercharge[1]);

                    $prix_metier_avc_tva=$tva/100*$prix_metier_charge[0]+$prix_metier_charge[0];

                    $html.='<table>
                                    <tr>
                                        <td style="background-color:grey;border-left: solid 1px black ;border-bottom: solid 1px black ;border-top: solid 1px black;text-align:left;font-size: 1.8em;"  colspan="3">'.$metier["nom_corps_metier"].'</td>
                                        <td style="border-bottom: solid 1px black ;border-top: solid 1px black;"  ></td>
                                        <td style="border-bottom: solid 1px black ;border-top: solid 1px black;border-right: solid 1px black;font-size: 1.3em;text-align:right;">' .number_format($prix_metier_avc_tva, 2, ',', ' ').' €</td>
                                    </tr>
                             </table>';
                     //si trouvé : le metier est à la charge du client et on break pour eviter des repetitions
                    $trouvé=true;
                    //add to cout charge client
                    $cout_charge_client+=$prix_metier_avc_tva;
                    break;
                }  
            }
            //si pas trouvé cela signifie que le metier nest pas a la charge du client 
            if(!$trouvé){
                $html.='<table>
                <tr>
                    <td style="background-color:grey;border-left: solid 1px black ;border-bottom: solid 1px black ;border-top: solid 1px black;text-align:left;font-size: 1.8em;"  colspan="3">'.$metier["nom_corps_metier"].'</td>
                    <td style="border-bottom: solid 1px black ;border-top: solid 1px black;border-right: solid 1px black;" colspan="2" ></td>
   
                </tr>
         </table>';
            }

   
    $html.=' <span  style=";font-size: 1.0em;margin: 0px;display: block;"  >'.$metier["remarque"].'</span><br  >';
    foreach($Allregroupement as $regroupement)
    {
        if($regroupement["nom_corps_metier"]==$metier["nom_corps_metier"])
        { 
            
            $html.= ' <table  >';
            $html.='<tr    ><td align="center" style=" border: 1px solid black;font-size: 1.5em;" colspan="5" >'.$regroupement["nom_regroupement"].'</td></tr>';
           
            $numTrav=0;
                foreach($travaux_Du_Plan[0] as $travail)
                {              
                    $T=$Travail->gettravaux($travail);
                        if($T[0]["nom_regroupement"]==$regroupement["nom_regroupement"] )
                        {          
                            $html.= '<tr  >';                        
                                 $html.='<td style=" border-left:1px solid black ;border-right:1px solid black;" >'.$T[0]["nom_travail"].'</td>';   
                                 $html.='<td style=" border-left:1px solid black;border-right:1px solid black;">'.$T[0]["caracteristique"].'</td>';   

                               

                                 if($T[0]["chargeclient"]==1)
                                 {

                                    $cout_travaux_charge_client_tva=0;
                                        //si loption lotissement est ativé
                                        if($_SESSION["devis"][0]["lotissement"]=="1")
                                        {
                                            $coutloti=explode("/",$T[0]["cout"]);
                                            $coutloti=$coutloti[1];
                                            $cout_travaux_charge_client_tva=$tva/100*$coutloti+$coutloti;
                                        }
                                        else
                                        {
                                            $cout=explode("/",$T[0]["cout"]);
                                            $cout=$cout[0];
                                            $cout_travaux_charge_client_tva=$tva/100*$cout+$cout;
                                        }

                                    //fosse septique ou egout 
                                    if($T[0]["nom_travail"]=="fosse septique")
                                    {
                                        $prixassainissement=explode("-",$assainissement);


                                        $prixassainissementTVA=$tva/100*$prixassainissement[1]+$prixassainissement[1];
                                       
                                        $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;"></td>'; 
                                        $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;">'.$travaux_Du_Plan[1][$numTrav].'</td>'; 
                                        $html.='<td style="  text-align:center;border-left:1px solid black;border-right:1px solid black;">'.number_format($prixassainissementTVA, 2, ',', ' ').'€</td>'; 
                                        $cout_charge_client+=$prixassainissementTVA;
                                    }
                                    else
                                    {
                                        $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;"></td>'; 
                                        $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;" >'.$travaux_Du_Plan[1][$numTrav].'</td>';
                                        $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;">'.number_format($cout_travaux_charge_client_tva, 2, ',', ' ').'€</td>';   
                                        $cout_charge_client+=$cout_travaux_charge_client_tva;
                                    }
                                 }
                                 else
                                 {

                                    $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;">'.$travaux_Du_Plan[1][$numTrav].'</td>';   
                                    $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;"></td>';
                                    
                                    //enveloppe sanitaire
                                    if($T[0]["nom_travail"]=="enveloppe sanitaire")
                                    {
                                        $html.='<td style="  text-align:center;border-left:1px solid black;border-right:1px solid black;">'.$prixenvellope_sanitaire[1].'</td>'; 
                                    }
                                    else
                                    {
                                            $html.='<td style="  text-align:center;border-left:1px solid black;border-right:1px solid black;"></td>'; 
                                    }

                                    

                                 }
                                  
                            $html.= '</tr>';
                                                
                         }
                        $numTrav++;
                }
                $html.= '<tr><td  height="0" colspan="5" style="border-top: 1px solid black;"></td>   </tr>';   
                $html.= '</table><br  >';
        }
    
    }
   



}
 ///******************************************* */travaux supplementaire
 $html.= ' <table  >';
 
 $html.='<table>
 <tr>
     <td style="background-color:grey;border-left: solid 1px black ;border-bottom: solid 1px black ;border-top: solid 1px black;text-align:left;font-size: 1.8em;"  colspan="3">Options et travaux supplementaires</td>
     <td style="border-bottom: solid 1px black ;border-top: solid 1px black;border-right: solid 1px black;" colspan="2"  ></td>

 </tr>
</table>';
 $html.='<tr    ><td align="center" style=" border: 1px solid black;font-size: 1.5em;" colspan="5" >Options et travaux supplementaires</td></tr>';

 $opt_supp=json_decode($_SESSION["devis"][0]["options"]);
     foreach($opt_supp as $opt)
     {       
         $options = new option("","","","","");
         $the_opt= $options->getopt($opt[0][0]);                        
                 $html.= '<tr = >';                        
                      $html.='<td style=" border-left:1px solid black ;border-right:1px solid black;" >'.$the_opt[0]["nom"].'</td>';   
                      $html.='<td style=" border-left:1px solid black;border-right:1px solid black;"></td>';   
                         $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;">X</td>';   
                         $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;"></td>';    
                         $html.='<td style="  text-align:center;border-left:1px solid black;border-right:1px solid black;"></td>';               
                 $html.= '</tr>';                                      
     }

//affiher le prix de l'acces
     $amenagementacces=json_decode($_SESSION["devis"][0]["amenagement_acces"]);
     if($amenagementacces[1][0]!="aucun")
     {
        $html.= '<tr = >';                        
        $html.='<td style=" border-left:1px solid black ;border-right:1px solid black;" >'.$amenagementacces[1][0].' accès</td>';   
        $html.='<td style=" border-left:1px solid black;border-right:1px solid black;"> Surface :'.$amenagementacces[1][1]*$amenagementacces[1][2].' m²</td>';   
           $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;">X</td>';   
           $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;"></td>';    
           $html.='<td style="  text-align:center;border-left:1px solid black;border-right:1px solid black;">'.$amenagementacces[1][4].' €</td>';               
   $html.= '</tr>';  
        
     }
     if($amenagementacces[0][0]!="aucun")
     {
        $html.= '<tr = >';                        
        $html.='<td style=" border-left:1px solid black ;border-right:1px solid black;" >Trottoir de propreté</td>';   
        $html.='<td style=" border-left:1px solid black;border-right:1px solid black;"> Surface : '.$amenagementacces[0][0]*$amenagementacces[0][1].' m²</td>';   
           $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;">X</td>';   
           $html.='<td style=" text-align:center;border-left:1px solid black;border-right:1px solid black;"></td>';    
           $html.='<td style="  text-align:center;border-left:1px solid black;border-right:1px solid black;">'.$amenagementacces[0][3].' €</td>';               
   $html.= '</tr>';  
        
     }






     $html.= '</table>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// ****************************resumé devis**************************
$pdf->AddPage();
$resume_devis_html = $hearder;
;
$resume_devis_html.='<br><br><br><table     border-collapse: collapse;  border="1">
<tr>
    
    <td colspan="3" style="text-align:center;">Montant du prix convenu pour les ouvrages de la colonne-03</td>
    <td style="text-align:center;">EN EUROS</td>
    <td style="text-align:center;">';
    // separe la remise du prix 
    $prix=explode("/",$_SESSION["devis"][0]["prices"]);
    // separe le signe de leuros du prix
    $remise=$prix[1];
    $prix=explode(" ",$prix[0]);
    $prix_sans_remise=$prix[0]+$remise;
    $resume_devis_html.=number_format($prix_sans_remise, 2, ',', ' ').' €';
    $resume_devis_html.='</td>
</tr>';
//***************************************** */remise exeptionnelle si la remise est diferente de 0
if($remise!=0)
{
    $resume_devis_html.='<tr>
    <td colspan="3" style="text-align:center;">REMISE EXCEPTIONNELLE</td>
    <td style="text-align:center;">EN EUROS</td>
    <td style="text-align:center;">';
    // separe la remise du prix 
    $prix=explode("/",$_SESSION["devis"][0]["prices"]);
    // separe le signe de leuros du prix
    $remise=$prix[1];
    $resume_devis_html.=number_format($remise, 2, ',', ' ').' €';
    $resume_devis_html.='</td>
</tr>';

//********************************************* */nouveau montant 
$resume_devis_html.='<tr> 
<td colspan="3" style="text-align:center;"><p> <b> NOUVEAU Montant du prix convenu pour les ouvrages de la colonne-03</b></p></td>
<td style="text-align:center;">EN EUROS</td>
<td style="text-align:center;"> <b>';
// separe la remise du prix 
$prix=explode("/",$_SESSION["devis"][0]["prices"]);
// separe le signe de leuros du prix
$remise=$prix[1];
$prix=explode(" ",$prix[0]);
$prix_avec_remise=$prix[0];
$resume_devis_html.=number_format($prix_avec_remise, 2, ',', ' ').' €';
$resume_devis_html.='</b></td>
</tr>';

}

//********************************* */option dommage ouvrage 
$resume_devis_html.='<tr>
<td colspan="3" style="text-align:center;">Dommage Ouvrage</td>
<td style="text-align:center;">EN EUROS</td>
<td style="text-align:center;">';
// separe la remise du prix 

$prix=explode("/",$_SESSION["devis"][0]["prices"]);
// separe le signe de leuros du prix
$remise=$prix[1];
$prix=explode(" ",$prix[0]);
$prix_avec_remise=$prix[0];
$tauxdommage=$result[0]["tauxdommage"];
$prix_dommage_ouvrage=$prix_avec_remise*$tauxdommage/100;
$resume_devis_html.=number_format($prix_dommage_ouvrage, 2, ',', ' ').' €';
$resume_devis_html.='</td>
</tr>';

//********************************* */cout ouvrage charge client 


$resume_devis_html.='<tr>
<td colspan="3" style="text-align:center;">Côut des ouvrages à la charge du maître d\'ouvrage de la colonne-05</td>
<td style="text-align:center;">EN EUROS</td>
<td style="text-align:center;">';
$resume_devis_html.=number_format($cout_charge_client, 2, ',', ' ').' €';
$resume_devis_html.='</td>
</tr>';
//********************************* */cout total ouvrage  


$resume_devis_html.='<tr>
<td colspan="3" style="text-align:center;">Côut total de l\'ouvrage</td>
<td style="text-align:center;">EN EUROS</td>
<td style="text-align:center;">';
$total_ouvrage=$cout_charge_client+$prix_dommage_ouvrage+$prix_avec_remise;
$resume_devis_html.=number_format($total_ouvrage, 2, ',', ' ').' €';
$resume_devis_html.='</td></tr>';
$resume_devis_html.='</table>';
$resume_devis_html.='<br><br><hr>';


$resume_devis_html.='<p>Toutes les évaluation ont été faites aux taux de '.$tva.'% sur le prix hors taxes</p>';
$resume_devis_html.='<p>Toute modification de taxe sera à la charge ou à l\'avantage du maître d\'ouvrage</p>';

$resume_devis_html.='<p>"Les travaux non compris dans le prix convenu qui restent à la charge s\'élèvent à la somme de <br> '.number_format($cout_charge_client, 2, ',', ' ').' Euros,toutes taxes comprises".</p>';
$resume_devis_html.='Fait à: 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      
le :';

$resume_devis_html.='<p> Signature du constructeur	
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   	
Signature du maître d\'ouvrage</p>';

$pdf->writeHTML($resume_devis_html, true, false, true, false, '');

//Close and output PDF document

$filename="C:/xampp/htdocs/B/pdf/pdfcreation/part1.pdf";

$pdf->Output($filename,'F');
header("Location:../views/test.php");
//============================================================+
// END OF FILE