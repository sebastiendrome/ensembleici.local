/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.toolbar = 'Auto';
				 
	CKEDITOR.config.toolbar_Auto =
	[
		{ name: 'clipboard',	items : [ 'Cut','Copy','Paste','-','Undo','Redo' ] },
		{ name: 'editing',		items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'basicstyles',	items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript' ] },
		{ name: 'paragraph',	items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
		{ name: 'links',		items : [ 'Link','Unlink'] },
		{ name: 'colors',		items : [ 'TextColor'] },
		{ name: 'insert',		items : [ 'Image','Smiley'] },
		{ name: 'count',		items : [ 'CharCount'] },
		
	];

	CKEDITOR.config.toolbar_AutoA =
	[
		{ name: 'clipboard',	items : [ 'Cut','Copy','Paste','-','Undo','Redo' ] },
		{ name: 'editing',		items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'basicstyles',	items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript' ] },
		{ name: 'paragraph',	items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
		{ name: 'links',		items : [ 'Link','Unlink'] },
		{ name: 'colors',		items : [ 'TextColor'] },
		{ name: 'insert',		items : [ 'Image','Smiley'] },
		{ name: 'count',		items : [ 'CharCount'] },
		{ name: 'sourcea',		items : [ 'Source'] }
	];
	
	CKEDITOR.config.toolbar_NewsLetter =
	[
		{ name: 'clipboard',	items : [ 'Cut','Copy','Paste','-','Undo','Redo' ] },
		{ name: 'editing',		items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'basicstyles',	items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript' ] },
		{ name: 'paragraph',	items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
		{ name: 'links',		items : [ 'Link','Unlink'] },
		{ name: 'colors',		items : [ 'TextColor'] },
		{ name: 'insert',		items : [ 'Image'] },
		{ name: 'count',		items : [ 'CharCount'] },
		{ name: 'sourcea',		items : [ 'Source'] }
	];

	config.language = 'fr';
	// config.uiColor = '#AADC6E';
};
