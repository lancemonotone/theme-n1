<?php 
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
?>
<table id="mm-subscriptions-table">
	<thead>
		<tr>
      		<th id="mm-subscriptions-date-column" scope="col">Start Date</th>
			<th id="mm-subscriptions-description-column" scope="col">Description</th>
		 	<th id="mm-subscriptions-amount-column" scope="col">Amount</th>
			<th id="mm-subscriptions-action-column" scope="col">Action</th>
			<th id="mm-subscriptions-status-column" scope="col">Status</th>
      	</tr>
	</thead>
	<tbody>
		<?php foreach($p->datagrid->rows as $key=>$record) { ?>
		<tr>
			<?php foreach($record as $key=>$field) { ?>
				<td><?php echo $field["content"]; ?></td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>