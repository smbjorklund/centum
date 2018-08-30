<!-- Wrapper Start -->
<div id="wrapper">


  <!-- Header
  ================================================== -->

  <?php if ($page['header_top']): ?>
    <div class="container" id="header-top">
      <?php print render($page['header_top']); ?>
    </div>
  <?php endif; ?>

  <!-- 960 Container -->
  <div class="container ie-dropdown-fix">

    <!-- Header -->
    <div id="header">

      <!-- Logo -->
      <div class="eight columns">
        <?php if ($logo): ?>
          <div id="logo">
            <a href="<?php print $front_page; ?>"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" /></a>
            <?php if ($site_slogan): ?>
              <div id="tagline"><?php print $site_slogan; ?></div>
            <?php endif; ?>
            <div class="clear"></div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Social / Contact -->
      <div class="eight columns">


        <!-- Social Icons -->
        <ul class="social-icons">
          <?php
          $facebook = theme_get_setting('facebook_url', 'centum');
          $twitter = theme_get_setting('twitter_url', 'centum');
          $dribbble = theme_get_setting('dribbble_url', 'centum');
          $linkedin = theme_get_setting('linkedin_url', 'centum');
          $pintrest = theme_get_setting('pintrest_url', 'centum');
          $youtube = theme_get_setting('youtube_url', 'centum');
          ?>
          <?php if (!empty($facebook)): ?>
            <li class="facebook"><a href="<?php print $facebook; ?>">Facebook</a></li>
          <?php endif; ?>

          <?php if ($twitter): ?>
            <li class="twitter"><a href="<?php print $twitter; ?>">Twitter</a></li>
          <?php endif; ?>

          <?php if ($dribbble): ?>
            <li class="dribbble"><a href="<?php print $dribbble; ?>">Dribbble</a></li>
          <?php endif; ?>

          <?php if ($linkedin): ?>
            <li class="linkedin"><a href="<?php print $linkedin; ?>">LinkedIn</a></li>
          <?php endif; ?>

		<?php if ($youtube): ?>
            <li class="youtube"><a href="<?php print $youtube; ?>">Youtube</a></li>
          <?php endif; ?>

          <?php if ($pintrest): ?>
            <li class="pintrest"><a href="<?php print $pintrest; ?>">Pintrest</a></li>
          <?php endif; ?>
        </ul>

        <div class="clear"></div>

        <!-- Contact Details -->
        <div id="contact-details">
          <?php
          $header_contact_mail = theme_get_setting('header_contact_email', 'centum');
          $header_contact_phone = theme_get_setting('header_contact_phone', 'centum');
          ?>
          <?php if (!empty($header_contact_mail) || !empty($header_contact_phone)): ?>
            <ul>
              <?php if (!empty($header_contact_mail)): ?>
                <li><i class="mini-ico-envelope"></i><a href="mailto:<?php print $header_contact_mail; ?>"><?php print $header_contact_mail; ?></a></li>
              <?php endif; ?>

              <?php if (!empty($header_contact_phone)): ?>
                <li><i class="mini-ico-user"></i><?php print $header_contact_phone; ?></li>
              <?php endif; ?>
            </ul>
          <?php endif; ?>
        </div>

      </div>

    </div>
    <!-- Header / End -->

    <?php if ($page['header']): ?>
      <div class="container" id="header">
        <?php print render($page['header']); ?>
      </div>
    <?php endif; ?>

    <?php if ($page['header_below']): ?>
      <div class="container" id="header-below">
        <?php print render($page['header_below']); ?>
      </div>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="sixteen columns">


      <div id="navigation">
        <?php if (!empty($navigation)): ?>
          <?php print $navigation; ?>
        <?php endif; ?>

        <?php if (!empty($seach_block_form)): ?>
          <!-- Search Form -->
          <div class="search-form">
            <?php print $seach_block_form; ?>
          </div>
        <?php endif; ?>

      </div> 
      <div class="clear"></div>

    </div>
    <!-- Navigation / End -->

  </div>
  <!-- 960 Container / End -->


    <?php if ($page['featured']): ?>
            <div class="sixteen columns">
      <div class="container" id="featured">
        <?php print render($page['featured']); ?>
              </div>
      </div>
    <?php endif; ?>


  <?php if ($breadcrumb): ?>
    <div class="container">

      <div class="sixteen columns">
        <div id="breadcrumb"><?php print $breadcrumb; ?></div>
      </div>
    </div>
  <?php endif; ?>





  <?php if ($title): ?>
    <!-- // page title -->
    <div class="container">

      <div class="sixteen columns">

        <!-- Page Title : Print Section Name (Blog) instead of title-->
        <!-- div id="page-title" -->
          <!--h2--><?php //print $title; ?><!--/h2-->
          <!--div id="bolded-line"></div-->
        <!--/div-->
        <!-- Page Title / End -->

      </div>
    </div>
    <!-- // end page title -->
  <?php endif; ?>


  <!-- Content
  ================================================== -->

  <!-- 960 Container -->
  <div class="container">
    <?php if (drupal_is_front_page() && !empty($slider)): ?>
      <!-- Flexslider -->
      <div class="sixteen columns">
        <section class="slider">
          <?php print $slider; ?>
        </section>
      </div>
      <!-- Flexslider / End -->
    <?php endif; ?>

  </div>
  <!-- 960 Container / End -->

  <?php if ($page['home_services']): ?>
    <div class="container">
      <?php print render($page['home_services']); ?>
    </div>
  <?php endif; ?>


  <!-- 960 Container -->
  <div class="container">

    <?php if ($page['highlighted']): ?>
      <div class="sixteen columns">
        <?php print render($page['highlighted']); ?>

      </div>
      <div class="clear"></div>
    <?php endif; ?>



    <?php if ($page['sidebar_first']): ?>
      <div class="sidebar four columns" id="sidebar-first">

        <?php print render($page['sidebar_first']); ?>
      </div>
    <?php endif; ?>


    <?php
    $content_class = 'main-content';
    if ($page['sidebar_second'] || $page['sidebar_first']) {
      if ($page['sidebar_first']) {
        $content_class = 'twelve columns';
      } else {
        $content_class = 'twelve columns';
      }
    }
    ?>

    <div id="content" class="<?php print $content_class; ?>">
      <?php if (!$page['sidebar_first'] && !$page['sidebar_second']): ?>
        <div class="container"><div class="sixteen columns">
          <?php endif; ?>
          <?php print $messages; ?>

          <div class="clear"></div>

          <?php if (!empty($tabs['#primary']) || !empty($tabs['#secondary'])): ?>
            <div class="tabs">
              <?php print render($tabs); ?>
            </div>
          <?php endif; ?>
          <?php print render($page['help']); ?>
          <?php if ($action_links): ?>
            <ul class="action-links">
              <?php print render($action_links); ?>
            </ul>


          <?php endif; ?>
          <?php if (!$page['sidebar_first'] && !$page['sidebar_second']): ?>
          </div></div>
      <?php endif; ?>
      <?php
      $page_content_class = 'page-content';

      if (!($page['sidebar_second']) && !($page['sidebar_first'])) {
        $path_alias = drupal_get_path_alias();
        if (arg(0) == 'node' && is_numeric(arg(1))) {
          $node = node_load(arg(1));
          if ($node->type == 'page' || $node->type == 'portfolio') {
            $path_alias = 'node-page';
          }
        }
        $allow_alias = array(
            'portfolio',
            'portfolio/2c',
            'portfolio/3c',
            'portfolio/4c',
            'front-page',
            'node-page',
        );
        if (!in_array($path_alias, $allow_alias)) {
          $page_content_class = 'sixteen columns';
        }
      }
      ?>
      <?php if ($page['content_top']): ?>
        <div class="content" id="content-top">
          <?php print render($page['content_top']); ?>
        </div>
      <?php endif; ?>

      <div class="<?php print $page_content_class; ?>">
        <?php print render($page['content']); ?>
      </div>

    <?php if ($page['content_bottom']): ?>
      <div class="content" id="content-bottom">
        <?php print render($page['content_bottom']); ?>
      </div>
    <?php endif; ?>

      <?php print $feed_icons; ?>
    </div>

    <?php if ($page['sidebar_second']): ?>
      <div class="sidebar four columns" id="sidebar-second">
        <?php print render($page['sidebar_second']); ?>
      </div>
    <?php endif; ?>

  </div>
  <!-- 960 Container / End -->

</div>
<!-- Wrapper / End -->


<!-- Footer
================================================== -->

<!-- Footer Start -->
<div id="footer">
  <!-- 960 Container -->
  <div class="container">


    <?php if ($page['footer_firstcolumn'] || $page['footer_secondcolumn'] || $page['footer_thirdcolumn'] || $page['footer_fourthcolumn']): ?>
      <!-- 1/4 Columns -->
      <div class="four columns">
        <?php print render($page['footer_firstcolumn']); ?>
      </div>

      <!--  1/4 Columns -->
      <div class="four columns">
        <?php print render($page['footer_secondcolumn']); ?>
      </div>

      <!-- 1/4 Columns -->
      <div class="four columns">
        <?php print render($page['footer_thirdcolumn']); ?>
        <div class="clearfix"></div>
      </div>

      <!-- 1/4 Columns -->
      <div class="four columns">
        <?php print render($page['footer_fourthcolumn']); ?>
      </div>

    <?php endif; ?>

    <!-- Footer / Bottom -->
    <div class="sixteen columns">
      <div id="footer-bottom">
        <?php if ($page['footer_below']): ?>
          <?php print render($page['footer_below']); ?>
        <?php endif; ?>
        <?php print theme_get_setting('footer_copyright_message', 'centum'); ?>
        <div id="scroll-top-top"><a href="#"></a></div>
      </div>
    </div>

  </div>
  <!-- 960 Container / End -->

</div>
<!-- Footer / End -->
