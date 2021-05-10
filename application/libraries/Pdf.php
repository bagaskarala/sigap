<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf extends Dompdf
{
    public function __construct()
    {
        parent::__construct();
    }

    public function generate_pdf_a4_portrait($html, $file_name) {
        $dompdf = new Dompdf();

        $options = new Options();
        $options->setIsRemoteEnabled(true);

        $dompdf->setOptions($options);

        $dompdf->setPaper('A4', 'potrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream($file_name.'.pdf',array("Attachment"=>0));
    }
    
    public function generate_pdf_a4_landscape($html, $file_name) {
        $dompdf = new Dompdf();

        $options = new Options();
        $options->setIsRemoteEnabled(true);

        $dompdf->setOptions($options);

        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream($file_name.'.pdf',array("Attachment"=>0));
    }

}
