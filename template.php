<?php

include_once __DIR__ . '/includes/custom_menu.inc';
include_once __DIR__ . '/includes/slider.inc';

function _centum_add_css() {
  $theme_path = path_to_theme();
  $css_arr = [
    'css/base.css',
    'css/skeleton.css',
    'css/fancybox.css',
    'css/style.css',
  ];

  foreach ($css_arr as $css) {
    drupal_add_css($theme_path . '/' . $css);
  }

  $theme_layout_style = theme_get_setting('theme_layout_style', 'centum');
  drupal_add_css($theme_path . '/css/' . $theme_layout_style);
  $default_color = theme_get_setting('theme_color', 'centum');
  drupal_add_css($theme_path . '/css/colors/' . $default_color);
  drupal_add_css($theme_path . '/css/centum.css');
}

/**
 * Implements hook_preprocess_html().
 */
function centum_preprocess_html(&$variables) {
  $theme_path = path_to_theme();
  _centum_add_css();

  drupal_add_html_head(
    [
      '#tag' => 'meta',
      '#attributes' =>
        [
          'name' => 'viewport',
          'content' => 'width=device-width, initial-scale=1',
        ],
    ], 'centum:viewport_meta'
  );
}

function centum_preprocess_page(&$vars) {
  if (isset($vars['node'])) {
    $vars['theme_hook_suggestions'][] = 'page__'. $vars['node']->type;
  }

  $custom_main_menu = _custom_main_menu_render_superfish();
  if (!empty($custom_main_menu['content'])) {
    $vars['navigation'] = $custom_main_menu['content'];
  }

  if (arg(0) === 'node' && arg(1)) {
    $nid = arg(1);
    $node = node_load($nid);
    if ($node->type === 'blog') {
      $vars['title'] = t('Blog');
    }
  }

  if (variable_get('theme_centum_first_install', TRUE)) {
    _centum_install();
  }

  $banners = centum_show_banners();
  $vars['slider'] = centum_banners_markup($banners);
  $seach_block_form = drupal_get_form('search_block_form');
  $seach_block_form['#id'] = 'searchform';
  $seach_block_form['search_block_form']['#attributes']['class'][] = 'search-text-box';
  $vars['seach_block_form'] = drupal_render($seach_block_form);
}

function centum_format_comma_field($field_category, $node, $limit = NULL) {
  $category_arr = array();

  if (!empty($node->{$field_category}[LANGUAGE_NONE])) {
    foreach ($node->{$field_category}[LANGUAGE_NONE] as $item) {
      $term = taxonomy_term_load($item['tid']);
      if ($term) {
        $category_arr[] = l($term->name, 'taxonomy/term/' . $item['tid']);
      }

      if ($limit && count($category_arr) === $limit) {
        return implode(', ', $category_arr);
      }
    }
  }

  return implode(', ', $category_arr);
}

function centum_preprocess_node(&$variables) {
  $variables['view_mode'] = $variables['elements']['#view_mode'];
  $variables['teaser'] = $variables['view_mode'] == 'teaser';
  $variables['node'] = $variables['elements']['#node'];
  $node = $variables['node'];
  $variables['date'] = format_date($node->created);
  $variables['name'] = theme('username', array('account' => $node));
  $uri = entity_uri('node', $node);
  $variables['node_url'] = url($uri['path'], $uri['options']);
  $variables['title'] = check_plain($node->title);
  $variables['page'] = $variables['view_mode'] == 'full' && node_is_page($node);
  $variables = array_merge((array) $node, $variables);
  $variables += array('content' => array());

  foreach (element_children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }

  field_attach_preprocess('node', $node, $variables['content'], $variables);

  if (variable_get('node_submitted_' . $node->type, TRUE)) {
    $variables['display_submitted'] = TRUE;
    $submitted = '<span><i class="mini-ico-calendar"></i>' . t('On') . ' ' . format_date($node->created, 'custom', 'd M, Y') . '</span> <span><i class="mini-ico-user"></i>' . t('By') . ' ' . $variables['name'] . '</span> ';

    if (!empty($node->comment_count)) {
      $submitted .= '<span><i class="mini-ico-comment"></i>' . t('With') . ' <a href="' . url('node/' . $node->nid) . '#comments">' . $node->comment_count . ' ' . t('Comments') . '</a></span>';
    }

    $variables['submitted'] = $submitted;
    $variables['user_picture'] = theme_get_setting('toggle_node_user_picture') ? theme('user_picture', array('account' => $node)) : '';
  } else {
    $variables['display_submitted'] = FALSE;
    $variables['submitted'] = '';
    $variables['user_picture'] = '';
  }

  $variables['classes_array'][] = drupal_html_class('node-' . $node->type);
  if ($variables['promote']) {
    $variables['classes_array'][] = 'node-promoted';
  }
  if ($variables['sticky']) {
    $variables['classes_array'][] = 'node-sticky';
  }
  if (!$variables['status']) {
    $variables['classes_array'][] = 'node-unpublished';
  }
  if ($variables['teaser']) {
    $variables['classes_array'][] = 'node-teaser';
  }
  if (isset($variables['preview'])) {
    $variables['classes_array'][] = 'node-preview';
  }

  $variables['theme_hook_suggestions'][] = 'node__' . $node->type;
  $variables['theme_hook_suggestions'][] = 'node__' . $node->nid;
}

function centum_form_alter(&$form, &$form_state, $form_id) {
  if (!empty($form['actions']['submit'])) {
    $form['actions']['submit']['#attributes']['class'][] = 'button color';
  }

  if (isset($form['actions']['preview'])) {
    $form['actions']['preview']['#attributes']['class'][] = 'button color';
  }

  if (isset($form['submit'])) {
    $form['submit']['#attributes']['class'] = array('button color');
  }

  switch ($form_id) {
    case 'search_block_form':
      if (!empty($form['search_block_form'])) {
        $form['search_block_form']['#prefix'] = '<div class="search">';
        $form['search_block_form']['#suffix'] = '</div>';
      }
      break;
    case 'contact_site_form':
      $form['#prefix'] = '<div class="headline no-margin"><h4>' . t('Contact Form') . '</h4></div>';
      break;
  }
}

function centum_status_messages(&$variables) {
  $display = $variables['display'];
  $output = '';
  $message_info = array(
      'status' => array(
          'heading' => 'Status message',
          'class' => 'success',
      ),
      'error' => array(
          'heading' => 'Error message',
          'class' => 'error',
      ),
      'warning' => array(
          'heading' => 'Warning message',
          'class' => '',
      ),
  );

  foreach (drupal_get_messages($display) as $type => $messages) {
    $message_class = $type != 'warning' ? $message_info[$type]['class'] : 'warning';
    $output .= "<div class=\"notification alert alert-block alert-$message_class $message_class closeable fade in\">\n";

    if (!empty($message_info[$type]['heading'])) {
      $output .= '<h2 class="element-invisible">' . $message_info[$type]['heading'] . "</h2>\n";
    }

    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    } else {
      $output .= $messages[0];
    }
    $output .= "</div>\n";
  }
  return $output;
}

function centum_tagadelic_weighted(array $vars) {
  $terms = $vars['terms'];
  $output = '<div class="tags">';

  foreach ($terms as $term) {
    $output .= l($term->name, 'taxonomy/term/' . $term->tid, array(
                'attributes' => array(
                    'class' => array("tagadelic", "level" . $term->weight),
                    'rel' => 'tag',
                    'title' => $term->description,
                )
                    )
            ) . " \n";
  }

  if (count($terms) >= variable_get('tagadelic_block_tags_' . $vars['voc']->vid, 12)) {
    $output .= theme('more_link', array('title' => t('more tags'), 'url' => "tagadelic/chunk/{$vars['voc']->vid}"));
  }

  $output .= '</div>';
  return $output;
}

function centum_pager($variables) {
  $tags = $variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $quantity = $variables['quantity'];
  global $pager_page_array, $pager_total;
  $pager_middle = ceil($quantity / 2);
  $pager_current = $pager_page_array[$element] + 1;
  $pager_first = $pager_current - $pager_middle + 1;
  $pager_last = $pager_current + $quantity - $pager_middle;
  $pager_max = $pager_total[$element];

  $i = $pager_first;
  if ($pager_last > $pager_max) {
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }

  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }

  $li_first = theme('pager_first', array('text' => (isset($tags[0]) ? $tags[0] : t('« first')), 'element' => $element, 'parameters' => $parameters));
  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('‹ previous')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next = theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('next ›')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_last = theme('pager_last', array('text' => (isset($tags[4]) ? $tags[4] : t('last »')), 'element' => $element, 'parameters' => $parameters));

  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
          'class' => array('pager-first'),
          'data' => $li_first,
      );
    }

    if ($li_previous) {
      $items[] = array(
          'class' => array('pager-previous'),
          'data' => $li_previous,
      );
    }

    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array(
            'class' => array('pager-ellipsis'),
            'data' => '…',
        );
      }
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
              'class' => array('pager-item'),
              'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
              'class' => array('pager-current', 'current'),
              'data' => $i,
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
              'class' => array('pager-item'),
              'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
      if ($i < $pager_max) {
        $items[] = array(
            'class' => array('pager-ellipsis'),
            'data' => '…',
        );
      }
    }

    if ($li_next) {
      $items[] = array(
          'class' => array('pager-next'),
          'data' => $li_next,
      );
    }

    if ($li_last) {
      $items[] = array(
          'class' => array('pager-last'),
          'data' => $li_last,
      );
    }

    return '<h2 class="element-invisible">' . t('Pages') . '</h2>' . theme('item_list', array(
                'items' => $items,
                'attributes' => array('class' => array('pager', 'pagination')),
            ));
  }
}

function centum_table($variables) {
  $header = $variables['header'];
  $rows = $variables['rows'];
  $attributes = $variables['attributes'];
  $caption = $variables['caption'];
  $colgroups = $variables['colgroups'];
  $sticky = $variables['sticky'];
  $empty = $variables['empty'];

  if (count($header) && $sticky) {
    drupal_add_js('misc/tableheader.js');
    $attributes['class'][] = 'sticky-enabled';
  }

  $attributes['class'][] = 'standard-table'; // added default table style.
  $output = '<table' . drupal_attributes($attributes) . ">\n";

  if (isset($caption)) {
    $output .= '<caption>' . $caption . "</caption>\n";
  }

  if (count($colgroups)) {
    foreach ($colgroups as $number => $colgroup) {
      $attributes = array();

      // Check if we're dealing with a simple or complex column
      if (isset($colgroup['data'])) {
        foreach ($colgroup as $key => $value) {
          if ($key == 'data') {
            $cols = $value;
          } else {
            $attributes[$key] = $value;
          }
        }
      } else {
        $cols = $colgroup;
      }

      if (is_array($cols) && count($cols)) {
        $output .= ' <colgroup' . drupal_attributes($attributes) . '>';
        $i = 0;
        foreach ($cols as $col) {
          $output .= ' <col' . drupal_attributes($col) . ' />';
        }
        $output .= " </colgroup>\n";
      } else {
        $output .= ' <colgroup' . drupal_attributes($attributes) . " />\n";
      }
    }
  }

  if (!count($rows) && $empty) {
    $header_count = 0;
    foreach ($header as $header_cell) {
      if (is_array($header_cell)) {
        $header_count += isset($header_cell['colspan']) ? $header_cell['colspan'] : 1;
      } else {
        $header_count++;
      }
    }
    $rows[] = array(array('data' => $empty, 'colspan' => $header_count, 'class' => array('empty', 'message')));
  }

  if (count($header)) {
    $ts = tablesort_init($header);
    // HTML requires that the thead tag has tr tags in it followed by tbody
    // tags. Using ternary operator to check and see if we have any rows.
    $output .= (count($rows) ? ' <thead><tr>' : ' <tr>');
    foreach ($header as $cell) {
      $cell = tablesort_header($cell, $header, $ts);
      $output .= _theme_table_cell($cell, TRUE);
    }
    // Using ternary operator to close the tags based on whether or not there are rows
    $output .= (count($rows) ? " </tr></thead>\n" : "</tr>\n");
  } else {
    $ts = array();
  }

  if (count($rows)) {
    $output .= "<tbody>\n";
    $flip = array('even' => 'odd', 'odd' => 'even');
    $class = 'even';
    foreach ($rows as $number => $row) {
      $attributes = array();

      // Check if we're dealing with a simple or complex row
      if (isset($row['data'])) {
        foreach ($row as $key => $value) {
          if ($key == 'data') {
            $cells = $value;
          } else {
            $attributes[$key] = $value;
          }
        }
      } else {
        $cells = $row;
      }
      if (count($cells)) {
        // Add odd/even class
        if (empty($row['no_striping'])) {
          $class = $flip[$class];
          $attributes['class'][] = $class;
        }

        // Build row
        $output .= ' <tr' . drupal_attributes($attributes) . '>';
        $i = 0;
        foreach ($cells as $cell) {
          $cell = tablesort_cell($cell, $header, $ts, $i++);
          $output .= _theme_table_cell($cell);
        }
        $output .= " </tr>\n";
      }
    }
    $output .= "</tbody>\n";
  }

  $output .= "</table>\n";
  return $output;
}

function centum_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  if (!empty($breadcrumb)) {
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    $output .= '<div class="breadcrumb">' . implode(' » ', $breadcrumb) . '</div>';
    return $output;
  }
}
