<?php if (!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
} ?>

<div class="wrap">

  <div class="mm-sister-plugin mm-sister-plugin-wp-mail-smtp">

    <div class="mm-sister-plugin-image mp-courses-image">
      <img src="<?php echo esc_url(MM_IMAGES_URL . '/courses-logo.svg'); ?>" width="800" height="216" alt="">
    </div>

    <div class="mm-sister-plugin-title">
      <?php _mmt('Build & Sell Courses Quickly & Easily with MemberMouse'); ?>
    </div>

    <div class="mm-sister-plugin-description">
      <?php _mmt('Get all the ease of use you expect from MemberMouse combined with powerful LMS features designed to make building online courses super simple. This add-on boils it down to a basic, click-and-go process.'); ?>
    </div>

    <div class="mm-sister-plugin-info mm-clearfix">
      <div class="mm-sister-plugin-info-image">
        <div>
          <img src="<?php echo esc_url(MM_IMAGES_URL . '/membermouse-courses.png'); ?>" alt="<?php esc_attr_e('MemberMouse Courses curriculum builder', 'membermouse'); ?>">
        </div>
      </div>
      <div class="mm-sister-plugin-info-features">
        <ul>
          <li style="margin-bottom: 5px; font-size: 13px;"><i class="mp-icon mp-icon-right-big"></i><?php echo _mmt('Powerful LMS features'); ?></li>
          <li style="margin-bottom: 5px; font-size: 13px;"><i class="mp-icon mp-icon-right-big"></i><?php echo _mmt('Create beautiful courses out of the box w/ Classroom Mode'); ?></li>
          <li style="margin-bottom: 5px; font-size: 13px;"><i class="mp-icon mp-icon-right-big"></i><?php echo _mmt('Fully visual drag-and-drop curriculum builder'); ?></li>
          <li style="margin-bottom: 5px; font-size: 13px;"><i class="mp-icon mp-icon-right-big"></i><?php echo _mmt('Protect content with MemberMouse access rules'); ?></li>
          <li style="margin-bottom: 5px; font-size: 13px;"><i class="mp-icon mp-icon-right-big"></i><?php echo _mmt('Track learners\' progress'); ?></li>
        </ul>
      </div>
    </div>

    <div class="mm-sister-plugin-step mm-sister-plugin-step-no-number mm-sister-plugin-step-current mm-clearfix">
      <div class="mm-sister-plugin-step-detail">
        <div class="mm-sister-plugin-step-title">
          <?php if ($info['isInstalled']) : // Installed but not active
          ?>
            <?php echo _mmt('Enable Courses'); ?>
          <?php else : // Not installed
          ?>
            <?php echo _mmt('Install and Activate MemberMouse Courses'); ?>
          <?php endif; ?>
        </div>
        <?php if((MM_MemberMouseService::hasPermission(MM_MemberMouseService::$EXTENSION_COURSES) == MM_MemberMouseService::$ACTIVE)) : ?>
        <div class="mm-sister-plugin-step-button">
          <?php if ($info['isInstalled']) : // Installed but not active
          ?>
            <button type="button" class="mm-courses-action button button-primary button-mm-hero" data-action="activate"><?php echo _mmt('Activate Courses Add-On'); ?></button>
          <?php else : // Not installed
          ?>
            <button type="button" class="mm-courses-action button button-primary button-mm-hero" data-action="install-activate"><?php echo _mmt('Install & Activate MemberMouse Courses Add-On'); ?></button>
          <?php endif; ?>
        </div>
        <?php else : ?>
        <div class="mm-sister-plugin-not-in-plan">
        	<?php echo _mmt('Courses for MemberMouse is available for all current MemberMouse plans (Basic, Plus, Pro and Elite). Quizzes and Certificates are available for Plus, Pro and Elite plans.
MemberMouse customers on legacy plans will need to switch to a currently offered plan for access to these extensions'); ?>
			<p><?php echo sprintf(_mmt("To get access, %supgrade your plan now%s."),'<a href="'.MM_MemberMouseService::getUpgradeUrl(MM_MemberMouseService::$EXTENSION_COURSES).'" target="_blank">','</a>'); ?></p>
        </div>
        <?php endif; ?>
        <div id="mm-action-notice" class="mm-action-notice notice inline">
          <p></p>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  jQuery(document).ready(function($) {
    $('.mm-courses-action').click(function(event) {
      event.preventDefault();
      var $this = $(this);
      $this.prop('disabled', 'disabled');
      var notice = $('#mm-action-notice');
      $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'mm_courses_action',
            nonce: "<?php echo wp_create_nonce('mm_courses_action'); ?>",
            type: $this.data('action')
          },
        })
        .done(function(data) {
          console.log(data);
          $this.remove();
          if ( data.data.redirect.length > 0 ) {
            window.location.href = data.data.redirect;
          } else {
            notice.find('p').html(data.data.message);
            notice.addClass('notice-' + data.data.result);
            notice.show();
            $this.removeProp('disabled');
          }
        })
        .fail(function(data) {
          console.log(data);
          notice.find('p').html(data.data.message);
          notice.addClass('notice-' + data.data.result);
          notice.show();
          $this.removeProp('disabled');
        })
        .always(function(data) {

        });
    });
  });
</script>