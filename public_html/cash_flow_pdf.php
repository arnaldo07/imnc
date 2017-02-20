<?php
/**
* Handles cashflows
* @package IMNC Manager
* @link https:// github.com/      The github project
* @author Arnaldo Govene <arnaldo.govene@outlook.com>
* @copyright 2016 Xindiri
* @license
**/

//Initialize
require_once("../includes/initialize.php");
//check login
if (!$session->is_logged_in()){redirect_to('index.php');}

/**
* Cash flows by year
**/
//Credits
$counts = Count::find_by_type("Crédito");
$credits = array();
foreach ($counts as $count) {
  array_push($credits, Credit::monthly_flow($count->count_id, $cur_year = isset($_POST['year'])? $_POST['year'] : date('Y')));
}
$monthly_credits = Credit::monthly_total($cur_year = isset($_POST['year'])? $_POST['year'] : date('Y'));

// Debits
$counts = Count::find_by_type("Débito");
$debits  = array();
foreach ($counts as $count) {
  array_push($debits, Debit::monthly_flow($count->count_id, $cur_year = isset($_POST['year'])? $_POST['year'] : date('Y')));
}
$monthly_debits = Debit::monthly_total($cur_year = isset($_POST['year'])? $_POST['year'] : date('Y'));

//Get post year
isset($_POST['year'])? $post_year = $_POST['year'] : $post_year = NULL;

/**********/
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
// Colored table

// for($i=1;$i<=1000;$i++)
// $pdf->Cell(0,10,'Printing line number '.$i,0,1);
$pdf->Output();

?>
</script>
