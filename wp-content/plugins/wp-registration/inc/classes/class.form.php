<?php 
/**
 * Form Main Class
 * */
 
class WPR_Form 
{
	private $fields;

	var $form_id;
	var $form_fields;
	
	private static $ins = null;

	function __construct( $form_id ){
		
		$this->form_id 		= $form_id;
 
		$this->form_fields	= wpr_get_form_fields($this->form_id);
		$this->has_steps = $this->check_steps();
	
	}
	
	
	// Check if the form has steps fields
	public function check_steps() {
		
		$steps = [];
		if(is_array($this->form_fields)){
			
			foreach($this->form_fields as $index => $field) {
				if( array_key_exists('Step', $field) ) {
					$steps[] = $field;
				}
			}
		}
		
		
		return $steps;
	}

	public static function get_instance() {
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

	function render_form_fields() {
		
		// if step form enable
	
		if(count($this->check_steps()) > 1 && !is_user_logged_in()){
			$this->check_steps();
			
			$width = 100/count($this->check_steps());
			
    		echo '<div id="progressbar" style="text-align:center;">';
			
			foreach($this->form_fields as $index => $field) {

				foreach ($field as $type => $meta) {
				
					if($type == 'Step'){
						if ( $this->is_visible($meta)) {
							    echo '<span class="step" style="width:'.$width.'%"></span>';
							   
						}
					}
				}
			}
		
			echo '</div>';
			$step_started = false;
			$step_no = 1;
			$field_count = 1;
	
			foreach($this->form_fields as $index => $field) {
			
				$field_count++;
			
				foreach ($field as $type => $meta) {
					
					if($type == 'Step' && ! $step_started){
					
						echo '<fieldset class="form-style wpr-tab-reg wpr-step wpr-step-'.$step_no.'">';
						$step_started = true;
						continue;
					}
					
					$field_setting = WPR_META()->get_field_settings($type);
					if (isset( $field_setting['scripts']) ) {
						wpr_load_input_script($type, $field_setting['scripts']);
					}
		
					if ( $this->is_visible($meta) && $type !== 'Step' ) {
						$form_fields = '';
						ob_start();
					
						$field_template = "inputs/{$type}.php";
						$field_meta		= array( 'field_meta' => $meta );
						$field_template = apply_filters('wpr_field_template', $field_template);
						wpr_load_templates( $field_template, $field_meta );
						$form_fields = ob_get_clean();
						echo $form_fields;
					}
					
					if( $type == 'Step' && $step_started ) {
						
						$step_no++;
						echo '<div style="clear:both;">';
						echo '</div>';
						echo '</fieldset>';
						
						echo '<fieldset class="form-style wpr-tab-reg wpr-step wpr-step-'.$step_no.'">';
					}
				}
			}
		
			if($this->has_steps){
				
				
				echo '</fieldset>';
				
				$btn_color = $this->get_option('wpr_btn_label_clr');
				$btn_bg_color = $this->get_option('wpr_btn_bg_clr');
		
				$classes   = $this->get_option('wpr_btn_cls') ? explode(',',$this->get_option('wpr_btn_cls')): array();
		        $classes[] = 'btn';
		        $classes[] = 'btn-info';
		        $btn_class = implode(' ',$classes);
				$font_size = $this->get_option('wpr_label_size');
				
				echo '<div style="overflow:auto;">';
			    echo '<div style="float:right;">';
			    echo '<button type="button" style="color:'.esc_attr($btn_color).'; background:'.esc_attr($btn_bg_color).';font-size:'.esc_attr($font_size).'" id="prevBtn" class="previous action-button-previous '.esc_attr($btn_class).'" onclick="nextPrev(-1)">Previous</button>';
			    echo '<button type="button" style="color:'.esc_attr($btn_color).'; background:'.esc_attr($btn_bg_color).';font-size:'.esc_attr($font_size).'" id="nextBtn" class="next action-button '.esc_attr($btn_class).'" onclick="nextPrev(1)">Next</button>';
			      	echo $this->step_submit_btn(); 
			    echo '</div>';
			  echo '</div>';
			}
	
		}else{
		
			foreach($this->form_fields as $index => $field) {
		
		
				foreach ($field as $type => $meta) {
				
					$field_setting = WPR_META()->get_field_settings($type);
					if (isset( $field_setting['scripts']) ) {
						wpr_load_input_script($type, $field_setting['scripts']);
					}
		
					if ( $this->is_visible($meta) && $type !='Step' ) {
						$form_fields = '';
						ob_start();
						
						
						$field_template = "inputs/{$type}.php";
						$field_meta		= array( 'field_meta' => $meta );
						$field_template = apply_filters('wpr_field_template', $field_template);
						wpr_load_templates( $field_template, $field_meta );
						$form_fields = ob_get_clean();
						echo $form_fields;
					}
				}
			}
		
		}
		
	}
	
	
	public function is_last_step($data_name) {
		
		$return = false;
		$last_field = end($this->form_fields);
		// var_dump($last_field); exit;
		foreach($last_field as $type => $meta){
			
			if( isset($meta['data_name']) && $meta['data_name'] == $data_name ) {
				$return = true;
			}
		}
		
		return $return;
	}

	// submit button form_setting
	function submit_btn(){

		$btn_lable = $this->get_option('wpr_button_label') == ''?'submit':$this->get_option('wpr_button_label');
		$btn_color = $this->get_option('wpr_btn_label_clr');
		$btn_bg_color = $this->get_option('wpr_btn_bg_clr');

		$classes   = $this->get_option('wpr_btn_cls') ? explode(',',$this->get_option('wpr_btn_cls')): array();
        $classes[] = 'btn';
        $classes[] = 'btn-info';
        $btn_class = implode(' ',$classes);
		$font_size = $this->get_option('wpr_label_size');
		$html  = '';
		$html .='<div class="row">';
		$html .='<div class="col-md-12">';
		$html .= '<span class="wpr_sub_form wpr-sign-error">';
		$html .= '<input type="submit" class="'.esc_attr($btn_class).'" value="'.esc_attr($btn_lable).'" style="color:'.esc_attr($btn_color).'; background:'.esc_attr($btn_bg_color).';font-size:'.esc_attr($font_size).'">';
		$html .= '<span class="error wpr_alert"></span>';
		$html .= '<span class="wpr-spinner"></span>';
		$html .= '</span>';
		$html .='</div>';
		$html .='</div>';

		return $html;

	}
	
	function step_submit_btn(){

		$btn_lable = $this->get_option('wpr_button_label') == ''?'Submit':$this->get_option('wpr_button_label');
		$btn_color = $this->get_option('wpr_btn_label_clr');
		$btn_bg_color = $this->get_option('wpr_btn_bg_clr');

		$classes   = $this->get_option('wpr_btn_cls') ? explode(',',$this->get_option('wpr_btn_cls')): array();
        $classes[] = 'btn';
        $classes[] = 'btn-info';
        $btn_class = implode(' ',$classes);
		$font_size = $this->get_option('wpr_label_size');
		$html  = '';
		$html .= '<span class="wpr_sub_form wpr-sign-error" style="margin-top:10px">';
		$html .= '<input type="submit" id="submit_btn" class="wpr-action-submit '.esc_attr($btn_class).'" value="'.esc_attr($btn_lable).'" style="color:'.esc_attr($btn_color).'; background:'.esc_attr($btn_bg_color).';font-size:'.esc_attr($font_size).'">';
		$html .= '<span class="error wpr_alert"></span>';
		$html .= '<span class="wpr-spinner"></span>';
		$html .= '</span>';

		return $html;

	}

	// Handle field visiblity based on settings
	function is_visible( $meta ) {

		$return = false;

		if( !isset($meta['wpr_visible']) ) {

			$return = true;

		} elseif( $meta['wpr_visible'] == 'visible_in_registration' 
			|| $meta['wpr_visible'] == 'visible_in_both') {

			$return = true;
		}

		return apply_filters('wpr_is_registration_field_visible', $return, $meta, $this);
		
	}
	
	// Get form options
	function get_option( $key ) {
		
		$form_option = get_post_meta($this->form_id, $key, true);
		return apply_filters('form_option' , $form_option, $key);
	}

	// Setting as functions
	function auto_login() {
		
		$auto_login = $this->get_option('wpr_auto_login') == 'yes' ? true : false;

		if( wpr_is_email_verification_required() ) {
			$auto_login = false;
		}
		
        return apply_filters('wpr_auto_login', $auto_login, $this);
	}
	
	function get_login_page_url() {
		
		//@Todo: Need to connect login page.
		return '';
	}
	
	function get_form_admins() {
		
		//@Todo: Need to add option for admins otherwise siteadmin
		$admins[] = get_bloginfo('admin_email');
		return apply_filters('wpr_form_admins', $admins, $this);
		
	}
	
	// It will return user meta in key/val
	function get_user_meta() {
		
		return '';
	}

	// Get field meta by data name
	function get_field_by_dataname() {

		
	}
}