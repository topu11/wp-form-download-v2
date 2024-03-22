<?php

require_once( WP_PLUGIN_DIR . '/SuncodeIT-Custom-Form'.'/assets/css/main.php' );  
 require_once( WP_PLUGIN_DIR . '/SuncodeIT-Custom-Form'.'/includes/user_functionalities.php' );   

?>
<style type='text/css'>
/* form elements */
form {
}
label {
  display:block;
  font-weight:bold;
  margin:15px 0;
}
input {
  padding:2px;
  border:1px solid #eee;
  font: normal 1em Verdana, sans-serif;
  color:#777;
}
select
{
 padding:2px;
  border:1px solid #eee;
  font: normal 1em Verdana, sans-serif;
  color:#777;
  width:100%;
}
input.buttons { 
  font: bold 12px Arial, Sans-serif; 
  height: 50px;
  width: 150px;
  margin-top:20px;
  margin-left:200px;
  margin-bottom: 50px;
  cursor: pointer;  
  color: #333;
  background: #e7e6e6 url(MarketPlace-images/button.jpg) repeat-x;
  border: 1px solid #dadada;
}
.flex{
  display: flex;
}
.removefile
{
  margin-left:5px ;
}
</style>


<div class="wrap pbwp">
<h1 class="wp-heading-inline">Set Country Wise Fixed Services</h1>
<?php
global $wpdb;
 $encoderit_country_with_code=$wpdb->prefix . 'encoderit_country_with_code';
$fixed_services=encoderit_admin_functionalities::fiexed_service();
$checkboxes='';
foreach($fixed_services as $key=>$value)
        {
          $check='';
          if($value['service_id']=="fixed_id_8")
          {
            $check='checked';
          }
            if($value['is_input'])
            {
                $checkboxes .='<div class="product__item d-flex-center">
                <input
                  type="checkbox"
                  class="encoder_it_fixed_services_create"
                  data-price="'.$value['service_price'].'"
                  data-name="'.$value['service_name'].'"
                  id="encoder_it_custom_services'.$value['service_id'].'"
                  data-service_id="'.$value['service_id'].'"
                  data-is_input="true"
                  name="encoder_it_custom_services[]"
                  value="'.$value['service_id'].'"
                  '. $check.'
                />
                <label class="d-flex-center">
                  <span>'.$value['service_name'].'</span>
                <input type="number" min="1" class="fixed_service_prices" value="1" id="x_'.$value['service_id'].'">
                </label>
              </div>';
            }else
            {
                $checkboxes .='<div class="product__item d-flex-center">
                <input
                  type="checkbox"
                  class="encoder_it_fixed_services_create"
                  data-price="'.$value['service_price'].'"
                  data-name="'.$value['service_name'].'"
               
                  id="encoder_it_custom_services'.$value['service_id'].'"
                  data-service_id="'.$value['service_id'].'"
                  data-is_input="false"
                  name="encoder_it_custom_services[]"
                  value="'.$value['service_id'].'"
                  '. $check.'
                />
                <label class="d-flex-center">
                  <span>'.$value['service_name'].'</span>
                  <input type="number" min="1" class="fixed_service_prices" value="1" id="x_'.$value['service_id'].'">
                </label>
              </div>';
            }
            
        }
$sql="SELECT * FROM   $encoderit_country_with_code" ;
$result = $wpdb->get_results($sql);
$html='<option value="0">Please select country</option>';
foreach ($result as $singledata)
{
    $html .='<option value='.$singledata->id.'>'.$singledata->country_name.'</option>';
}
?>

<a href="<?=admin_url() .'admin.php.?page=scf-custom-services'?>" class="button" style="padding:5px 25px;background-color: #2271b1;color: white">Back</a>

  <form action="" method='POST' enctype="multipart/form-data">
    <label for="">Select Country:</label>
    <select name="select_country" id="select_country"><?=$html?></select>
    <div class="right_col product__container" id="service_container">
    <?=$checkboxes?>
   </div>
    <br>
    <br>
    <input class="buttons button-primary" type="submit" value="Save" name="btn" id="fixed_service_check">    

  </form>
</div>
<script>
  jQuery(document).ready(function () {
    jQuery("#select_country").select2();
     
    jQuery('#fixed_service_check').on('click',function(e){
      e.preventDefault();
      var service=document.getElementsByClassName("encoder_it_fixed_services_create");
      var checked_servie=[];
      var checked_servie_data_ids=[];
      var checked_servie_data_name=[];
      var checked_servie_data_is_input=[];
      var checked_servie_price=[];

      for(var j=0;j<service.length;j++)
      {
        if(service[j].checked)
        {
          checked_servie.push(service[j].id);
          checked_servie_data_ids.push(service[j].getAttribute('data-service_id'));
          checked_servie_data_name.push(service[j].getAttribute('data-name'));
          checked_servie_data_is_input.push(service[j].getAttribute('data-is_input'));
        }
      }
      for(var k=0;k<checked_servie_data_ids.length;k++)
      {
        checked_servie_price.push(document.getElementById("x_"+checked_servie_data_ids[k]).value)
      }
      console.log(checked_servie);
      console.log(checked_servie_data_ids);
      console.log(checked_servie_data_name);
      console.log(checked_servie_data_is_input);
      console.log(checked_servie_price);
      var country_id=document.getElementById("select_country").value;         
      if(country_id == 0 || service.length == 0)
      {
        alert('please Select Country')
      }else
      {
        var formdata = new FormData();
              formdata.append('action','fixed_service_save');
              formdata.append('checked_servie',checked_servie);
              formdata.append('checked_servie_data_ids',checked_servie_data_ids);
              formdata.append('checked_servie_data_name',checked_servie_data_name);
              formdata.append('checked_servie_data_is_input',checked_servie_data_is_input);
              formdata.append('checked_servie_price',checked_servie_price);
              formdata.append('country_id',country_id);
              formdata.append('nonce','<?php echo wp_create_nonce('admin_ajax_nonce_encoderit_custom_form') ?>')
              jQuery.ajax({
                      url: '<?php echo admin_url('admin-ajax.php'); ?>',
                      type: 'post',
                      processData: false,
                      contentType: false,
                      processData: false,
                      dataType:"json",
                      data: formdata,
                      success: function(obj) {
                        console.log(obj.status);
                          if(obj.status == "success")
                          {
                            location.href = '<?=admin_url('admin.php?page=scf-fixed-service-list')?>';
                          }
                        
                      }
                });
      }
    })
     
          
  });
</script>