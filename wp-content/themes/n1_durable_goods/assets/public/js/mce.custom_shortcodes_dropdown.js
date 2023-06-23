tinymce.PluginManager.add('custom_shortcodes', function(editor, url) {
  // console.log('custom_shortcodes_data', custom_shortcodes_data);
  editor.addButton('custom_shortcodes_dropdown', {
    type    : 'listbox',
    text    : 'Shortcodes',
    // provided by class.tinymce.php
    values  : custom_shortcodes_data.shortcodes,
    onselect: function(e) {
      editor.insertContent(this.value());
    },
  });
});
