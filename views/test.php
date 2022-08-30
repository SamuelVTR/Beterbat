<?php
session_start();
include_once "../models/plan_type.class.php";
include_once "../models/Client.class.php";
use setasign\Fpdi\Fpdi;

require_once('fpdf/fpdf.php');
require_once('fpdi2/src/autoload.php');


class ConcatPdf extends Fpdi
{
    public $nbpage=0;
    public $firstpdf= 0;
    public $files = array();

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function concat()
    {
        foreach($this->files AS $file) 
        {
            $pageCount = $this->setSourceFile($file);
            if( $this->firstpdf<=1){
                $this->nbpage=$pageCount; 
                $this->firstpdf+=1;
            }
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $pageId = $this->ImportPage($pageNo);
                $s = $this->getTemplatesize($pageId);
                $this->AddPage($s['orientation'], $s);
                $this->useImportedPage($pageId);
            }
        }
    }

}
$plan_type = new plan("","","","","","","","","","");
$plan= $plan_type->getplan($_SESSION["devis"][0]["plan"]);

$Clients = new Client("","","","","","","","","");
$client=$Clients->getclient($_SESSION["devis"][0]["client"]);

$pdf = new ConcatPdf();

$pdf->setFiles(array('../uploads/notice.pdf','../pdf/pdfcreation/part1.pdf',$plan[0]['pdf']));
$pdf->concat();

$nbpagefirstpdf=$pdf->nbpage;
$filename="../uploads/test.pdf";
$pdf->Output($filename,'F');


$pdf = new Fpdi();
// add a page

// set the source file

// import page 1
$pageCount = $pdf->setSourceFile('../uploads/test.pdf');

for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    $tplIdx = $pdf->importPage($pageNo);

    // add a page
    $size = $pdf->getTemplateSize($tplIdx);

    // create a page (landscape or portrait depending on the imported page size)
    if ($size['width'] > $size['height']) {
        $pdf->AddPage('L', array($size['width'], $size['height']+5));
    } else {
        $pdf->AddPage('P', array($size['width'], $size['height']+5));
    }
    
    $pdf->useTemplate($tplIdx);

    // font and color selection
    $pdf->SetFont('Helvetica');
    $pdf->SetTextColor(200, 0, 0);

    $nomclient=$client[0]["Nom"];
    $lieu_construction=$client[0]["adresse_terrain"];
    $date=date('d/m/Y');;
    $commercial="com";
    $indice=chr(65+$client[0]["indice"]);
   
    // now write some text above the imported page
    if($pageNo==$nbpagefirstpdf+2)
    {
      //lieu_construction
      $pdf->SetXY(85, 35);
      $pdf->Write(2, $lieu_construction);
    
        //nom client
        $pdf->SetXY(28, 100);
        $pdf->Write(2, $nomclient);

         //date
         $pdf->SetXY(135, 160);
         $pdf->Write(2, $date);
    }
    if($pageNo==$nbpagefirstpdf+3)
    {
        $pdf->SetXY(221, 185);
        $pdf->Write(2, $date );
      
        $pdf->SetXY(232, 190);
        $pdf->Write(2, $commercial);

        $pdf->SetXY(217, 192);
        $pdf->Write(2, $indice);

        //nom client
        $pdf->SetXY(35, 192);
        $pdf->Write(2, $nomclient);

    }
    if($pageNo==$nbpagefirstpdf+4)
    {
        $pdf->SetXY(221, 185);
        $pdf->Write(2, $date );
      
        $pdf->SetXY(232, 190);
        $pdf->Write(2, $commercial);

        $pdf->SetXY(217, 192);
        $pdf->Write(2, $indice);

        //nom client
        $pdf->SetXY(35, 192);
        $pdf->Write(2, $nomclient);
    }
    if($pageNo==$nbpagefirstpdf+5)
    {
        $pdf->SetXY(221, 185);
        $pdf->Write(2, $date );
      
        $pdf->SetXY(232, 190);
        $pdf->Write(2, $commercial);

        $pdf->SetXY(217, 192);
        $pdf->Write(2, $indice);

        //nom client
        $pdf->SetXY(35, 192);
        $pdf->Write(2, $nomclient);
    }
    if($pageNo==$nbpagefirstpdf+6)
    {
        $pdf->SetXY(221, 185);
        $pdf->Write(2, $date );
      
        $pdf->SetXY(232, 190);
        $pdf->Write(2, $commercial);

        $pdf->SetXY(217, 192);
        $pdf->Write(2, $indice);

        //nom client
        $pdf->SetXY(35, 192);
        $pdf->Write(2, $nomclient);
    }



}

$pdf->Output(); 