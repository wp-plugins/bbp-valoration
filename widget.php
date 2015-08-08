<?php
 
class bbpv_widget extends WP_Widget {
 
    function __construct(){
        // Constructor del Widget
        $widget_ops = array('classname' => 'bbpv_widget', 'description' => "Show Topics per votes, visit or replies." );
        parent::__construct('bbpv_widget', "BBpress Valoration Widget", $widget_ops);
    }
 
    function widget($args,$instance){
        // Contenido del Widget que se mostrará en la Sidebar
        extract($args);
        global $wpdb;
        echo $before_widget;  

        $bbpv_option = esc_attr($instance["bbpv_option"]);
        $bbpv_number = ((int)esc_attr($instance["bbpv_number"]));
        $bbpv_title = esc_attr($instance["bbpv_title"]);
        if (isset($instance["bbpv_showvalues"]))
            $bbpv_showvalues = esc_attr($instance["bbpv_showvalues"]);

        //We add the title
        if (!empty($bbpv_title)) { ?>
            <h1 class="widget-title"><?php echo $bbpv_title; ?></h1>
        <?php }

        //set the meta Key according to the choosen option
        switch ($bbpv_option) {
            case 1:
                $meta_key = 'bbpv-votes';
                break;
            case 2:
                $meta_key = 'bbpv-visits';
                break;
            case 3:
                $meta_key = '_bbp_reply_count';
                break;
        } 

        $post = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = '".$meta_key."' ORDER BY CAST(meta_value as DECIMAL) DESC LIMIT ".$bbpv_number);
        ?><ul><?php
        foreach ($post as $key) {
            $meta = get_post_meta($key->post_id, '_bbp_topic_id', true);
            if ($key->post_id==$meta) {
            echo "<li><a href=\"".get_post_permalink($key->post_id)."\">".get_the_title($key->post_id)."</a>";
            if (isset($instance["bbpv_showvalues"]) && $bbpv_showvalues=="1")
            echo " - ".$key->meta_value;
            echo "</li>"; }
        }
        ?></ul><?php

        echo $after_widget;
    }
 
    function update($new_instance, $old_instance){
        // Función de guardado de opciones  
        $instance = $old_instance;
        $instance["bbpv_title"] = strip_tags($new_instance["bbpv_title"]);
        $instance["bbpv_option"] = strip_tags($new_instance["bbpv_option"]);
        $instance["bbpv_number"] = strip_tags($new_instance["bbpv_number"]);
        $instance["bbpv_showvalues"] = strip_tags($new_instance["bbpv_showvalues"]);
        // Repetimos esto para tantos campos como tengamos en el formulario.
        return $instance;      
    }
 
    function form($instance){
        // Formulario de opciones del Widget, que aparece cuando añadimos el Widget a una Sidebar
         ?>
         <div class="widget-content">
         <p>
            <label for="<?php echo $this->get_field_id('bbpv_title'); ?>"><?php _e('Add a title for Widget (Can be empty)','bbpv'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('bbpv_title'); ?>" id="<?php echo $this->get_field_id('bbpv_title'); ?>" value="<?php if (isset($instance["bbpv_title"]) && esc_attr($instance["bbpv_title"])) echo esc_attr($instance["bbpv_title"]); ?>">
         <p>
            <label for="<?php echo $this->get_field_id('bbpv_option'); ?>"><?php _e('Select which option to show','bbpv'); ?></label>
            <select name="<?php echo $this->get_field_name('bbpv_option'); ?>" id="<?php echo $this->get_field_id('bbpv_option'); ?>">
                <option><?php _e('Select an Option','bbpv'); ?></option>
                <option value="1" <?php if (isset($instance["bbpv_option"]) && esc_attr($instance["bbpv_option"])=='1') echo "selected=\"selected\""; ?>><?php _e('per Votes','bbpv');?></option>
                <option value="2" <?php if (isset($instance["bbpv_option"]) && esc_attr($instance["bbpv_option"])=='2') echo "selected=\"selected\""; ?>><?php _e('per Visits','bbpv');?></option>
                <option value="3" <?php if (isset($instance["bbpv_option"]) && esc_attr($instance["bbpv_option"])=='3') echo "selected=\"selected\""; ?>><?php _e('per Replies','bbpv');?></option>
            </select>
         </p>
         <p>
            <label for="<?php echo $this->get_field_id('bbpv_number'); ?>"><?php _e('Select which option to show','bbpv'); ?></label>
            <select name="<?php echo $this->get_field_name('bbpv_number'); ?>" id="<?php echo $this->get_field_id('bbpv_number'); ?>">
                <option><?php _e('Select an Option','bbpv'); ?></option>
                <?php for ($i=1; $i<=10; $i++ ) { ?>
                <option value="<?php echo $i; ?>" <?php if (isset($instance["bbpv_number"]) && esc_attr($instance["bbpv_number"])==$i) echo "selected=\"selected\""; ?>><?php echo $i; ?></option>
                <?php } ?>
            </select>
         </p>
         <p> 
            <label for="<?php echo $this->get_field_id('bbpv_showvalues'); ?>"><?php _e('Check to show the values','bbpv'); ?></label>
            <input type="checkbox" name="<?php echo $this->get_field_name('bbpv_showvalues'); ?>" id="<?php echo $this->get_field_id('bbpv_showvalues'); ?>" value="1" <?php if (isset($instance["bbpv_showvalues"]) && esc_attr($instance["bbpv_showvalues"])=='1') echo "checked=\"checked\""; ?>>
        </div>
        <?php
    }    
} 
 
?>