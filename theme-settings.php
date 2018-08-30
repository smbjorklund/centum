<?php

require_once 'includes/slider.inc';

function centum_form_system_theme_settings_alter(&$form, $form_state) {

  $theme_path = drupal_get_path('theme', 'centum');
  drupal_add_js($theme_path . '/js/colorpicker.js');
  drupal_add_css($theme_path . '/css/colorpicker.css');


  _centum_custom_js();
  $form['settings'] = array(
      '#type' => 'vertical_tabs',
      '#title' => t('Theme settings'),
      '#weight' => 2,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
  );




  $form['settings']['header_contact'] = array(
      '#type' => 'fieldset',
      '#title' => t('Header contact info settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
  );

  $form['settings']['header_contact']['header_contact_email'] = array(
      '#title' => t('Email address'),
      '#type' => 'textfield',
      '#default_value' => theme_get_setting('header_contact_email', 'centum'),
  );
  $form['settings']['header_contact']['header_contact_phone'] = array(
      '#title' => t('Phone number'),
      '#type' => 'textfield',
      '#default_value' => theme_get_setting('header_contact_phone', 'centum'),
  );


  $form['settings']['social_links'] = array(
      '#type' => 'fieldset',
      '#title' => t('Social links settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
  );

  $social_links = array(
      'facebook' => t('Facebook URL'),
      'twitter' => t('Twitter URL'),
      'dribbble' => t('Dribbble URL'),
      'linkedin' => t('LinkedIn URL'),
      'pintrest' => t('Pintrest URL'),
      'youtube' => t('Youtube URL')
  );

  foreach ($social_links as $name => $label) {

    $form['settings']['social_links'][$name . '_url'] = array(
        '#type' => 'textfield',
        '#title' => $label,
        '#default_value' => theme_get_setting($name . '_url', 'centum'),
    );
  }

  $form['settings']['portfolio'] = array(
      '#type' => 'fieldset',
      '#title' => t('Portfolio settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
  );

  $form['settings']['portfolio']['default_portfolio'] = array(
      '#type' => 'select',
      '#title' => t('Default portfolio display'),
      '#options' => array(
          '2c' => 'Portfolio - 2cols',
          '3c' => 'Portfolio - 3cols',
          '4c' => 'portfolio - 4cols',
      ),
      '#default_value' => theme_get_setting('default_portfolio', 'centum'),
  );



  $form['settings']['portfolio']['default_nodes_portfolio'] = array(
      '#type' => 'select',
      '#title' => t('Number nodes show on portfolio page'),
      '#options' => drupal_map_assoc(array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 25, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 100)),
      '#default_value' => theme_get_setting('default_nodes_portfolio', 'centum'),
  );


  $form['settings']['footer'] = array(
      '#type' => 'fieldset',
      '#title' => t('Footer settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
  );

  $form['settings']['footer']['footer_copyright_message'] = array(
      '#type' => 'textarea',
      '#title' => t('Footer copyright message'),
      '#default_value' => theme_get_setting('footer_copyright_message', 'centum'),
  );

  $form['settings']['skin'] = array(
      '#type' => 'fieldset',
      '#title' => t('Skin settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
  );



  $dir = drupal_get_path('theme', 'centum') . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'colors';

  $files = file_scan_directory($dir, '/.*\.css/');

  $css_files = array();
  if (!empty($files)) {
    foreach ($files as $file) {
      if (isset($file->filename)) {
        $css_files[$file->filename] = $file->filename;
      }
    }
  }


  $form['settings']['skin']['theme_layout_style'] = array(
      '#title' => t('Layout style'),
      '#type' => 'select',
      '#options' => array('boxed.css' => t('Boxed'), 'wide.css' => t('Wide')),
      '#default_value' => theme_get_setting('theme_layout_style', 'centum'),
  );
  $form['settings']['skin']['theme_color'] = array(
      '#type' => 'select',
      '#title' => t('Select default color'),
      '#default_value' => theme_get_setting('theme_color', 'centum'),
      '#options' => $css_files,
  );

  // bg color

  $form['settings']['skin']['theme_background_color'] = array(
      '#type' => 'textfield',
      '#title' => t('Background color'),
      '#description' => t('This feature is for boxed style. If you would set backgournd color, you must select Background image bellow to [None].'),
      '#default_value' => theme_get_setting('theme_background_color', 'centum'),
      '#attributes' => array('class' => array('colorSelector')),
  );


  // bg background
  $dir = drupal_get_path('theme', 'centum') . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'bg';

  $files = file_scan_directory($dir, '/.*\.png/');


  $bg_files = array('' => t('[None]'));
  if (!empty($files)) {
    foreach ($files as $file) {
      if (isset($file->filename)) {
        $bg_files[$file->filename] = $file->filename;
      }
    }
  }

  $form['settings']['skin']['theme_background_image'] = array(
      '#title' => t('Background image'),
      '#type' => 'select',
      '#default_value' => theme_get_setting('theme_background_image', 'centum'),
      '#options' => $bg_files,
      '#description' => t('This feature is for boxed style.')
  );



//slider
  $form['slider'] = array(
      '#type' => 'fieldset',
      '#title' => t('Slider managment'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#weight' => 100,
  );

  $banners = centum_get_banners();
  $form['slider']['banner']['images'] = array(
      '#type' => 'vertical_tabs',
      '#title' => t('Banner images'),
      '#weight' => 2,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
  );

  $i = 0;
  foreach ($banners as $image_data) {
    $form['slider']['banner']['images'][$i] = array(
        '#type' => 'fieldset',
        '#title' => t('Image !number: !title', array('!number' => $i + 1, '!title' => $image_data['image_title'])),
        '#weight' => $i,
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
        '#tree' => TRUE,
        // Add image config form to $form
        'image' => _centum_banner_form($image_data),
    );

    $i++;
  }

  $form['slider']['banner']['image_upload'] = array(
      '#type' => 'file',
      '#title' => t('Upload a new image'),
      '#weight' => $i,
  );

  $form['#submit'][] = 'centum_settings_submit';
}

function _centum_custom_js() {


  $js_code = "(function ($)  {
    $(document).ready(function(){
      $('.colorSelector').each(function(){
        var \$select_item = $(this);
        
        \$select_item.ColorPicker({
          color: $(this).val(),
          onShow: function (colpkr) {
            $(colpkr).fadeIn(500);
            return false;
          },
          onHide: function (colpkr) {
            $(colpkr).fadeOut(500);
            return false;
          },
          onChange: function (hsb, hex, rgb) {
            \$select_item.val('#' + hex);
            
          }
        });
      
      
      });
      
      
    });
  })(jQuery);";

  drupal_add_js($js_code, 'inline');
}