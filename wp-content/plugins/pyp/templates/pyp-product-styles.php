<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $post;
echo get_option("pp_product_custom_css");

if (get_option('payyourprice_color_selection') == 'custom') {
    ?>
    .payyourprice_message {
    color: #<?php echo get_option("error_message_color_picker") ?>;
    }
<?php } ?>

.payyourprice_contribution_button {
float:left;
width: 85px;
margin-right:10px;
margin-top:10px;
height:50px;
border: 1px solid #ddd;
background: #<?php echo get_option('pyp_button_color') ?>;
color:#<?php echo get_option('pyp_button_text_color') ?>;
text-align: center;

<?php if (get_option('pyp_button_box_shadow') == '1') { ?>
    box-shadow: inset 0 0 1em #<?php echo get_option('pyp_button_color') ?>, 0 0 1em #<?php echo get_option('pyp_button_text_color') ?>;
<?php } else { ?>
    box-shadow: none;
<?php } ?>
padding-top: 10px;
cursor: pointer;
}

.switch_color_button {
background: #<?php echo get_option('pyp_selected_button_color') ?>;
color:#<?php echo get_option('pyp_selected_button_text_color') ?>;
}

<?php
$product = sumo_pyp_get_product($post->ID);
if (sumo_pyp_get_product_type($product) === 'variable') {
    ?>

    .payyourprice_customize_class {
    display:none;
    }

    .payyourprice_min,.payyourprice_max {
    color:#77a464;
    }

    .single_variation_wraps {
    display:table !important;
    }
<?php
}
