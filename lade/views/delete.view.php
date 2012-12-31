<?
	$page->Title = 'Delete';
	$page->BodyId = 'delete';
?>

<h3>
	Delete
	<? /* [ <?=(SectionInfo.ParentSection != null) ? SectionInfo.ParentSection.DisplayName + " : " + SectionInfo.ParentSection.Value[SectionInfo.ParentSection.Table.DisplayColumn.Name] + " - " : ""?>
	<?=SectionInfo.DisplayName?> ]
	<?=SectionInfo.Value[SectionInfo.Table.DisplayColumn.Name]?> */ ?>
</h3>
<? if ( !$page->Ajax ) { ?>
	<p><a href="<?=$page->BackLink?>">Back</a></p>
<? } ?>

<? if ($page->DatabaseUpdated) { ?>
	<p id="success">Item deleted.</p>
<? } else if ( $page->SectionInfo.Value != NULL ) { ?>
	<table>
		<tr>
			<? foreach ( $column in $page->SectionInfo.Table.Columns ) { ?>
				<? if ( $column.Listed ) { ?>
					<th>
						<?=$column.DisplayName?>
					</th>
				<? } ?>
			<? } ?>
		</tr>
		<tr>
			<? foreach ( $column in $page->SectionInfo.Table.Columns ) { ?>
				<? if ( $column.Listed ) { ?>
					<td>
						<?=$page->SectionInfo.Value[$column.Name]?>
					</td>
				<? } ?>
			<? } ?>
		</tr>
	</table>
	<form action="" method="post">
		<input name="confirm" type="submit" value="Confirm Delete" />
	</form>
<? } else { ?>
	<p id="error">Nothing here.</p>
<? } ?>

<? if ( !$page->Ajax ) { ?>
	<p><a href="<?=$page->BackLink?>">Back</a></p>
<? } ?>