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

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="">
  <link rel="stylesheet" href="static/stylesheets/cashflow.css">
  <link rel="stylesheet" href="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.css">
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/ajax/jquery.js"></script>
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.js" type="text/javascript"></script>
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.min.js" type="text/javascript"></script>
  <title>Fluxo de Caixa</title>
</head>
<div class="cashflow-statement-container">
  <div class="cashflow-statement-table">
    <div class="page-head">
      <h1 class="page-title">Fluxo de Caixa</h1>
    </div>
    <div class="select-input float-right">
      <form action="cashFlow.php" method="POST" class="cashflows">
        <label>Ano</label><br>
        <select name="year" class="cashflow-period">
          <?php
          $start_year = 2017;
          $count = 1;
          $end_year = date('Y');
          while ($start_year <= $end_year): ?> {
            <?php if(isset($post_year) & $post_year != NULL): ?>
              <option selected="selected" value="<?php echo $post_year; ?>"><?php echo $post_year; $post_year = NULL;?></option>
              <option value="<?php echo $start_year; ?>"><?php echo $start_year; ?></option>
            <?php elseif ($count == 1 & !$year == NULL): ?>
              <option selected="selected" value="<?php echo $start_year; ?>"><?php echo $start_year; ?></option>
            <?php else: ?>
              <option value="<?php echo $start_year; ?>"><?php echo $start_year; ?></option>
            <?php endif; ?>
            <?php
            $start_year ++;
            $count ++;
            ?>
          <?php endwhile; ?>
        </option>
      </select>
    </form>
  </div>
  <table class="cashflow-statement">
    <thead>
      <td></td>
      <td> Janeiro</td>
      <td> Fevereiro</td>
      <td> Março</td>
      <td> Abril</td>
      <td> Maio</td>
      <td> Junho</td>
      <td> Julho</td>
      <td> Agosto</td>
      <td> Setembro</td>
      <td> Outubro</td>
      <td> Novembro</td>
      <td> Dezembro</td>
      <td> Total</td>
    </thead>
    <tbody>
      <tr class="distiguish">
        <td class="bold "> Entradas</td>
        <?php $monthly_credit_total = NULL; // Monthly credits?>
        <?php foreach ($monthly_credits as $monthly_credit): ?>
          <td>
            <?php
            if(!empty($monthly_credit->credit_amount)){
              print(number_format($monthly_credit->credit_amount, 2));
              $monthly_credit_total+= $monthly_credit->credit_amount;
            }
            ?>
          </td>
        <?php endforeach; ?>
        <td class="bold total-results">  <?php echo  number_format($monthly_credit_total, 2); ?></td>
        <?php if(isset($credits) & !empty($credits)): ?>
          <?php foreach ($credits as $credit): ?>
          </tr>
          <?php $iteration=1; $total_credit = NULL;?>
          <?php foreach ($credit as $credit_object): ?>
            <?php while(isset($iteration)): ?>
              <td><?php echo $credit_object->credit_description; ?></td>
              <?php unset($iteration); ?>
            <?php endwhile; ?>
            <td><?php echo number_format($credit_object->credit_amount, 2); //credit per month?></td>
            <?php $total_credit += $credit_object->credit_amount; ?>
          <?php endforeach; ?>
          <td class="bold total-results"><?php echo  number_format($total_credit, 2); //total credits?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    <tr class="distiguish">
      <td class="bold"> Saidas</td>
      <?php $monthly_debit_total = NULL; //Monthly debits ?>
      <?php foreach ($monthly_debits as $monthly_debit): ?>
        <td>
          <?php
          if(!empty($monthly_debit->debit_amount)){
            print(number_format($monthly_debit->debit_amount, 2));
            $monthly_debit_total+= $monthly_debit->debit_amount;
          }
          //?>
        </td>
      <?php endforeach; ?>
      <td class="bold total-results">  <?php echo  number_format($monthly_debit_total, 2); ?></td>
    </tr>

      <?php if(isset($debits) & !empty($debits)): ?>
        <?php foreach ($debits as $debit): ?>
        </tr>
        <?php $iteration=1; $total_debit = NULL;?>
        <?php foreach ($debit as $debit_object): ?>
          <?php while(isset($iteration)): ?>
            <td><?php echo $debit_object->debit_description; ?></td>
            <?php unset($iteration); ?>
          <?php endwhile; ?>
          <td><?php echo number_format($debit_object->debit_amount, 2); //credit per month?></td>
          <?php $total_debit += $debit_object->debit_amount; ?>
        <?php endforeach; ?>
        <td class="bold total-results"><?php echo  number_format($total_debit, 2); //total credits?></td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>

  </tbody>
</table>
<div class="table-footer float-right">
  <span>Extracto de <?php $post_year != NULL? print($post_year) : print(date('Y')); ?>, </span>
  <span>Impresso aos <span class="datetime"></span></span>
</div>
</div>
</div>
<script>
//Handles date and time
var date = new Date();
var options = { year: "numeric", month: "long", day: "numeric", hour: "numeric", minute: "numeric", second: "numeric"};
$(".datetime").html(date.toLocaleString('pt-PT', options));

//submit cashflow period
$("select.cashflow-period").change(function(){
  $("form.cashflows").submit();
});
</script>
