<?
	$page->Title = 'List';
	$page->BodyId = 'list';
	
	$page->SetWrapperFile('_wrapper.view.php');
?>


<? if ( $page->SectionInfo != NULL ) { ?>
	<? if ( !$page->Ajax ) { ?>
	<h3>
		View
		[ <?=( $page->SectionInfo->ParentSection != NULL ) ? $page->SectionInfo->ParentSection->Table->DisplayColumn->DisplayName . " : " . $page->SectionInfo->ParentSection->Value[$page->SectionInfo->ParentSection->Table->DisplayColumn->Name] . " - " : ""?>
		<?=$page->SectionInfo->DisplayName?> ]
		Items
	</h3>
	<dl>
		<dt>Sort By:</dt>
		<dd>
			<? if ( !$page->QueryOrderBy ) { ?>
				None
			<? } else { ?>
				<?=$page->QueryOrderBy?>, <?=$page->QueryAscDesc?>
			<? } ?>
		</dd>
	</dl>
	<p>
		<? if ( $page->RemoveSortLink ) { ?>
			<a href="<?=$page->RemoveSortLink?>">Remove Sorting</a>
		<? } ?>
	</p>
	<p>
		<a href="<?=$page->AddLink?>">Add</a>
	</p>
	<p>
		<a href="<?=$page->BackLink?>">Back</a>
	</p>
	<? } ?>            
	<table>
		<thead>
			<tr>
				<? foreach( $page->SectionInfo->Table->Columns as $column ) { ?>
					<? if ( $column->Listed ) { ?>
						<th><a href="<?=@$page->OrderByLinks[$column]?>"><?=$column->DisplayName?></a></th>
					<? } ?>
				<? } ?>
				<? if ( $page->SectionInfo->SubSection != NULL ) { ?>
					<th></th>
				<? } ?>
				<th>
					Edit
				</th>
				<th>
					Delete
				</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ( $page->SectionInfo->Values as $key => $item ) { ?>
				<tr>
					<? foreach( $page->SectionInfo->Table->Columns as $column ) { ?>
						<? if ( $column->Listed ) { ?>
							<td>
								<? if ( $column->Boolean ) { ?>
									<a href="<?=$page->SingleEditLink?><?=$item[$page->SectionInfo->Table->PkColumn->Name]?>&name=<?=$column->SimpleName?>&value=<?=($item[$column->Name])?"0":"1"?>"><?=($item[$column->Name])?'True':'False'?></a>
								<? } else { ?>
									<? if ( strlen($item[$column->Name]) > 100 ) { ?>
										<?=substr($item[$column->Name], 0, 97)?>...
									<? } else { ?>
										<?=$item[$column->Name]?>
									<? } ?>
								<? } ?>
								<? if ( $column->NumericUpDown ) { ?>
									<a href="<?=$page->SingleEditLink?><?=$item[$page->SectionInfo->Table->PkColumn->Name]?>&name=<?=$column->SimpleName?>&value=<?=$item[$column->Name]+1?>">+1</a>
									<a href="<?=$page->SingleEditLink?><?=$item[$page->SectionInfo->Table->PkColumn->Name]?>&name=<?=$column->SimpleName?>&value=<?=$item[$column->Name]-1?>">-1</a>
								<? } ?>
							</td>
						<? } ?>
					<? } ?>
					<? if ( $page->SectionInfo->SubSection != NULL ) { ?>
						<td>
							<a href="<?=$page->SubSectionLink?>&pk=<?=$item[$page->SectionInfo->Table->PkColumn->Name]?>&subsection=<?=$page->SectionInfo->SubSection->Name?>"><?=$page->SectionInfo->SubSection->DisplayName?></a>
						</td>
					<? } ?>
					<td>
						<a href="<?=$page->EditLinks[$key]?>">Edit</a>
					</td>
					<td>
						<a href="<?=$page->DeleteLinks[$key]?>">Delete</a>
					</td>
				</tr>
			<? } ?>
		</tbody>
	</table>
	<? if ( !$page->Ajax ) { ?>
	<p>
		<a href="<?=$page->AddLink?>">Add</a>
	</p>
	<p>
		<a href="<?=$page->BackLink?>">Back</a>
	</p>
	<? } ?>
<? } else { ?>
	<p id="gcms_error">Nothing here.</p>
	<p><a href="/lade">Back</a></p>
<? } ?>
