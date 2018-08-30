<?php
/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>
<div id="node-<?php print $node->nid; ?>" class="post <?php if ($page): ?><?php print 'post-page '; ?> <?php endif; ?><?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <?php print $user_picture; ?>
  <?php
  $post_icon = 'standard'; // default
  ?>

  <?php if (!empty($content['field_image'])): ?>
    <?php
    hide($content['field_image']);
    $field_image = field_get_items('node', $node, 'field_image');
    if (!empty($field_image) && count($field_image) == 1):
      ?>

      <?php $image_view_url = file_create_url($field_image[0]['uri']); ?>

      <div class="post-img picture">
        <a rel="image" href="<?php print $image_view_url; ?>">
          <?php print theme('image_style', array('style_name' => 'blog_teaser', 'path' => $field_image[0]['uri'])); ?>
          <div class="image-overlay-zoom"></div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (!empty($field_image) && count($field_image) > 1): ?>
      <?php
      $post_icon = 'gallery';
      ?>
      <?php if (!$page): ?>
        <section class="slider">
          <div class="flexslider subpage">
            <ul class="slides">
              <?php foreach ($field_image as $img): ?>
                <?php $img_view = file_create_url($img['uri']); ?>
                <li><div class="picture"><a href="<?php print $img_view; ?>" rel="image-gallery" title="<?php print $node->title; ?>"><?php print theme('image_style', array('style_name' => 'blog_teaser', 'path' => $img['uri'])); ?><div class="image-overlay-zoom"></div></a></div></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </section>
      <?php endif; ?>

      <?php if ($page): ?>
        <section class="slider">
          <div class="flexslider home">
            <ul class="slides">
              <?php foreach ($field_image as $img): ?>
                <?php $img_view = file_create_url($img['uri']); ?>
                <li><?php print theme('image_style', array('style_name' => 'portfolio_view', 'path' => $img['uri'])); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </section>
      <?php endif; ?>


      <div class="clear"></div>

    <?php endif; ?>
  <?php endif; ?>



  <?php if (!$page): ?>
    <a class="post-icon <?php print $post_icon; ?>" href="<?php print $node_url; ?>"></a>
  <?php endif; ?>


  <div class="<?php if (!$page): ?>post-content<?php endif; ?>"<?php print $content_attributes; ?>>
    <div class="post-title">
      <?php print render($title_prefix); ?>
      <?php if ($node->type == 'blog' || $node->type == 'article') { ?>
        <h2<?php print $title_attributes; ?> class="blogTitle"><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
      <?php } else { ?>

        <?php if (!$page): ?>
          <h2<?php print $title_attributes; ?> class="blogTitle"><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
        <?php endif; ?>
      <?php } ?>
      <?php print render($title_suffix); ?>
    </div>


    <?php if (!$page): ?>

      <?php if ($display_submitted): ?>
        <div class="post-meta submitted">
          <?php print $submitted; ?>
        </div>
      <?php endif; ?>
      <div class="post-description">
        <?php
        // We hide the comments and links now so that we can render them later.
        hide($content['comments']);
        hide($content['links']);
        print render($content);
        ?>
      </div>
    <?php endif; ?>


    <!-- // render display for full view -->
    <?php if ($page): ?>
      <div class="portfolio-view-content">
        <div class="four columns alpha">
          <ul class="project-info">

            <?php
            $project_link = field_get_items('node', $node, 'field_portfolio_link');
            $client = field_get_items('node', $node, 'field_portfolio_client');
            ?>
            <?php if (!empty($client)): ?>
              <li><strong><?php print t('Client'); ?></strong>: <?php print $client[0]['value']; ?> </li>
            <?php endif; ?>
            <li><strong><?php print t('Date'); ?>:</strong> <?php print format_date($node->created, 'custom', 'M Y'); ?></li>
          </ul>
          <?php if (!empty($project_link)): ?>
            <a href="<?php print url($project_link[0]['value']); ?>" class="button color launch"><?php print t('Launch Project'); ?></a>
          <?php endif; ?>
        </div>

        <div class="twelve columns tooltips omega">
          <?php
          // We hide the comments and links now so that we can render them later.
          hide($content['comments']);
          hide($content['links']);
          hide($content['field_portfolio_link']);
          hide($content['field_portfolio_client']);
          print render($content);
          ?>
        </div>
      </div>

    <?php endif; ?>

    <!-- // end render for full view -->


    <?php if (!$page): ?>
      <a href="<?php print $node_url; ?>" class="post-entry"><?php print t('Continue Reading'); ?></a>
    <?php endif; ?>
    <?php if ($page): ?>
      <?php print render($content['links']); ?>
    <?php endif; ?>
  </div>



  <?php print render($content['comments']); ?>

</div>



<?php
$nids = db_query("SELECT n.nid FROM {node} n WHERE n.status = 1 AND n.type = :type AND n.nid <> :nid ORDER BY RAND() LIMIT 0,4", array(':type' => 'portfolio', ':nid' => $node->nid))->fetchCol();

$nodes = node_load_multiple($nids);
?>
<?php if (!empty($nodes)): ?>

  <div class="sixteen columns">
    <!-- Headline -->
    <div style="margin-top: 5px;" class="headline"><h3><?php print t('Related Work'); ?></h3></div>
  </div>

  <?php foreach ($nodes as $node) : ?>
    <!-- 1/4 Column -->
    <div class="four columns">
      <?php $field_image = field_get_items('node', $node, 'field_image'); ?>
      <?php if (!empty($field_image)): ?>
        <div class="picture"><a href="<?php print url('node/' . $node->nid); ?>"><?php print theme('image_style', array('style_name' => 'portfolio_item', 'path' => $field_image[0]['uri'])); ?><div class="image-overlay-link"></div></a></div>
      <?php endif; ?>
      <div class="item-description">
        <h5><?php print l($node->title, 'node/' . $node->nid); ?></h5>
        <?php
        $body = field_get_items('node', $node, 'body');
        if (!empty($body)) {
          print custom_trim_text(array('max_length' => 60), $body[0]['value']);
        }
        ?>
      </div>
    </div>
  <?php endforeach; ?>

<?php endif; ?>


