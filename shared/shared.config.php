<?php

M::Set('upload_directory', M::Get('monkake_directory') . 'doc-root/upload/');

M::Set('ladedgm_tables',
array(
	array(
		'Name' => 'ladedgm_section',
		'SimpleName' => 'section',
		'DisplayName' => 'Wordlet Sections',
		'DefaultOrderByColumn' => 'sortorder',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'value',
		'SimpleColumn' => 'name',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'name',
				'SimpleName' => 'name',
				'DisplayName' => 'Name',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'AllowGet' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'value',
				'SimpleName' => 'value',
				'DisplayName' => 'Value',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'sortorder',
				'SimpleName' => 'sortorder',
				'DisplayName' => 'Order',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'NumericUpDown' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValueQuery' => 'select Max(sortorder)+1 from lade_section',
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
	array(
		'Name' => 'ladedgm_wordlet',
		'SimpleName' => 'wordlet',
		'DisplayName' => 'Wordlets',
		'DefaultOrderByColumn' => 'name',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'name',
		'SimpleColumn' => 'name',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'section_id',
				'SimpleName' => 'section_id',
				'DisplayName' => 'Section',
				'DefaultAscDesc' => 'asc',
				//'Listed' => false,
				//'Editable' => false,
				//'Addable' => true,
				'FkTable' => 'ladedgm_section',
				'UseParentPk' => true
			),
			array(
				'Name' => 'name',
				'SimpleName' => 'name',
				'DisplayName' => 'Name',
				'Listed' => true,
				'Editable' => false,
				'Addable' => true,
				'AllowGet' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'value',
				'SimpleName' => 'value',
				'DisplayName' => 'Value',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'InputType' => 'textarea',
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
	array(
		'Name' => 'ladedgm_asection',
		'SimpleName' => 'asection',
		'DisplayName' => 'Admin Sections',
		'DefaultOrderByColumn' => 'sortorder',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'dname',
		'SimpleColumn' => 'name',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'name',
				'SimpleName' => 'name',
				'DisplayName' => 'Name',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'dname',
				'SimpleName' => 'dname',
				'DisplayName' => 'Display Name',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'sortorder',
				'SimpleName' => 'sortorder',
				'DisplayName' => 'Order',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'NumericUpDown' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValueQuery' => 'select Max(sortorder)+1 from lade_asection',
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
	array(
		'Name' => 'ladedgm_grp',
		'SimpleName' => 'grp',
		'DisplayName' => 'Groups',
		'DefaultOrderByColumn' => 'name',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'dname',
		'SimpleColumn' => 'name',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'name',
				'SimpleName' => 'name',
				'DisplayName' => 'Name',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'dname',
				'SimpleName' => 'dname',
				'DisplayName' => 'Display Name',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
	array(
		'Name' => 'ladedgm_usr',
		'SimpleName' => 'user',
		'DisplayName' => 'Users',
		'DefaultOrderByColumn' => 'email',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'email',
		'SimpleColumn' => 'id',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'email',
				'SimpleName' => 'email',
				'DisplayName' => 'Email',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'pass',
				'SimpleName' => 'pass',
				'DisplayName' => 'Password',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'grp_id',
				'SimpleName' => 's_grp',
				'DisplayName' => 'Group',
				'FkColumn' => 'id',
				'FkTable' => 'ladedgm_grp',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'IsNull' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
	array(
		'Name' => 'ladedgm_rght',
		'SimpleName' => 'rights',
		'DisplayName' => 'Rights',
		'DefaultOrderByColumn' => 'id',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'asection_id',
		'SimpleColumn' => 'id',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'asection_id',
				'SimpleName' => 'section',
				'DisplayName' => 'Section',
				'FkColumn' => 'id',
				'FkTable' => 'ladedgm_asection',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
			),
			array(
				'Name' => 'grp_id',
				'SimpleName' => 'grp',
				'DisplayName' => 'Group',
				'FkColumn' => 'id',
				'FkTable' => 'ladedgm_grp',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'IsNull' => true,
			),
			array(
				'Name' => 'radd',
				'SimpleName' => 'add',
				'DisplayName' => 'Add',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultValue' => 'True',
			),
			array(
				'Name' => 'redit',
				'SimpleName' => 'edit',
				'DisplayName' => 'Edit',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultValue' => 'True',
			),
			array(
				'Name' => 'rlist',
				'SimpleName' => 'list',
				'DisplayName' => 'List',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultValue' => 'True',
			),
			array(
				'Name' => 'rdelete',
				'SimpleName' => 'delete',
				'DisplayName' => 'Delete',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultValue' => 'True',
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
	array(
		'Name' => 'ladedgm_news',
		'SimpleName' => 'news',
		'DisplayName' => 'News',
		'DefaultOrderByColumn' => 'id',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'title',
		'SimpleColumn' => 'id',
		'RevisionTable' => 'ladedgm_newsrev',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
				'RevisionColumn' => 'news_id',
			),
			array(
				'Name' => 'usr_id',
				'SimpleName' => 'usr',
				'DisplayName' => 'User',
				'FkColumn' => 'id',
				'FkTable' => 'ladedgm_usr',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'RevisionColumn' => 'usr_id',
			),
			array(
				'Name' => 'title',
				'SimpleName' => 'title',
				'DisplayName' => 'Title',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'RevisionColumn' => 'title',
			),
			array(
				'Name' => 'body',
				'SimpleName' => 'body',
				'DisplayName' => 'Body',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'textarea',
				'RevisionColumn' => 'body',
			),
			array(
				'Name' => 'posted',
				'SimpleName' => 'posted',
				'DisplayName' => 'Posted',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValueQuery' => 'select NOW()',
				'RevisionColumn' => 'posted',
				'ValidationType' => 'datetime',
			),
			array(
				'Name' => 'homepage',
				'SimpleName' => 'homepage',
				'DisplayName' => 'Show on Homepage',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
			array(
				'Name' => 'plaintext',
				'SimpleName' => 'plaintext',
				'DisplayName' => 'Plain Text',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
	array(
		'Name' => 'ladedgm_event',
		'SimpleName' => 'event',
		'DisplayName' => 'Events',
		'DefaultOrderByColumn' => 'scheduled',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'title',
		'SimpleColumn' => 'id',
		//'RevisionTable' => 'ladedgm_newsrev',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
				//'RevisionColumn' => 'news_id',
			),
			array(
				'Name' => 'usr_id',
				'SimpleName' => 'usr',
				'DisplayName' => 'User',
				'FkColumn' => 'id',
				'FkTable' => 'ladedgm_usr',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				//'RevisionColumn' => 'usr_id',
			),
			array(
				'Name' => 'title',
				'SimpleName' => 'title',
				'DisplayName' => 'Title',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				//'RevisionColumn' => 'title',
			),
			array(
				'Name' => 'location',
				'SimpleName' => 'location',
				'DisplayName' => 'Location',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				//'RevisionColumn' => 'title',
			),
			array(
				'Name' => 'directions',
				'SimpleName' => 'directions',
				'DisplayName' => 'Directions',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'FormNote' => 'Go to <a href="http://maps.google.com" target="_blank">Google Maps</a>,<br />
								look up address,<br />
								click the "Get Directions" link (top left),<br />
								click the "Get Directions" button,<br />
								click the "Link" link (top right) and copy the link in the top box.',
				//'RevisionColumn' => 'title',
			),
			array(
				'Name' => 'link',
				'SimpleName' => 'link',
				'DisplayName' => 'Default Link',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'FormNote' => '<strong>Waring:</strong> links must start with "http://" if linking to another site<br/>
								(i.e. "www.discgolf.com" will not work, instead use "http://www.discgolf.com"',
			),
			array(
				'Name' => 'flyerimg',
				'SimpleName' => 'flyerimg',
				'DisplayName' => 'Flyer Image Link',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'image',
				'UploadDirectory' => M::Get('upload_directory') . 'flyers/',
				'UploadVirtualDirectory' => '/upload/flyers/',
				'UploadFilename' => '{name}_{datetime}.{ext}',
				'UploadImageDefaultWidthMax' => 800,
			),
			array(
				'Name' => 'flyerpdf',
				'SimpleName' => 'flyerpdf',
				'DisplayName' => 'Flyer PDF Link',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'upload',
				'UploadDirectory' => M::Get('upload_directory') . 'flyers/',
				'UploadVirtualDirectory' => '/upload/flyers/',
				'UploadFilename' => '{name}_{datetime}.{ext}',
			),
			array(
				'Name' => 'signupenabled',
				'SimpleName' => 'signupenabled',
				'DisplayName' => 'Registration Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
			array(
				'Name' => 'signup',
				'SimpleName' => 'signup',
				'DisplayName' => 'Registration Link',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'upload',
				'UploadDirectory' => M::Get('upload_directory') . 'signup/',
				'UploadVirtualDirectory' => '/upload/signup/',
				'UploadFilename' => '{name}_{datetime}.{ext}',
				'FormNote' => 'Must start with http://. Leave blank for default signup link',
			),
			array(
				'Name' => 'results',
				'SimpleName' => 'results',
				'DisplayName' => 'Results Link',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'upload',
				'UploadDirectory' => M::Get('upload_directory') . 'results/',
				'UploadVirtualDirectory' => '/upload/results/',
				'UploadFilename' => '{name}_{datetime}.{ext}',
			),
			array(
				'Name' => 'scheduled',
				'SimpleName' => 'scheduled',
				'DisplayName' => 'Scheduled Date',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValueQuery' => 'select NOW()',
				'ValidationType' => 'date',
				//'RevisionColumn' => 'posted',
			),
			array(
				'Name' => 'scheduleend',
				'SimpleName' => 'scheduleend',
				'DisplayName' => 'Scheduled End Date',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				//'DefaultValueQuery' => 'select NOW()',
				'ValidationType' => 'date',
				//'RevisionColumn' => 'posted',
				'IsNull' => true,
			),
			array(
				'Name' => 'month',
				'SimpleName' => 'month',
				'DisplayName' => 'Only Display Month?',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '0',
			),
			array(
				'Name' => 'ispdga',
				'SimpleName' => 'ispdga',
				'DisplayName' => 'Is PDGA Event',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
			array(
				'Name' => 'pricepro',
				'SimpleName' => 'pricepro',
				'DisplayName' => 'Price, Pro',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'FormNote' => 'Example: 45.00',
			),
			array(
				'Name' => 'priceadv',
				'SimpleName' => 'priceadv',
				'DisplayName' => 'Price, Advanced',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'FormNote' => 'Example: 40.00',
			),
			array(
				'Name' => 'pricerec',
				'SimpleName' => 'pricerec',
				'DisplayName' => 'Price, Intermediate/Rec',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'FormNote' => 'Example: 35.00',
			),
			array(
				'Name' => 'pricejr',
				'SimpleName' => 'pricejr',
				'DisplayName' => 'Price, Jr.',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'FormNote' => 'Example: 30.00',
			),
			array(
				'Name' => 'discs',
				'SimpleName' => 'discs',
				'DisplayName' => 'Discs',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'textarea',
				'FormNote' => 'Put each disc selection on a new line',
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
	array(
		'Name' => 'ladedgm_gimage',
		'SimpleName' => 'gimage',
		'DisplayName' => 'Gallery Images',
		'DefaultOrderByColumn' => 'created',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'title',
		'SimpleColumn' => 'id',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'created',
				'SimpleName' => 'created',
				'DisplayName' => 'Created',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValueQuery' => 'select NOW()',
				'ValidationType' => 'datetime',
			),
			array(
				'Name' => 'title',
				'SimpleName' => 'title',
				'DisplayName' => 'Title',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'imgpath',
				'SimpleName' => 'imgpath',
				'DisplayName' => 'Fullsize Image Link',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'image',
				'UploadDirectory' => M::Get('upload_directory') . 'gallery/',
				'UploadVirtualDirectory' => '/upload/gallery/',
				'UploadFilename' => '{name}_{datetime}.{ext}',
				'UploadImageDefaultWidthMax' => 800,
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
	array(
		'Name' => 'ladedgm_storeitem',
		'SimpleName' => 'store_item',
		'DisplayName' => 'Store Items',
		'DefaultOrderByColumn' => 'created',
		'PrimaryKeyColumn' => 'id',
		'DisplayColumn' => 'title',
		'SimpleColumn' => 'id',
		'Columns' => array(
			array(
				'Name' => 'id',
				'SimpleName' => 'id',
				'DisplayName' => 'ID',
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'created',
				'SimpleName' => 'created',
				'DisplayName' => 'Created',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValueQuery' => 'select NOW()',
				'ValidationType' => 'datetime',
			),
			array(
				'Name' => 'title',
				'SimpleName' => 'title',
				'DisplayName' => 'Title',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
			),
			array(
				'Name' => 'description',
				'SimpleName' => 'description',
				'DisplayName' => 'Description',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'textarea',
			),
			array(
				'Name' => 'imgpath',
				'SimpleName' => 'imgpath',
				'DisplayName' => 'Fullsize Image Link',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'image',
				'UploadDirectory' => M::Get('upload_directory') . 'store/',
				'UploadVirtualDirectory' => '/upload/store/',
				'UploadFilename' => '{name}_{datetime}.{ext}',
				'UploadImageDefaultWidthMax' => 800,
			),
			/*array(
				'Name' => 'paypal',
				'SimpleName' => 'paypal',
				'DisplayName' => 'PayPal Button Code',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'DefaultAscDesc' => 'asc',
				'InputType' => 'textarea',
			),*/
			array(
				'Name' => 'price',
				'SimpleName' => 'price',
				'DisplayName' => 'Price',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'FormNote' => 'Example: 45.00',
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '',
			),
			array(
				'Name' => 'shipping',
				'SimpleName' => 'shipping',
				'DisplayName' => 'Shipping (for the first unit of this item added)',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'FormNote' => 'Example: 2.00',
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '0.00',
			),
			array(
				'Name' => 'shipping2',
				'SimpleName' => 'shipping2',
				'DisplayName' => 'Shipping2 (for each additional unit of this item added)',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'FormNote' => 'Example: 0.50',
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '0.00',
			),
			array(
				'Name' => 'enabled',
				'SimpleName' => 'enabled',
				'DisplayName' => 'Enabled',
				'Listed' => true,
				'Editable' => true,
				'Addable' => true,
				'Boolean' => true,
				'DefaultAscDesc' => 'asc',
				'DefaultValue' => '1',
			),
		)
	),
));

M::Set('ladedgm_sections', array(
	array(
		'Name' => 'grpright',
		'DisplayName' => 'Rights',
		'Table' => 'ladedgm_rght',
	),
	array(
		'Name' => 'group',
		'DisplayName' => 'Groups',
		'Table' => 'ladedgm_grp',
		'SubSection' => 'grpright',
	),
	array(
		'Name' => 'user',
		'DisplayName' => 'Users',
		'Table' => 'ladedgm_usr',
	),
	array(
		'Name' => 'wordlets',
		'DisplayName' => 'Wordlets',
		'Table' => 'ladedgm_wordlet',
	),
	array(
		'Name' => 'section',
		'DisplayName' => 'Wordlet Sections',
		'Table' => 'ladedgm_section',
		'SubSection' => 'wordlets',
	),
	array(
		'Name' => 'a_section',
		'DisplayName' => 'Admin Sections',
		'Table' => 'ladedgm_asection',
	),
	array(
		'Name' => 'news',
		'DisplayName' => 'News',
		'Table' => 'ladedgm_news',
		'SubSection' => '',
	),
	array(
		'Name' => 'event',
		'DisplayName' => 'Events',
		'Table' => 'ladedgm_event',
	),
	array(
		'Name' => 'gimage',
		'DisplayName' => 'Gallery Images',
		'Table' => 'ladedgm_gimage',
	),
	array(
		'Name' => 'store_item',
		'DisplayName' => 'Store Items',
		'Table' => 'ladedgm_storeitem',
	),
));