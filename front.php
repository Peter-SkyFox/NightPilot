<div class="row" style="text-align:center; margin-bottom:50px">
 <p> <span id="connectButton" class="btn2">Install MetaMask</span></p>
  <label for="cars">Choose a currency:</label>
  <select id="currency">
    <option value="eth">Ethereum</option>
    <option value="erc20">ERC 20</option>
  </select>
  <?php if (is_user_logged_in()) {
    global $current_user;
    wp_get_current_user();
    $username = $current_user->user_firstname . " " . $current_user->user_lastname;
    echo '<input type = "hidden" value = "' . esc_attr($username) . '" id = "username" >';
  }
  ?>
  <?php
  foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
    $product_description = $cart_item['data']->get_name(); // Product description
    $product_id = $cart_item['data']->get_id();
  }
  echo '<input type = "hidden" value = "' . esc_attr($product_description) . '" id = "description" >';
  ?>
</div>
<div class="frontform">
  <div class="row">
    <div class="col-md-5 ethvalue">
      <label for="fname"><span class="curren">ETH</span> Value</label>
    </div>
    <div class="col-md-1 colon">
      <label for="fname">:</label>
    </div>
    <div class="col-md-6 labeldec" style="float:right; width:50%; margin-right:-65px !important;">
      <?php
      global $woocommerce;
      echo '<input type = "hidden" value = "' . esc_attr($woocommerce->cart->total) . '" id = "dollval">';
      ?>
      <label for="lname"><span id="dolleramount"></span> <span class="curren">ETH</span>&nbsp;(<span id="countdown">60</span>s)</label>
    </div>
  </div><br><br>
  <div class="row" style="display:none;">
    <div class="col-md-5 ethvalue">
      <label for="fname">ETH Transaction Fees</label>
    </div>
    <div class="col-md-1 colon">
      <label for="fname">:</label>
    </div>
    <div class="col-md-6 ethvalue label" style="float:right; width:50%; margin-right:-65px !important;">
      <label for="lname" class="labelfine"><span id="transactionfee"></span> <span id="curren">ETH</span></label>
    </div>
  </div>
  <input type="hidden" id="gasprice" name="" value="" placeholder="fsdf">
  <div class="row">
    <div class="col-md-5 ethvalue">
      <label for="fname"><span class="curren">ETH</span> Transaction Fees</label>
    </div>
    <div class="col-md-1 colon">
      <label for="fname">:</label>
    </div>
    <div class="col-md-6 labeldec" style="float:right; width:50%; margin-right:-65px !important;">
      <label><span id="gfrontfee">0</span> <span class="curren">ETH</span></label>
    </div>
  </div><br>
  <hr class="seprateline">
  <div class="row">
    <div class="col-md-5 ethvalue">
      <label for="fname" style="font-weight:bold;">Total <span class="curren">ETH</span></label>
    </div>
    <div class="col-md-1 colon">
      <label for="fname">:</label>
    </div>
    <div class="col-md-6" style="float:right; width:50%; margin-right: -69px !important;">
      <label for="lname" class="labelfine" style="font-weight:bold;"><span id="finaleth">0</span> <span class="curren">ETH</span></label>
    </div>
  </div>
  <br>
  <?php
  $dateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
  $datime = $dateTime->format("m-j-Y  H:i A");
  $curr_date = $dateTime->format("m-j-Y");
  echo '<input type="hidden" id="currentdate" name="" value="' . esc_attr($datime) . '" >';
  echo '<input type="hidden" id="currdate" name="" value="' . esc_attr($curr_date) . '" >';
  ?>
  <hr class="seprateline">
  <input id="valuetobetransfer" name="finalvalue" value="" type="hidden"></input>
  <input type="hidden" name="finalfee" id="finalfee" value="" name="">
  <input type="hidden" name="" id="dolloramt" value="" name="">
  <input type="hidden" name="" id="transfee" value="" name="">
  <div class="row">
    <a class="btn btn-primary btn-lg btn-block mb-3" id="sendButton">
      Place order
    </a>
  </div>
</div>
<?php
global $wpdb, $table_prefix;
$tblnme = $table_prefix . 'cryptochain';
$found = $wpdb->get_results("SELECT wallet_address,mode from $tblnme");
$k =   $found[0]->wallet_address;
$m =   $found[0]->mode;
?>
<span id="adminaccount" style="display:none;"><?php echo esc_html($k);   ?></span>
<span id="paymode" style="display:none;"><?php echo esc_html($m);   ?></span>
<span id="accounts" style="display:none;"></span>
<p class="info-text alert alert-primary" style="display:none;">
  Network: <span id="network"></span>
</p>
<p class="info-text alert alert-secondary" style="display:none;">
  ChainId: <span id="chainId"></span>
</p>
<p class="info-text alert alert-secondary" style="display:none;">
  Gas Limit: <input id="gas" value="75000"></input>
</p>
<script type="text/javascript">
  jQuery(document).ready(function() {
    var seconds = document.getElementById("countdown").textContent;
    var countdown = setInterval(function() {
      seconds--;
      document.getElementById("countdown").textContent = seconds;
      if (seconds <= 0) {
        seconds = 60;
         kk = jQuery("#currency option:selected").val();
         if (kk == 'erc20') {
          ercrates();
         }else {
          exchange();
        gasexchangerate();
         }
      }
    }, 1000);
    exchange();
    function exchange() {
      jQuery.ajax({
        type: 'GET',
        url: 'https://api.coingecko.com/api/v3/exchange_rates',
        success: function(data) {
          let order_total = "<?php echo $woocommerce->cart->total ?>";
          eth = data.rates.eth.value;
          doller = data.rates.usd.value;
          usd_to_eth = eth / doller;
          finaleth = usd_to_eth * order_total;
          finaleth1 = finaleth.toFixed(6);
          jQuery("#dolleramount").html(finaleth1);
          jQuery("#dolloramt").val(finaleth1);
          var order_amt = Number(finaleth1);
          transfee = order_amt * 0.02;
          transfee = transfee.toFixed(6);
          senteth = Number(transfee) + order_amt;
          senteth1 = senteth.toFixed(6);
          senteth1 = Number(senteth1);
          jQuery("#valuetobetransfer").val(senteth1);
          jQuery("#transactionfee").html(transfee);
          jQuery("#transfee").val(transfee);
          setValuesInLs(order_amt, transfee)
        },
        error: function(error) {
          console.log('not implemented');
        }
      });
    }
    gasexchangerate();
    function gasexchangerate() {
      jQuery.ajax({
        type: 'GET',
        url: 'https://ethgasstation.info/api/ethgasAPI.json?api-key=a0cf958dc3c4f1a73f2b45d31247650b1971c5fb57782628c45e38f7462d',
        success: function(data) {
          Average = data.average / 10;
          //alert(Average);
          jQuery("#gasprice").val(Average);
          jQuery("#gspfront").html(Average);
          fee = jQuery("#transactionfee").html();
          gasfee = Average * 0.000000001 * 75000;
          gasfee1 = gasfee.toFixed(6);
          gasfee2 = Number(gasfee1) + Number(fee);
          jQuery("#gfrontfee").html(gasfee2.toFixed(6));
          amount = jQuery("#dolleramount").html();
          total = Number(fee) + Number(amount) + Number(gasfee1);
          jQuery("#finaleth").text(total.toFixed(6));
        },
        error: function(error) {
          console.log('not implemented');
        }
      });
    }
    function ercrates() {
      jQuery.ajax({
        type: 'GET',
        url: 'https://api.coingecko.com/api/v3/simple/price?ids=erc20&vs_currencies=usd',
        success: function(data) {
          let order_total = "<?php echo $woocommerce->cart->total ?>";
          doller =  data.erc20.usd;
          erc = order_total / doller;
          ercfee = erc * 0.2;
          erctotal = erc + ercfee;
          erctotal = erctotal.toFixed(3);
          erc = erc.toFixed(3);
          ercfee = ercfee.toFixed(3);
           jQuery("#valuetobetransfer").val(erc);
          jQuery("#dolleramount").html(erc);
          jQuery("#gfrontfee").html(ercfee);
          jQuery("#finaleth").html(erctotal);
        },
        error: function(error) {
          console.log('not implemented');
        }
      });
    }
    var pop = jQuery("input[name='payment_method']:checked").val();
    //alert(pop);
    if (pop == 'crypto') {
      jQuery("#place_order").css("display", "none");
    }
    jQuery("input[name='payment_method']").change(function() {
      var radioValuep = jQuery("input[name='payment_method']:checked").val();
      //alert(radioValuep);
      if (radioValuep == 'crypto') {
        jQuery("#place_order").css("display", "none");
      } else {
        jQuery("#place_order").css("display", "inline-block");
      }
    });
   jQuery('#currency').change(function(){
     var curr = jQuery(this).val();
     if(curr == 'erc20'){
         jQuery(".curren").html('ERC');
        ercrates();
     }else{
       jQuery(".curren").html('ETH');
       exchange();
       gasexchangerate();
     }
});
  });
  function setValuesInLs(ordertotal, orderfee) {
    localStorage.setItem("order_total", ordertotal);
    localStorage.setItem("order_fee", orderfee);
  }
</script>