<?php
$id=$_GET['id'];
if(empty($id) || !isset($_GET['id']))
{
  echo "Sorry This is Wrong Page ID Missing";
  exit;
}
global $wpdb;

$table_name = $wpdb->prefix . 'encoderit_custom_form_services'; 
$row_service = $wpdb->get_row("SELECT * FROM " . $table_name . " where id =$id");

$encoderit_service_with_country = $wpdb->prefix . 'encoderit_service_with_country';
$encoderit_country_with_code = $wpdb->prefix . 'encoderit_country_with_code';

$sql="SELECT *from $encoderit_service_with_country JOIN $encoderit_country_with_code ON $encoderit_service_with_country.country_id = $encoderit_country_with_code.id where $encoderit_service_with_country.service_id=$id and $encoderit_service_with_country.is_active <> 3";

$encoderit_service_with_country_price = $wpdb->get_results($sql);
//print_r($encoderit_service_with_country_price);

if(isset($_POST['btn'])){
 
  
    $table_name = $wpdb->prefix . 'encoderit_custom_form_services';

      $data = array(
        "service_name" => $_POST["service_name"],
        "updated_at" => date('Y-m-d H:i:s')
      );
        $where_condition=array(
            'id' => $id
        );
     
       $update_service_table=$wpdb->update($table_name, $data, $where_condition);
       $table_updated_encoderit_service_with_country=$wpdb->prefix . 'encoderit_service_with_country';
          foreach($_POST['country_names'] as $key=>$value)
          {
            if(!empty($_POST['service_prices'][$key]))
            {
              $info = array(
                'service_id' => $id,
                'country_id' => $_POST['country_names'][$key],
                'price'=>$_POST['service_prices'][$key],
                'is_active'=>$_POST['is_active'][$key],
            );
            $where_condition=array(
                'country_id' => $value,
                'service_id'=>$id
            );
            $updated_encoderit_service_with_country=$wpdb->update($table_updated_encoderit_service_with_country, $info, $where_condition);
            if ($updated_encoderit_service_with_country === FALSE || $updated_encoderit_service_with_country < 1) {
              $sql="SELECT * FROM " . $table_updated_encoderit_service_with_country . " WHERE country_id = '$value' and service_id=$id ";
              $result=$wpdb->get_results($sql);
              if (count($result) == 0)
              {

                $wpdb->insert($table_updated_encoderit_service_with_country, $info);  
              }
          }
            }
          }  
       ?>
        <script>location.reload()</script>
       <?php
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
</style>
<div style="padding: 30px;">
<a href="<?=admin_url() .'admin.php.?page=scf-custom-services'?>" class="button" style="padding:5px 25px;background-color: #2271b1;color:white">Back</a>
<h1>Update Service</h1>
  <form action="" method='POST' enctype="multipart/form-data">
    <label for="">Service Name:</label>
    <input type="text" name="service_name" value="<?=$row_service->service_name?>" style="width:100%;" required>
    <label for="">Service Price:</label>
    <?php 
    $sl=1;
    foreach($encoderit_service_with_country_price as $value)
    {
      
      ?>
          <div class="file_item flex" id="remove_service_update_row_<?=$sl?>">
          <div style="width: 210px;margin-right:10px"><label for="">Country:</label><select class="" name="country_names[]"><option value="<?=$value->country_id?>"><?=$value->country_name?></option></select></div>
          <div style="width: 210px;"><label for="">Service Price:</label><input type="number" min="1"  name="service_prices[]" value="<?=$value->price?>"></div>
          <div style="width: 210px;"><label for="">Is Active:</label><select class="country_names" name="is_active[]"><option value="1" <?php if($value->is_active == 1) echo 'selected'; ?>>Active</option><option value="0" <?php if($value->is_active == 0) echo 'selected'; ?>>Inactive</option></select></div>
          <div style="margin-left: 14px;width: 210px;"><label for="">Remove Button</label>
          <a  href="javascript:void(0)" class="button" data-id="<?=$sl?>" data-country="<?=$value->country_id?>" data-service="<?=$value->service_id?>" id="remove_service_update_<?=$sl?>" onclick="remove_the_service_by_country(this.id)">Delete</a>
        </div>
        </div> 
      <?php
      $sl++;
    }
    
    ?>
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
      newInput +='<div style="width: 210px;margin-right:10px"><label for="">Country:</label><select class="country_names" name="country_names[]">'+country_options+'</select></div>';
      newInput +='<div style="width: 210px;"><label for="">Service Price:</label><input type="number" min="1"  name="service_prices[]"></div>';
      newInput +='<div style="width: 210px;"><label for="">Is Active:</label><select class="country_names" name="is_active[]">'+service_options+'</select></div>';
      newInput +='<div style="margin-left: 14px;width: 210px;"><label for="">Remove Button</label><button class="removefile button">X</button></div>';
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