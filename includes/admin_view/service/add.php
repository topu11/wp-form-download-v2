<?php
if(isset($_POST['btn'])){
  
    if(empty($_POST['country_names']))
    {
      echo "<script>alert('Please Insert Country Name and Price')</script>";
    }
    else
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'encoderit_custom_form_services';
      $search_data=$_POST["service_name"];
      $sql="SELECT * FROM " . $table_name . " WHERE service_name = '$search_data'";
      $result=$wpdb->get_results($sql);
      if (count($result) != 0)
      {
        echo "<script>alert('Already inserted $search_data Data')</script>";
      }
      else
      {
        $data = array(
          "service_name" => $_POST["service_name"],
          "created_at" => date('Y-m-d H:i:s')
        );
        $inserted = $wpdb->insert($table_name, $data);
        if($inserted)
        {
          $sql="SELECT * FROM " . $table_name . " WHERE service_name = '$search_data'";
          $service_data=$wpdb->get_results($sql);
          
          $table_updated_encoderit_service_with_country=$wpdb->prefix . 'encoderit_service_with_country';
          foreach($_POST['country_names'] as $key=>$value)
          {
            if(!empty($_POST['service_prices'][$key]))
            {
              $info = array(
                'service_id' => $service_data[0]->id,
                'country_id' => $_POST['country_names'][$key],
                'price'=>$_POST['service_prices'][$key],
                'is_active'=>$_POST['is_active'][$key],
            );
            $where_condition=array(
                'country_id' => $value,
                'service_id'=>$service_data[0]->id
            );
            $updated_encoderit_service_with_country=$wpdb->update($table_updated_encoderit_service_with_country, $info, $where_condition);
            if ($updated_encoderit_service_with_country === FALSE || $updated_encoderit_service_with_country < 1) {
              $wpdb->insert($table_updated_encoderit_service_with_country, $info);
              
          }
            }
          }
        }
      } 
    }
    
}
  
  
  

?>
<style type='text/css'>
/* form elements */
form {
  margin:10px; 
  padding: 0 5px;
  background: #F5F5F5;  
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
<div style="padding: 30px;">
<a href="<?=admin_url() .'admin.php.?page=scf-custom-services'?>" class="button" style="padding:5px 25px;background-color: #2271b1;color: black">Back</a>
<h1>Add New Service</h1>
  <form action="" method='POST' enctype="multipart/form-data">
    <label for="">Service Name:</label>
    <input type="text" name="service_name" value="" style="width:100%;" required>
    
    <label for="">Service Price:</label>
    <div id="services_div"></div>
    <button id="addFile" style="display: none;">Add Country</button>
    <br>
    <br>
    <input class="buttons" type="submit" name="btn">    
  </form>
</div>
<script>
  jQuery(document).ready(function () {
          
           let country_options=null;
           var formdata = new FormData();
            formdata.append('action','enoderit_get_country_code');
            formdata.append('nonce','<?php echo wp_create_nonce('admin_ajax_nonce_encoderit_custom_form') ?>')
            jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    processData: false,
                    contentType: false,
                    processData: false,
                    data: formdata,
                    success: function(data) {
                      country_options=data;
                      console.log(country_options)
                      jQuery("#addFile").show();
                    }
              });
              var service_options='<option value="1">Active</option><option value="2">Inactive</option>';

    jQuery("#addFile").on("click", function (e) {
      e.preventDefault();
      var newInput =
      '<div class="file_item flex">';
      newInput +='<div><label for="">Country:</label><select class="country_names" name="country_names[]">'+country_options+'</select></div>';
      newInput +='<div><label for="">Service Price:</label><input type="number" min="1"  name="service_prices[]"></div>';
      newInput +='<div><label for="">Is Active:</label><select class="country_names" name="is_active[]">'+service_options+'</select></div>';
      newInput +='<div><label for="">Remove Button</label><button class="removefile">X</button></div>';
      newInput +='</div>';         
      jQuery("#services_div").append(newInput);
      jQuery('.country_names').select2();
    });

    jQuery(document).on("click", ".removefile", function (e) {
     e.preventDefault();
    jQuery(this).closest(".file_item").remove(); // to get clicked element
     
  });

  });
</script>