<?php
if (!current_user_can('manage_options')) {
        	return;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) { 
});
</script>
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
</div>
<?php
