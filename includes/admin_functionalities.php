<?php
class encoderit_admin_functionalities
{
    public static function get_service_list()
    {
        require_once( dirname( __FILE__ ).'/admin_view/service/index.php' );
        
    }
    public static function add_new_service()
    {
        require_once( dirname( __FILE__ ).'/admin_view/service/add.php' );
    }
    public static function enoderit_custom_form_admin_submit()
    {
     
        $erros=self::check_validation_data_request();
        
        if (!wp_verify_nonce($_POST['nonce'], 'admin_ajax_nonce_encoderit_custom_form')) {
            echo json_encode([
                'success' => 'error',
                'message'=>'Invalid Nonce field'
            ]);
        }elseif(!empty($erros))
        {
            echo $erros;
        }
        else
        {
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'encoderit_custom_form';
        
            $data = array(
                'files_by_admin' => self::save_files_by_admin(),
                'updated_by' => wp_get_current_user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $where_condition=array(
                'id' => $_POST['form_id']
            );
             
            $inserted=$wpdb->update($table_name, $data, $where_condition);

                if($inserted)
                {
                    self::send_mail_to_user();

                    echo  json_encode([
                        'success' => 'success',
                        'message'=>'Form Submmited Successfully'
                    ]);
                }else
                {
                    echo  json_encode([
                        'success' => 'error',
                        'message'=>'Something worng.;'
                    ]);
                }
        }
        wp_die(); 
    }
    public static function update_service()
    {
        require_once( dirname( __FILE__ ).'/admin_view/service/update.php' );
    }
    public static function save_files_by_admin()
    {
        global $wpdb;
        $id=$_POST['form_id'];
        $table_name = $wpdb->prefix . 'encoderit_custom_form';
        $file_paths=[];
        $result = $wpdb->get_row("SELECT * FROM " . $table_name . " where id =$id");
        if(!empty($result->files_by_admin))
        {

            $file_paths=json_decode($result->files_by_admin,true);
        }
        $wordpress_upload_dir = wp_upload_dir();
       foreach($_FILES['file_array']['name'] as $key=>$value)
       {

         $tail='';
         $file_name_with_addition='';
         $new_file_path = $wordpress_upload_dir['path'] . '/' . $value;
         $i=1;
         while (file_exists($new_file_path)) {
            $i++;
            $new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' .$value;
        }
        if (move_uploaded_file($_FILES['file_array']['tmp_name'][$key], $new_file_path)) {
            if($i>1)
            {
                //$string_json_path_Single=$wordpress_upload_dir['url'].'/' . $i . '_' .$value;
                $tail=$wordpress_upload_dir['subdir'].'/' . $i . '_' .$value;
                $file_name_with_addition= $i . '_' .$value;
            }else
            {
                //$string_json_path_Single=$wordpress_upload_dir['url'].'/'.$value; 
                $tail=$wordpress_upload_dir['subdir'].'/'.$value;
                $file_name_with_addition= $value; 
            }
            
           $single_file=[
            'name'=>$file_name_with_addition,
            'paths'=>$tail
           ];
           array_push($file_paths,$single_file);
        }   
       }
       return json_encode($file_paths);
    }
    public static function send_mail_to_user()
    {
        $search_data=$_POST['form_id'];

        global $wpdb;

        $table_name=$wpdb->prefix . 'encoderit_custom_form';
        $sql="SELECT * FROM " . $table_name . " WHERE id = '$search_data'";
        $result=$wpdb->get_row($sql);
        $subscriber=get_user_by('ID',$result->user_id);
        $to = $subscriber->user_email;

		$subject = 'Admin Upload Files to Your Case ' . ' (' . $subscriber->display_name . ')';

		$message = '<p>Admin Upload Files to Your Case Please Collect them </p>';

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		wp_mail($to, $subject, $message, $headers);
    }
    public static function check_validation_data_request()
    {
        $message='';
         if(empty($_FILES))
         {
            $message .='Please Input files.;';
         }
         if(!empty($message))
         {
            return json_encode([
                'success' => 'error',
                'message'=>$message
            ]);
         }else
         {
            return;
         }
    }

    public static function enoderit_get_country_code()
    {
        global $wpdb;
        $table_name=$wpdb->prefix . 'encoderit_country_with_code';
        $result = $wpdb->get_results("SELECT * FROM " . $table_name . "");
        $html='';
        foreach ($result as $singledata)
        {
            $html .='<option value='.$singledata->id.'>'.$singledata->country_name.'</option>';
        }
        echo $html;
        wp_die();
    }
    public static function enoderit_get_service_by_country()
    {
        global $wpdb;
        $is_payment_show=false;
        $country_id=$_POST['country_id'];
        $encoderit_service_with_country = $wpdb->prefix . 'encoderit_service_with_country';
        $encoderit_custom_form_services = $wpdb->prefix . 'encoderit_custom_form_services';

        $sql="SELECT * FROM $encoderit_service_with_country JOIN $encoderit_custom_form_services ON $encoderit_service_with_country.service_id=$encoderit_custom_form_services.id WHERE $encoderit_service_with_country.is_active=1 and $encoderit_service_with_country.country_id=$country_id";
        $result = $wpdb->get_results($sql);
        $html='';
        if(!empty($result))
        {
            foreach($result as $key=>$value)
            {
                 $html .='<div class="product__item d-flex-center">
                 <input
                   type="checkbox"
                   class="encoder_it_custom_services"
                   data-price="'.$value->price.'"
                   onclick="add_total_price(this.id)"
                   id="encoder_it_custom_services'.$value->service_id.'"
                   name="encoder_it_custom_services[]"
                   value="'.$value->id.'"
                 />
                 <label class="d-flex-center">
                   <span>'.$value->service_name.'</span>
                   <span>$'.$value->price.'</span>
                 </label>
               </div>';
    
            }
            $is_payment_show=true;
        }else
        {
            $html='<p>No service found for this country</p>';
        }
        
        echo json_encode(['html'=>$html,'is_payment_show'=>$is_payment_show]);
        wp_die();
    }
}