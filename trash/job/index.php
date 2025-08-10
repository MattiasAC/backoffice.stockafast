<?php
require_once('vendor2/fpdf184/fpdf.php');
require_once('vendor2/fpdf_merge/fpdf_merge.php');
require_once('vendor2/FPDI-2.3.3/src/autoload.php');

class mypdf
{
    public function __construct(){

    }
    public function selecter(){
        $files = scandir("html/job/texts/");
        $return = "<select name='filename' class='form-control' style=''>";
        foreach($files as $file){
            $sel = isset($_POST["filename"]) && $_POST["filename"]       == $file? "selected":"";
            if(!in_array($file,[".",".."]) )  {
                $return.= "<option $sel>{$file}</option>";
            }

        }
        $return.= "</select>";
        return $return;
    }
    public function create($filename)
    {
        $text = file('html/job/texts/'.$filename);
        $pdf = new FPDF('P','mm',array(210,437));
        $pdf->AddPage("P");
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','',12);
        $pdf->SetLeftMargin(25);
        $pdf->SetRightMargin(25);
        $pdf->Ln(20);
        foreach($text as $p){
            if(!empty($p)){
                $p = mb_convert_encoding($p, "ISO-8859-1", mb_detect_encoding($p));
                if(substr($p,0,4) == "BOLD"){
                    $pdf->SetFont('Arial','B',12);
                    $p = substr($p,5);
                }else{
                    $pdf->SetFont('Arial','',12);
                }
                $pdf->write(6,$p);
            }

        }

        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,6,mb_convert_encoding("Kontaktuppgifter:", "ISO-8859-1","UTF-8"),0,1);
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,6,mb_convert_encoding("Mattias Altahr-Cederberg", "ISO-8859-1","UTF-8"),0,1);
        $pdf->Cell(0,6,mb_convert_encoding("Lilla Hammars väg 13A", "ISO-8859-1","UTF-8"),0,1);
        $pdf->Cell(0,6,mb_convert_encoding("236 37 Höllviken", "ISO-8859-1","UTF-8"),0,1);
        $pdf->Cell(0,6,mb_convert_encoding("", "ISO-8859-1","UTF-8"),0,1);
        $pdf->Cell(0,6,mb_convert_encoding("Personnummer: 710726-3993", "ISO-8859-1","UTF-8"),0,1);
        $pdf->Cell(0,6,mb_convert_encoding("Telefon: 0708 59 67 88", "ISO-8859-1","UTF-8"),0,1);
        $pdf->Cell(0,6,mb_convert_encoding("E-post: info@altahr.se", "ISO-8859-1","UTF-8"),0,1);
        $pdf->Cell(0,6,mb_convert_encoding("Hemsida: altahr.se", "ISO-8859-1","UTF-8"),0,1);
        $pdf->setY($pdf->getY()-52);
        $pdf->setX(95);
        $pdf->Image("html/job/gazelle.png",null,null,100);
        $pdf->Output("F","html/job/pdfs/cv.pdf");
      
    
        // Bilagor

        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->AddPage();$pdf->setSourceFile('html/job/pdfs/cv.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        if(isset($_POST["bilagor"])){


       $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/examensbevis.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
       $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/linkedin.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/ilka.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/nomadiz.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/cyberrelax.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/sida1.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/sida2.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/sida3.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/sida4.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        $pdf->AddPage();$pdf->setSourceFile('html/job/bilagor/sida5.pdf');$templateId = $pdf->importPage(1);$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
                    }
        $filename = 'html/job/pdfs/MattiasCV_'.substr($filename,0,strlen($filename)-4).'.pdf';
        $pdf->Output('F', $filename);
        echo "<iframe src=\"https://admin.altahr.se/{$filename}\" width=\"100%\" style=\"height:100%\"></iframe>";

    }
}
$pdf = new mypdf();
echo "<form action='/job/index/' class='form-inline' method='post'>
{$pdf->selecter()}
<input type='checkbox' class='btn btn-primary' name='bilagor'> Bilagor
<input type='submit' class='btn btn-primary' name='create'>
</form>";
if(isset($_POST["create"])){
    $pdf->create($_POST["filename"]);
}

?>