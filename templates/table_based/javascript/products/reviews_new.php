<script type="text/javascript"><!--
function checkForm(form_name) {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var review = form_name.review.value;

  if (review.length < <?php echo REVIEW_TEXT_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_REVIEW_TEXT; ?>";
    error = 1;
  }

  if ((form_name.rating[0].checked) || (form_name.rating[1].checked) || (form_name.rating[2].checked) || (form_name.rating[3].checked) || (form_name.rating[4].checked)) {
  } else {
    error_message = error_message + "<?php echo JS_REVIEW_RATING; ?>";
    error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}
//--></script>
