<?php
/**
* Handles moviments
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
$counts = Count::find_all();
$result_array = array();
$date = date('Y-m-d');
foreach ($counts as $count) {
  $count_id = $count->count_id;
  // Query
  $sql = "SELECT * FROM (SELECT ifnull(sum(debit_amount), 0.00) as debit_amount, count_description as debit_description FROM ".Debit::$table_name;
  $sql .= " JOIN ".Count::$table_name." WHERE count_id = debit_count_id AND count_id = {$count_id} AND MONTH(debit_date) = MONTH('{$date}') ) as debit_table JOIN ";
  $sql .= " (SELECT ifnull(sum(credit_amount), 0.00) as credit_amount, count_description as credit_description FROM ".Credit::$table_name." JOIN ".Count::$table_name;
  $sql .= " WHERE count_id = credit_count_id AND count_id = {$count_id} AND MONTH(credit_date) = MONTH('{$date}') ) as credit_table JOIN ";
  $sql .= " (SELECT ifnull(sum(debit_amount), 0.00) as debit_sum FROM ".Debit::$table_name." JOIN ".Count::$table_name." WHERE count_id = debit_count_id AND ";
  $sql .= " count_id = {$count_id} AND YEAR(debit_date) = YEAR('{$date}')) debit_sum JOIN (SELECT ifnull(sum(credit_amount), 0.00) as credit_sum ";
  $sql .= " FROM ".Credit::$table_name." JOIN ".Count::$table_name." WHERE count_id = credit_count_id AND count_id = {$count_id} AND YEAR(credit_date) = YEAR('{$date}')) as credit_sum ";
  $result_set = $database->query($sql);
  $results = array();

  while ($row = $database->fetch_array($result_set)) {
    $results [] = $row;
  }
  array_push($result_array, $results);
}

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
  <script src="js/script.js"></script>
  <link rel="stylesheet" href="static/stylesheets/statement.css">
  <link rel="stylesheet" href="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.css">
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/ajax/jquery.js"></script>
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.js" type="text/javascript"></script>
  <script src="<?php echo SITE_PUBLIC ?>static/javascripts/jquery ui/jquery-ui.min.js" type="text/javascript"></script>
  <title>Balancetes</title>
</head>
<div class="moviments-statement-container">
  <div class="moviments-statement-table">
    <div class="page-head"><h1>Balancetes</h1></div>
    <div class="select-input float-right">
      <form action="cashFlow.php" method="POST" class="statements">
        <label>Ano</label><br>
        <select name="year" class="statement-period">
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
    <table class="movements-statement-table">
      <thead>
        <tr class="first-row">
          <td class="counts">Conta</td>
          <td class="attributes" colspan="2">Movimento do Mês</td>
          <td class="attributes" colspan="2">Acumulado</td>
          <td class="attributes" colspan="2">Saldo</td>
        </tr>
        <tr class="second-row">
          <td></td>
          <td class="referenes-terms" >Débito</td>
          <td class="referenes-terms" >Crédito</td>
          <td class="referenes-terms" >Débito</td>
          <td class="referenes-terms" >Crédito</td>
          <td class="referenes-terms" >Devedor</td>
          <td class="referenes-terms" >Credor</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td class="values">-</td>
          <td class="values">-</td>
          <td class="values">-</td>
          <td class="values">-</td>
          <td class="values">-</td>
          <td class="values">-</td>
        </tr>
        <?php if(isset($result_array) & !empty($result_array)): ?>
          <?php
          //initialize varioables
          $total_debit = NULL; $total_credit = NULL; $total_debit_sum = NULL; $total_credit_sum = NULL;
          $total_balance = NULL;  $count = NULL;?>
          <?php foreach ($result_array as $result_set): ?>
            <?php foreach ($result_set as $result): ?>
              <tr class="<?php if($count%2){echo "even"; } else { echo "odd"; }?>">
                <td ><?php echo $result['debit_description']; ?></td>
                <td class="values"><?php echo $result['debit_amount']; ?></td>
                <?php $total_debit += $result['debit_amount']; ?>
                <td class="values"><?php echo $result['credit_amount']; ?></td>
                <?php $total_credit += $result['credit_amount']; ?>
                <td class="values"><?php echo $debit_sum = $result['debit_sum']; ?></td>
                <?php $total_debit_sum += $result['debit_sum']; ?>
                <td class="values"><?php echo $credit_sum = $result['credit_sum']; ?></td>
                <?php $total_credit_sum += $result['credit_sum']; ?>
                <?php $balance = $credit_sum - $debit_sum; ?>
                <?php $total_balance += $balance; ?>
                <td class="values"><?php if($balance < 0 ) { print(number_format($balance, 2)); } elseif($balance == 0){ print(number_format(0, 2)); } else { print('-'); } ?></td>
                <td class="values"><?php if($balance >= 0 ) { print(number_format($balance, 2)); } else { print('-') ;} ?></td>
              </tr>
              <?php $count++; ?>
            <?php endforeach; ?>
              <?php endforeach; ?>
              <!--Totals-->
              <tr class="totals">
                <td class="total">Total</td>
                <td class="values"><?php echo number_format($total_debit, 2); ?></td>
                <td class="values"><?php echo number_format($total_credit, 2); ?></td>
                <td class="values"><?php echo number_format($total_debit_sum, 2); ?></td>
                <td class="values"><?php echo number_format($total_credit_sum, 2); ?></td>
                <td class="values"><?php if($total_balance < 0 ) { print(number_format($total_balance, 2)); } elseif($total_balance == 0){ print(number_format(0, 2)); } else { print('-'); } ?></td>
                <td class="values"><?php if($total_balance >= 0 ) { print(number_format($total_balance, 2)); } else { print('-') ;} ?></td>
              </tr>
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
$("select.statement-period").change(function(){
  $("form.statements").submit();
});
</script>
</body>
</html>
