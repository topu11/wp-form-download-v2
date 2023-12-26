<?php
 require_once( WP_PLUGIN_DIR . '/SuncodeIT-Custom-Form'.'/assets/css/main.php' );
 global $wpdb;
 $table_name=$wpdb->prefix . 'encoderit_country_with_code';
 $result = $wpdb->get_results("SELECT * FROM " . $table_name . "");
 $html='<option>Please select country</option>';
 foreach ($result as $singledata)
 {
     $html .='<option value='.$singledata->id.'>'.$singledata->country_name.'</option>';
 }
 
?>
<div style="padding: 30px">
  <h1>Add New Case</h1>
  <form
    action=""
    method="POST"
    enctype="multipart/form-data"
    id="fileUploadForm"
  >
    <div class="row_d">
      <div class="titel_col">
        <label for="">Person number:</label>
      </div>
      <div class="right_col person_number_col">
        <input type="number" name="person_number" min="1" id="person_number"  value="" required />
      </div>
    </div>

    <div class="row_d">
      <div class="titel_col">
        <label for="">Description:</label>
      </div>
      <div class="right_col">
        <textarea
          name="description"
          id="description"
          row="10"
          style="width: 100%"
          required
        ></textarea>
      </div>
    </div>

    <div class="row_d">
      <div class="titel_col">
        <label for="">Add File:</label>
      </div>
      <div class="right_col add__file__container">
        <button id="addFile">Add File</button>
        <div id="files"></div>
      </div>
    </div>
    <div class="row_d">
      <div class="titel_col">
        <label for="">Country:</label>
      </div>
      <div class="right_col product__container">
        <select name="select_country" id="select_country"><?=$html?></select>
      </div>
    </div>
    <div class="row_d services_row">
      <div class="titel_col">
        <label for="">Services:</label>
      </div>
      <div class="right_col product__container" id="service_container">
        
      </div>
    </div>

    <div class="row_d">
      <div class="titel_col">
        <label for="">Selected Payment Method:</label>
      </div>
      <div class="right_col right_total_price">
        <div class="payment_method_container">
          <div class="item d-flex-center">
            <input type="radio" name="payment_method" id="encoderit_paypal"  value="Paypal" onclick="check_radio_payment_method(this.id)" />
            <span>Paypal</span>
          </div>
          <div class="item d-flex-center">
            <input type="radio" name="payment_method" id="encoderit_stripe"  value="Credit Card" onclick="check_radio_payment_method(this.id)"/>
            <span>Credit Card</span>
          </div>
          <div class="item d-flex-center" style="display: none;">
            <input type="radio" name="payment_method" id="encoderit_bank_transfer" value="Bank Transfer" onclick="check_radio_payment_method(this.id)"/>
            <span>Bank Transfer</span>
          </div>
        </div>
         <div class="paymet-area">
         
         <div id="stripe_payment_div" style="display:none">
            <div id="card-element"></div>
            <div id="card-errors" role="alert"></div>
          </div>
        </div>
        <div id="paypal-button-container" style="display:none"></div>

        <div class="total__price">
          <span>Total Price</span><span id="price"></span>
        </div>
        <div class="submit_btn"  >
          <input class="buttons" type="submit" name="btn" id="encoder_it_submit_btn_user_form" />
        </div>
      </div>
    </div>
  </form>
</div>
<?php require_once( dirname( __FILE__ ).'/js/add_case_js_file.php' ); ?>