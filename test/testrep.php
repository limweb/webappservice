<?php
require __DIR__.'/../app/config/database.php';
// include("../MPDF60/mpdf.php"); // include ให้ถูก
$html= '
<table width=100%><tr heigh=600><td style="border:.1mm solid #000000"; height=600>
<div height=600 style="border:.1mm solid #220044; padding:1em 2em; background-color:#ffffcc;height=600; ">
<h4>Page background</h4>
<div class="gradient">
The background colour can be set by CSS styles on the &lt;body&gt; tag. This will set the background
for the whole page. In this document, the background has been set as a gradient (see below).
</div>
</td></tr></table>
';

$mpdf=new mPDF('c','A4','','',15,15,25,25,10,10);

$mpdf->mirrorMargins = 1;	// Use different Odd/Even headers and footers and mirror margins

$mpdf->WriteHTML($html);
$name = 'test.pdf';
$mpdf->Output();
// $mpdf->Output($name,'I');
// $mpdf->Output($name,'D');
exit;
