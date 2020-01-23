<div class="wrap">
	<h1 class="wp-heading-inline">Customer Issues Management</h1>
	<hr class="wp-header-end">


<h2 class='screen-reader-text'></h2>
<ul class='subsubsub'></ul>

	<table class="wp-list-table widefat fixed striped posts">
		<tr>
			 <th id='title' class='manage-column'>Date</th>
			 <th id='title' class='manage-column'>Customer Name</th>
			 <th id='title' class='manage-column'>Customer Email</th>
			 <th id='title' class='manage-column'>Issue Description</th>
			 <th id='title' class='manage-column'>Priority</th>
		</tr>
		<tbody id="the-list">
		<?php
		  global $wpdb;
		  $result = $wpdb->get_results ( "SELECT * FROM wp_customer_issues" );
		    foreach ( $result as $print )   {

		      echo '<tr>';
		      echo '<td>' . $print->time.'</td>';
		      echo '<td>' . $print->customer_name.'</td>';
		      echo '<td>' . $print->customer_email.'</td>';
		      echo '<td>' . $print->customer_message.'</td>';
		      echo '<td>' . $print->issue_priority.'</td>';
		      echo '</tr>';
		  }
		?> 
		</tbody>
	</table>
</div>
