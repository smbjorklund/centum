<?php

/**
 * Save settings data.
 */
function centum_settings_submit($form, &$form_state) {
  $settings = array();

  // Update image field
  foreach ($form_state['input']['images'] as $image) {
    if (is_array($image)) {
      $image = $image['image'];

      if ($image['image_delete']) {
        // Delete banner file
        file_unmanaged_delete($image['image_path']);
        // Delete banner thumbnail file
        file_unmanaged_delete($image['image_thumb']);
      } else {
        // Update image
        $settings[] = $image;
      }
    }
  }

  // Check for a new uploaded file, and use that if available.
  if ($file = file_save_upload('image_upload')) {
    $file->status = FILE_STATUS_PERMANENT;
    if ($image = _centum_save_image($file)) {
      // Put new image into settings
      $settings[] = $image;
    }
  }

  // Save settings
  centum_set_banners($settings);
}

/**
 * Check if folder is available or create it.
 *
 * @param <string> $dir
 *    Folder to check
 */
function _centum_check_dir($dir) {
  // Normalize directory name
  $dir = file_stream_wrapper_uri_normalize($dir);

  // Create directory (if not exist)
  file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
}

/**
 * Save file uploaded by user and generate setting to save.
 *
 * @param <file> $file
 *    File uploaded from user
 *
 * @param <string> $banner_folder
 *    Folder where save image
 *
 * @param <string> $banner_thumb_folder
 *    Folder where save image thumbnail
 *
 * @return <array>
 *    Array with file data.
 *    FALSE on error.
 */
function _centum_save_image($file, $banner_folder = 'public://banner/', $banner_thumb_folder = 'public://banner/thumb/') {
  // Check directory and create it (if not exist)
  _centum_check_dir($banner_folder);
  _centum_check_dir($banner_thumb_folder);

  $parts = pathinfo($file->filename);
  $destination = $banner_folder . $parts['basename'];
  $setting = array();

  $file->status = FILE_STATUS_PERMANENT;

  // Copy temporary image into banner folder
  if ($img = file_copy($file, $destination, FILE_EXISTS_REPLACE)) {
    // Generate image thumb
    $image = image_load($destination);
    $small_img = image_scale($image, 100, 59);
    $image->source = $banner_thumb_folder . $parts['basename'];
    image_save($image);

    // Set image info
    $setting['image_path'] = $destination;
    $setting['image_thumb'] = $image->source;
    $setting['image_title'] = '';
    $setting['image_description'] = '';
    $setting['image_url'] = '<front>';
    $setting['image_url_title'] = 'Read more';
    $setting['image_weight'] = 0;
    $setting['image_published'] = FALSE;


    return $setting;
  }

  return FALSE;
}

/**
 * Provvide default installation settings for centum.
 */
function _centum_install() {
  // Deafault data
  $file = new stdClass;
  $banners = array();
  // Source base for images

  $src_base_path = drupal_get_path('theme', 'centum');
  $default_banners = theme_get_setting('default_banners', 'centum');


  // Put all image as banners
  foreach ($default_banners as $i => $data) {
    $file->uri = $src_base_path . '/' . $data['image_path'];
    $file->filename = $file->uri;

    $banner = _centum_save_image($file);
    unset($data['image_path']);
    $banner = array_merge($banner, $data);
    $banners[$i] = $banner;
  }

  // Save banner data
  centum_set_banners($banners);

  // Flag theme is installed
  variable_set('theme_centum_first_install', FALSE);
}

/**
 * Generate form to mange banner informations
 *
 * @param <array> $image_data
 *    Array with image data
 *
 * @return <array>
 *    Form to manage image informations
 */
function _centum_banner_form($image_data) {
  $img_form = array();

  // Image preview
  $img_form['image_preview'] = array(
      '#markup' => theme('image', array('path' => $image_data['image_thumb'])),
  );

  // Image path
  $img_form['image_path'] = array(
      '#type' => 'hidden',
      '#value' => $image_data['image_path'],
  );

  // Thumbnail path
  $img_form['image_thumb'] = array(
      '#type' => 'hidden',
      '#value' => $image_data['image_thumb'],
  );

  // Image title
  $img_form['image_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => $image_data['image_title'],
  );

  // Image description
  $img_form['image_description'] = array(
      '#type' => 'textarea',
      '#title' => t('Description'),
      '#default_value' => $image_data['image_description'],
  );


  // Image weight
  $img_form['image_weight'] = array(
      '#type' => 'weight',
      '#title' => t('Weight'),
      '#default_value' => $image_data['image_weight'],
  );

  // Image is published
  $img_form['image_published'] = array(
      '#type' => 'checkbox',
      '#title' => t('Published'),
      '#default_value' => $image_data['image_published'],
  );

  // Delete image
  $img_form['image_delete'] = array(
      '#type' => 'checkbox',
      '#title' => t('Delete image.'),
      '#default_value' => FALSE,
  );

  return $img_form;
}

function centum_get_banners($all = TRUE) {
  // Get all banners
  $banners = variable_get('theme_centum_banner_settings', array());

  // Create list of banner to return
  $banners_value = array();
  foreach ($banners as $banner) {
    if ($all || $banner['image_published']) {
      // Add weight param to use `drupal_sort_weight`
      $banner['weight'] = $banner['image_weight'];
      $banners_value[] = $banner;
    }
  }

  // Sort image by weight
  usort($banners_value, 'drupal_sort_weight');

  return $banners_value;
}

/**
 * Set banner settings.
 *
 * @param <array> $value
 *    Settings to save
 */
function centum_set_banners($value) {
  variable_set('theme_centum_banner_settings', $value);
}

function centum_banners_markup($banners) {

  if ($banners) {
    // Add javascript to manage banners
    // Generate HTML markup for banners
    return centum_banner_markup($banners);
  } else {
    return '';
  }
}

function centum_banners_add_js() {
  
}

/**
 * Generate banners markup.
 *
 * @return <string>
 *    HTML code to display banner markup.
 */
function centum_banner_markup($banners) {

  return centum_default_slider_markup($banners);
}

function centum_default_slider_markup($banners) {
  $output = '<div id="home-slider" class="flexslider home">';
  $output .= '<ul class="slides">';
  foreach ($banners as $i => $banner) {
    $variables = array(
        'path' => $banner['image_path'],
        'alt' => t('@image_desc', array('@image_desc' => $banner['image_title'])),
        'title' => t('@image_title', array('@image_title' => $banner['image_title'])),
        'attributes' => array(
            'class' => 'ob1_img_device1', // hide all the slides except #1
            'id' => 'slide-number-' . $i,
            'longdesc' => t('@image_desc', array('@image_desc' => $banner['image_description']))
        ),
    );
    // Draw image
    $image = theme('image', $variables);

    $output .= '<li>';
    $output .= $image;
    if (!empty($banner['image_title']) || !empty($banner['image_description'])) {
      $output .= '<div class="slide-caption">';
      $output .= '<h3>' . $banner['image_title'] . '</h3>';
      $output .= '<p>' . $banner['image_description'] . '</p>';
      $output .= '</div>';
    }
    $output .= '</li >';
  }
  $output .= '</div>';
  $output .= '</ul>';
  return $output;
}

/**
 * Get banner to show into current page in accord with settings
 *
 * @return <array>
 *    Banners to show
 */
function centum_show_banners() {
  $banners = centum_get_banners(FALSE);

  return $banners;
}