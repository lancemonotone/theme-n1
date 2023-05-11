<?php 
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
?>
<table id="mm-order-history-table">
	<thead>
		<tr>
      		<th id="mm-order-history-date-column" scope="col"><?php echo _mmt("Order #"); ?></th>
			<th id="mm-order-history-id-column" scope="col"><?php echo _mmt("Order Date"); ?></th>
		 	<th id="mm-order-history-description-column" scope="col"><?php echo _mmt("Description"); ?></th>
			<th id="mm-order-history-amount-column" scope="col"><?php echo _mmt("Amount"); ?></th>
			<th id="mm-order-history-type-column" scope="col"><?php echo _mmt("Type"); ?></th>
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