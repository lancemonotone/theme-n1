<?php
extract($info, EXTR_SKIP);
?>

<div style="background-color:#FFF; padding:10px; width:260px;">
  <img src="<?php echo esc_url(MM_IMAGES_URL . '/courses-logo.svg'); ?>" style="width:260px;" />
</div>

<div style="margin-bottom:15px;">
  <a href="http://support.membermouse.com/support/solutions/articles/" target="_blank">Need help configuring the MemberMouse Courses extension?</a>
</div>

<form method="post" action="" class="mm-form">
  <div class="mm-wrap">

    <input type="hidden" name="mm-courses-form" value="save" />
    <table style="width:520px; border-spacing: 10px; border-collapse: separate;">
      <tr>
        <td colspan="2">
          <div style="margin-bottom:10px;">
            <input type="checkbox" name="mm-courses-active" <?php echo ($isActive) ? "checked" : ""; ?> /> Enable Courses Extension
          </div>
        </td>
      </tr>
      <tr>
        <td>Courses Slug</td>
        <td>
          <input type="text" name="mm-courses-slug" style="font-family: courier; font-size:11px; width:250px;" value="<?php echo $coursesSlug; ?>" />
        </td>
      </tr>
      <tr>
        <td>Show Protected Courses in Listing</td>
        <td>
          <label class="switch">
            <input type="checkbox" name="mm-show-protected-courses" <?php echo ($showProtectedCourses) ? "checked" : ""; ?> value="1">
            <span class="slider round"></span>
          </label>
        </td>
      </tr>
      <tr>
        <td>Remove your instructor link</td>
        <td>
          <label class="switch">
            <input type="checkbox" name="mm-remove-instructor-link" <?php echo ($removeInstructorLink) ? "checked" : ""; ?> value="1">
            <span class="slider round"></span>
          </label>
        </td>
      </tr>
    </table>
  </div>

  <div class="mm-wrap">
    <p class="mm-header-text">Classroom Mode</p>

    <div style="margin-top:10px;">
      <input id="mm-enable-classroom-mode" name="mm-enable-classroom-mode" type="checkbox" value="1" <?php echo ($enableClassroomMode) ? "checked" : ""; ?>>
      Enable Classroom Mode
    </div>

    <table id="mm-classroom-fields" style="border-spacing: 10px; border-collapse: separate;">
      <tr>
        <td colspan="2">
          <label>Brand Color</label><br>
          <input type="text" name="mm-brand-color" value="<?php echo esc_attr($brandColor); ?>" class="mm-color-field" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <label>Accent Color</label><br>
          <input type="text" name="mm-accent-color" value="<?php echo esc_attr($accentColor); ?>" class="mm-color-field" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <label>Progress Color</label><br>
          <input type="text" name="mm-progress-color" value="<?php echo esc_attr($progressColor); ?>" class="mm-color-field" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <label>Menu Text Color</label><br>
          <input type="text" name="mm-menu-text-color" value="<?php echo esc_attr($menuTextColor); ?>" class="mm-color-field" />
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <label>Upload Classroom Logo</label>

          <div>
            <a href="#" id="mm-classroom-logo-btn" class="button"><?php esc_html_e('Select Image', 'membermouse'); ?></a>

            <button class="link" id="mm-classroom-logo-remove" style="color: #d63638" type="button">Remove</button>
          </div>
          <div style="background-color:#FFF; padding:10px 0; width:260px;">
            <img src="<?php echo esc_url(wp_get_attachment_url($classroomLogo)); ?>" id="mm-classroom-logo-preview" style="width:260px;" />
            <input value="<?php echo esc_attr($classroomLogo); ?>" type="hidden" name="mm-classroom-logo" id="mm-classroom-logo" />
          </div>

        </td>
      </tr>

      <tr>
        <td><label>Lesson Button Location</label></td>
        <td>
          <select name="mm-lesson-button-location">
            <option value="top" <?php selected($lessonButtonLocation, 'top'); ?>>
              <?php esc_html_e('Top', 'membermouse-courses'); ?></option>
            <option value="bottom" <?php selected($lessonButtonLocation, 'bottom'); ?>>
              <?php esc_html_e('Bottom', 'membermouse-courses'); ?></option>
            <option value="both" <?php selected($lessonButtonLocation, 'both'); ?>>
              <?php esc_html_e('Both', 'membermouse-courses'); ?></option>
          </select>
        </td>
      </tr>

      <tr>
        <td><label>Complete Link CSS </label></td>
        <td>
          <input type="text" name="mm-complete-link-css" value="<?php echo esc_html($completeLinkCSS); ?>" />
        </td>
      </tr>

      <tr>
        <td><label>Previous Link CSS</label></td>
        <td>
          <input type="text" name="mm-previous-link-css" value="<?php echo esc_html($previousLinkCSS); ?>" />
        </td>
      </tr>

      <tr>
        <td><label>Breadcrumb Link CSS</label></td>
        <td>
          <input type="text" name="mm-breadcrumb-link-css" value="<?php echo esc_html($breadcrumbLinkCSS); ?>" />
        </td>
      </tr>

      <tr>
        <td><label>WP Footer Hook</label></td>
        <td>

          <select name="mm-wp-footer">
            <option value="disabled" <?php selected($wpFooterHook, 'disabled'); ?>>
              <?php esc_html_e('Disabled', 'membermouse-courses'); ?></option>
            <option value="enabled" <?php selected($wpFooterHook, 'enabled'); ?>>
              <?php esc_html_e('Enabled', 'membermouse-courses'); ?></option>
          </select>
        </td>
      </tr>

    </table>
  </div>

  <div class="mm-wrap">
    <input type="submit" name="Submit" class="mm-ui-button blue" value="Save Configuration" />
  </div>

</form>

<script>
  jQuery(document).ready(function($) {
    $(".mm-color-field").wpColorPicker();


    $('body').on('click', '#mm-classroom-logo-btn', function(e) {
      e.preventDefault();

      var button = $(this),
        custom_uploader = wp.media({
          title: 'Insert image',
          library: {
            // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
            type: 'image'
          },
          button: {
            text: 'Use this image' // button label text
          },
          multiple: false
        }).on('select', function() {
          var attachment = custom_uploader.state().get('selection').first().toJSON();
          console.log(attachment);
          $('#mm-classroom-logo-preview').attr('src', attachment.url)
          $('#mm-classroom-logo').attr('value', attachment.id)
          $('#mm-classroom-logo-preview').show()
          $('#mm-classroom-logo-remove').show();
          $('#mm-classroom-logo-btn').hide();
        }).open();
    });


    if ($('#mm-classroom-logo').val().length === 0) {
      $('#mm-classroom-logo-preview').hide();
      $('#mm-classroom-logo-remove').hide();
    }

    $('body').on('click', '#mm-classroom-logo-remove', function(e) {
      $('#mm-classroom-logo').attr('value', '')
      $('#mm-classroom-logo-preview').attr('src', '')
      $('#mm-classroom-logo-remove').hide();
      $('#mm-classroom-logo-btn').show();
    });

    $('#mm-enable-classroom-mode').change(function() {
      if ($(this).is(':checked')) {
        $(this).closest('form').find('#mm-classroom-fields').show();
      } else {
        $(this).closest('form').find('#mm-classroom-fields').hide();
      }
    });

    if ($('#mm-enable-classroom-mode').is(':checked')) {
      $('#mm-enable-classroom-mode').closest('form').find('#mm-classroom-fields').show();
    } else {
      $('#mm-enable-classroom-mode').closest('form').find('#mm-classroom-fields').hide();
    }


  });
</script>