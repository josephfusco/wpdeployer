jQuery(document).ready(function() {
  // Spinning update buttons
  jQuery(".button-update-package").click(function(e) {
    var button = this;

    jQuery(button).html('<i class="fa fa-refresh fa-spin"></i>&nbsp; Updating');
    jQuery(button).blur();
  });
});
