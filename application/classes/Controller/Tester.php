<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Description of Test
 *
 * @author Alex
 */
class Controller_Tester extends Controller{
//    public function action_currencies()
//    {
//        $xmlString = file_get_contents('http://www.currency-iso.org/dam/downloads/table_a1.xml');
//        $xml= simplexml_load_string($xmlString);
//        foreach ($xml->CcyTbl->CcyNtry as $country)
//        {
//            $currency = new Model_Currency($country->CcyNbr);
//            $currency->id = $country->CcyNbr;
//            $currency->code = $country->Ccy;
//            $currency->save();
//        }
//        
//    }
    
    public function action_pdftest()
    {
        $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('Chronos Depot');
        $pdf->SetAuthor('Alex Jucan');
        $pdf->SetTitle('TCPDF Example');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 14, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        // set text shadow effect
        $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

        // Set some content to print
        $html = "
        <h1>Welcome to <a href=\"http://www.tcpdf.org\" style=\"text-decoration:none;background-color:#CC0000;color:black;\">&nbsp;<span style=\"color:black;\">TC</span><span style=\"color:white;\">PDF</span>&nbsp;</a>!</h1>
        <i>This is the first example of TCPDF library.</i>
        <p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
        <p>Please check the source code documentation and other examples for further information.</p>
        <p style=\"color:#CC0000;\">TO IMPROVE AND EXPAND TCPDF I NEED YOUR SUPPORT, PLEASE <a href=\"http://sourceforge.net/donate/index.php?group_id=128076\">MAKE A DONATION!</a></p>
        ";

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('example_001.pdf', 'D');
    }
}
