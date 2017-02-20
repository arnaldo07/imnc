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
* Create credits
**/
if(isset($_POST['credit']) & isset($_POST['description_id']) & isset($_POST['date'])){
  $credit_description_id = $_POST['description_id'];
  $credit_amount      = $_POST['credit'];
  $credit_date        = $_POST['date'];
  if(Credit::create($credit_description_id, $credit_amount, $credit_date, $credit_session = $_SESSION['session_id'], $credit_account_id = $_SESSION['user_id'])){
    //Success
  }
} elseif(isset($_POST['debit']) & isset($_POST['description_id']) & isset($_POST['date'])){
  $debit_description_id = $_POST['description_id'];
  $debit_amount      = $_POST['debit'];
  $debit_date        = $_POST['date'];
  if(Debit::create($debit_description_id, $debit_amount, $debit_date, $debit_session = $_SESSION['session_id'], $debit_account_id = $_SESSION['user_id'])){
    //Success
  }
}

/**
* Find inserted credits and debits
**/
global $database;
$sql = "SELECT count_description as credit_description, null as debit_description, credit_amount, null as debit_amount, insertion_datetime FROM ".Credit::$table_name." ";
$sql.= " JOIN ".Count::$table_name." WHERE count_id = credit_count_id AND credit_session = '{$_SESSION['session_id']}' AND credit_account_id = '{$_SESSION['user_id']}' UNION ";
$sql.= " SELECT null, count_description as debit_description, null, debit_amount, insertion_datetime FROM ".Debit::$table_name." ";
$sql.= " JOIN ".Count::$table_name." WHERE count_id = debit_count_id AND debit_session = '{$_SESSION['session_id']}' AND debit_account_id = '{$_SESSION['user_id']}' ORDER BY insertion_datetime";

$result_set = $database->query($sql);
$result_array = array();

while ($row = $database->fetch_array($result_set)) {
  $result_array [] = $row;
}

// Credits sum
$credits = Credit::sum($user_id = $_SESSION['user_id'], $session_id = $_SESSION['session_id']);
// Debit sum
$debits = Debit::sum($user_id = $_SESSION['user_id'], $session_id = $_SESSION['session_id']);

//counts
$counts = Count::find_all();

?>


<?php  include_once("templates/partials/header.php") //include header ?>
<link rel="stylesheet" href="<?php echo SITE_PUBLIC ?>static/stylesheets/moviments.css">

<div class="moviments-form-container">
  <div class="moviments-form-subcontainer">
    <div class="moviments-head">
      <h1 class="main-hero">Inserção de Movimentos Díários</h1>
      <p class="main-paragraph"></p>
    </div>
    <div class="moviments-body">
      <form method="POST" action="" class="moviments-insertion-form">
        <div class="form-row row-1">
          <div class="float-left">
            <label class="input-label"> Moeda</label><br>
            <input type="text" name="currency" class="currency disabled" value="MZN" disabled/>
          </div>
          <div class="float-left exchange">
            <label class="input-label"> Câmbio</label><br>
            <input type="text" name="exchange" class="exchange disabled" value="0,00" disabled/>
          </div>
          <div class="float-right">
            <label class="input-label datepicker-label"> Data de Movimento</label><br>
            <input type="text" name="operation-date" value="<?php print(strftime("%Y-%m-%d", time())); //Current date ?>" class="datepicker"/>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="empty-row"></div>
        <div class="the-table">
          <table class="moviments-table">
            <thead class="input-table-head">
              <tr class="head">
                <td class="code"> Código</td>
                <td class="description"> Designação</td>
                <td class="debit values"> Débito</td>
                <td class="credit values"> Crédito</td>
              </tr>
            </thead>
            <tbody class="input-table-body">
              <?php foreach ($result_array as $moviment): ?>
                <tr class="table-body">
                  <td class="code">
                    <input type="text"  value=""class="code disabled" maxlength="5" disabled/>
                  </td>
                  <td class="description">
                    <select name="description"  class="disabled" disabled>
                      <option value="" selected="selected">
                        <?php
                        // description
                        if(isset($moviment['credit_description'])){
                          echo $moviment['credit_description'];
                        } elseif(isset($moviment['debit_description'])){
                          echo $moviment['debit_description'];
                        }
                        ?>
                      </option>
                    </select>
                  </td>
                  <td class="values">
                    <input type="text" name="debit" class="disabled" value="  <?php
                    // amount
                    if(isset($moviment['debit_amount'])){
                      echo number_format($moviment['debit_amount'], 2);
                    } else{
                      echo '-';
                    }
                    ?>" maxlength="12" disabled/>
                  </td>
                  <td class="values">
                    <input type="text" name="credit" class="disabled" value=" <?php
                    // amount
                    if(isset($moviment['credit_amount'])){
                      echo number_format($moviment['credit_amount'], 2);
                    } else{
                      echo '-';
                    }
                    ?>"  maxlength="12" disabled/>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr class="table-body">
                <td class="code">
                  <input type="text" name="code" value=""class="code disabled" maxlength="5" disabled/>
                </td>
                <td class="description">
                  <select name="description" class="description" >
                    <option value="" selected="selected">Seleccione uma designação</option>
                    <?php if(isset($counts) & !empty($counts)): ?>
                      <?php foreach ($counts as $count): ?>
                        <option value="<?php echo $count->count_id; ?>"><?php echo $count->count_description; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </td>
                <td class="values">
                  <input type="text" name="debit" value="" class="debit" maxlength="12" />
                </td>
                <td class="values">
                  <input type="text" name="credit" value=""class="credit"  maxlength="12" />
                </td>
              </tr>
              <tr class="bottom"></tr>
            </tbody>
          </table>
          <div class="error-reports">
            <span class="error-report"><!--Handlles errors--><span>
            </div>
            <div class="table-footer">
              <div class="table-footer-content float-left">
                <div class="total-debit totals"><span class="debit-total-head">Total Débito - </span><span class="debit-total-body"><?php  !empty($credits)? print(number_format($credits, 2)) : print('0.00'); // print credits ?></span></div>
                <div class="total-credit totals"><span class="credit-total-head">Total Crédito - </span><span class="credit-total-body"><?php  !empty($debits)? print(number_format($debits, 2)) : print('0.00'); // print debits ?></span></div>
              </div>
              <div class="table-footer-content right float-right"><a href="cashFlow.php">Fluxo de Caixa</a><br><a class="print-page" href="#">Imprimir</a></div>
              <div class="clearfix"></div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
  <script type="text/javascript">
  //print page
  $('.print-page').click(function(){
    window.print();
  });
  //set data picker ui
  $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
  //on blur add new inputs
  $("input.datepicker, .table-body input, .table-body select").blur(function(){
    //check if inputs are empty
    var code  = $('input.code').val() //description
    var desc  = $('select.description').val() //description
    var debit = $('input.debit').val() //debit
    var credit = $('input.credit').val() //credit
    var date   = $('input.datepicker').val(); // operation date
    var error = "";

    if((date != null && date != "") && (desc !=null && desc != "") && ((debit != null && debit != "") || (credit != null && credit != ""))){
      if((credit != null && credit != "") && (debit !== null && debit !== "")){
        //Error
        error = 'Não é possivel definir crédito e débito do mesmo movimento.';
      } else if (credit !== null && credit !== "")  {
        //POST credit data
        if($.isNumeric(credit)){
          $.post('moviments.php', {description_id: desc, credit: credit, date: date}, function(data, status){
            $("body").html(data);
          });
        } else {
          error = "O crédito deve ser númerico.";
        }
      } else if (debit !== null && debit !== ""){
        //POST debit data
        if($.isNumeric(debit)){
          $.post('moviments.php', {description_id: desc, debit: debit, date: date}, function(data, status){
            $("body").html(data);
          });
        } else {
          error = "O crédito deve ser númerico.";
        }
      }
      // set error
      $('span.error-report').html(error);
      $('html, body').animate({scrollTop:$('span.error-report').position().top}, 1000);
    }
  });
  </script>
</body>
</html>
