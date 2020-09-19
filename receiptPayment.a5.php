<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once realpath(__DIR__ . '/class/databaseClass.php');
require_once realpath(__DIR__ . '/money2word.php');

class Receipt extends database {
  function getPaymentData($paymentID) {
    $sql = "SELECT acc.*, account.name as account, user.name as user FROM acc 
      INNER JOIN account on account.id = acc.id_account
      LEFT JOIN user on user.id = acc.id_user
      WHERE acc.id = {$paymentID}";
    $stmt = $this->pdo->query($sql);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    return $payment;


  }

}


$receipt = new Receipt();

$data = $receipt->getPaymentData($_GET['id']);
$date =  date("H:i:s Y/m/d", strtotime($data['date']));  
$amountStr = money2word(abs(intval($data['dollar'])));
$amount = abs(intval($data['dollar']));



?>


<!doctype html>
<html>
    <head>
        <title>Payment Receipt </title>
        <style>
            @font-face {
                font-family: 'Merriweather Sans';
                src: url('Merriweather_Sans/MerriweatherSans-Regular.ttf');
                font-weight: 400;
                font-style: normal;
            }

            @font-face {
                font-family: 'speda';
                src: url('Speda-Bold.ttf');
            }

            @font-face {
                font-family: 'droid-regular';
                src: url('DroidNaskh-Regular.ttf');
            }

            @font-face {
                font-family: 'droid-bold';
                src: url('DroidNaskh-Bold.ttf');
            }

            * {
                padding: 0;
                margin: 0;
                /* font-family: 'speda', tahoma; */
            }

            section {
                /*                 border: 1px solid red; */
                width: 148.5mm;
                height: 100mm;
                direction: rtl;
            }

            nav {
                vertical-align: top;
                border-bottom: 1px dotted gray;
                padding-bottom: 1mm;
                margin-bottom: 5mm;
                color: #303030;
            }

            nav h2 {
                font-family: 'speda';
            }

            nav h4 {
                font-family: 'speda';
                font-size: 0.8em;
            }

            nav h3 {
                text-align: right;
                font-family: 'speda';
                font-size: 1em;
                padding-top: 2mm;
            }

            .money {
                font-family: 'Merriweather Sans', sans-serif;
            }

            .money-thin {
                font-family: 'calibri', sans-serif;
                font-weight: 300;
            }

            .field {
                font-family: 'droid-bold';
            }

            .box-logo {
                float: left;
                width: 20mm;
                /*                 border: 1px solid brown; */
            }

            .box-logo > img {
                width: 100%;
            }

            .clear {
                clear: both;
            }

            .space {
                height: 4mm;
            }

            .seperator {
                border-bottom: 1px dashed gray;
            }

            .third {
                display: inline-block;
                width: 47.5mm;
                text-align: center;
                vertical-align: top;
            }

            .sixty {
                display: inline-block;
                width: 97mm;
                vertical-align: top;
            }

            .date-number {
                display: inline-block;
                /* width: 97mm; */
                text-align: right;
                font-size: 0.8em;
                vertical-align: bottom;
                /*                 margin-top: 0.3mm; */
                width: 100%;
            }

            .date-number > .date-box {
                display: inline-block;
                text-align: right;
                width: 73mm;
            }

            .date-number > .num-box {
                display: inline-block;
                text-align: left;
                width: 72mm;
                
            }

            .amount-box {
                border: 0.5mm solid black;
                padding: 1mm 1mm;
                width: 30mm;
                display: inline-block;
            }

            .third-amount {
                display: inline-block;
                width: 47.5mm;
                text-align: left;
                vertical-align: top;
            }

            .row {
                margin: 4mm 0;
            }

            .under-dot {
                border-bottom: 1px dotted gray;
                /*                 padding-bottom: 1mm; */
                font-family: 'droid-regular';
            }

            .row-shaddow {
                margin-top: -5mm;
                color: #404040;
                /*                font-family: courier; */
                font-size: 0.8em;
            }

            .small-tbl {
                font-size: 3mm;
            }

            .balance-tbl {
                border-bottom: 1px solid black;
                width: 30mm;
                display: inline-block;
            }

            .footer {
                text-align: center;
                font-family: courier new;
                font-size: 8px;
                bottom: 3px;
            }

            .payer-sign, .receiver-sign {
                display: inline-block;
                width: 70mm;
                text-align: center;
                font-size: 0.8em;
                margin-top: 3mm;
            }
        </style>
    </head>
    <body>
        <section>
            <nav>
                <div class="sixty">
                    <div class="third">
                        <h3>پسولەی پاره وەرگرتن </h3>
                    </div>
                    <div class="third">
                        <h2>ئای ستۆک</h2>
                        <h4>بۆ بازرگانی مۆبایل و ئەلیکترۆنیات</h4>
                    </div>
                </div>
                <div class="third">
                    <div class="box-logo">
                        <img src="dist/img/logo.png" alt="logo">
                    </div>
                </div>
                <!--                 <div class="row"> -->
                <div class="date-number">
                    <div class="date-box" style="direction:ltr;">
                    بەروار : <time class="money-thin" style="direction:ltr;"><?php echo $date; ?></time>
                    </div>
                    <div class="num-box">
                        ژمارە : 
                        <span class="money-thin">
                          <?php echo $data['id']; ?># 
                        </span>
                    </div>
                </div>
                <!--                 </div> -->
            </nav>
            <main>
                <div class="row">
                    <div class="sixty">
                        <span class="field">وەرگیراوە لە 
                        </span>
                        <span class="account-name under-dot">
                          <?php echo $data['account']; ?>
                        </span>
                    </div>
                    <div class="third-amount">
                        <span class="field">
                            بڕی

                            <span>
                                <div class="amount-box money">
                                  <span><?php echo dsh_money($amount); ?></span>
                                  <span style="float:right;">$</span>
                                </div>
                    </div>
                </div>
                <div class="row-shaddow">
                    <p>
                        <span class="field">بڕی پارە
                        </span>
                        <span class="under-dot">
                          <?php echo $amountStr; ?> دۆڵار
                        </span>
                    </p>
                </div>
                <div class="row">
                    <p>
                        <span class="field">ژمێریار
                         </span>
                        <span class="under-dot">
                          <?php echo $data['user']; ?>
                        </span>
                    </p>
                </div>
                <div class="row">
                    <div class="sixty">
                        <span class="field">تێبینی:
                        </span>
                        <span class="account-name under-dot">
                          <?php echo $data['detail']; ?>
                        </span>
                    </div>
                    <div class="third-amount small-tbl">
                        <div>
                            <span class="field">
                                باڵانس

                                <span>
                                    <div class="balance-tbl">
                                        <span class="money">
                                          <?php echo dsh_money(abs(floatval($data['balance'])) + abs($amount)); ?>
                                        </span>
                                        <span style="float:right;">$</span>
                                    </div>
                        </div>
                        <div>
                            <span class="field">
                                دراوه

                                
                                <span>
                                    <div class="balance-tbl">
                                        <span class="money">
                                          <?php echo dsh_money(abs($amount)); ?>
                                        </span>
                                        <span style="float:right;">$</span>
                                    </div>
                        </div>
                        <div>
                            <span class="field">
                                باقی

                                <span>
                                    <div class="balance-tbl">
                                        <span class="money">
                                          <?php echo dsh_money(abs(floatval($data['balance']))); ?>
                                        </span>
                                        <span style="float:right;">$</span>
                                    </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <p class="payer-sign">وەرگر
                    </p>
                    <p class="receiver-sign">پێدەر
                    </p>
                </div>
                <div class="footer">Developed By ERP14.com - 07505149171</div>
            </main>
        </section>
        <div class="space"></div>
        <div class="seperator"></div>
        <div class="space"></div>
        <section>
            <nav>
                <div class="sixty">
                    <div class="third">
                        <h3>پسولەی پاره وەرگرتن </h3>
                    </div>
                    <div class="third">
                        <h2>ئای ستۆک</h2>
                        <h4>بۆ بازرگانی مۆبایل و ئەلیکترۆنیات</h4>
                    </div>
                </div>
                <div class="third">
                    <div class="box-logo">
                        <img src="dist/img/logo.png" alt="logo">
                    </div>
                </div>
                <!--                 <div class="row"> -->
                <div class="date-number">
                    <div class="date-box" style="direction:ltr;">
                    بەروار : <time class="money-thin" style="direction:ltr;"><?php echo $date; ?></time>
                    </div>
                    <div class="num-box">
                        ژمارە : 
                        <span class="money-thin">
                          <?php echo $data['id']; ?># 
                        </span>
                    </div>
                </div>
                <!--                 </div> -->
            </nav>
            <main>
                <div class="row">
                    <div class="sixty">
                        <span class="field">وەرگیراوە لە 
                        </span>
                        <span class="account-name under-dot">
                          <?php echo $data['account']; ?>
                        </span>
                    </div>
                    <div class="third-amount">
                        <span class="field">
                            بڕی

                            <span>
                                <div class="amount-box money">
                                  <span><?php echo dsh_money($amount); ?></span>
                                  <span style="float:right;">$</span>
                                </div>
                    </div>
                </div>
                <div class="row-shaddow">
                    <p>
                        <span class="field">بڕی پارە
                        </span>
                        <span class="under-dot">
                          <?php echo $amountStr; ?> دۆڵار
                        </span>
                    </p>
                </div>
                <div class="row">
                    <p>
                        <span class="field">ژمێریار
                         </span>
                        <span class="under-dot">
                          <?php echo $data['user']; ?>
                        </span>
                    </p>
                </div>
                <div class="row">
                    <div class="sixty">
                        <span class="field">تێبینی:
                        </span>
                        <span class="account-name under-dot">
                          <?php echo $data['detail']; ?>
                        </span>
                    </div>
                    <div class="third-amount small-tbl">
                        <div>
                            <span class="field">
                                باڵانس

                                <span>
                                    <div class="balance-tbl">
                                        <span class="money">
                                          <?php echo dsh_money(abs(floatval($data['balance'])) + abs($amount)); ?>
                                        </span>
                                        <span style="float:right;">$</span>
                                    </div>
                        </div>
                        <div>
                            <span class="field">
                                دراوه

                                
                                <span>
                                    <div class="balance-tbl">
                                        <span class="money">
                                          <?php echo dsh_money(abs($amount)); ?>
                                        </span>
                                        <span style="float:right;">$</span>
                                    </div>
                        </div>
                        <div>
                            <span class="field">
                                باقی

                                <span>
                                    <div class="balance-tbl">
                                        <span class="money">
                                          <?php echo dsh_money(abs(floatval($data['balance']))); ?>
                                        </span>
                                        <span style="float:right;">$</span>
                                    </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <p class="payer-sign">وەرگر
                    </p>
                    <p class="receiver-sign">پێدەر
                    </p>
                </div>
                <div class="footer">Developed By ERP14.com - 07505149171</div>
            </main>
        </section>
    </body>
</html>
 
