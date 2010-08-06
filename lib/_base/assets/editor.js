tinyMCE.init({
   mode                            : 'textareas',
   editor_selector                 : 'wysiwyg',
   theme                           : 'advanced',
   width                           : '100%',
   height                          : '400',
   plugins                         : 'inlinepopups,advimage,paste',
   relative_urls                   : false,
   theme_advanced_toolbar_location : 'top',
   theme_advanced_toolbar_align    : 'left',
   theme_advanced_buttons1         : 'formatselect,|,bold,italic,underline,strikethrough,|,sub,sup,|,bullist,numlist,|,link,unlink,|,image,|,pasteword,selectall,cleanup,charmap,|,code',
   theme_advanced_buttons2         : '',
   theme_advanced_buttons3         : '',
   content_css                     : base+'assets/editor.css',
   apply_source_formatting         : true,
   theme_advanced_blockformats     : 'p',
   valid_elements                  : 'p,br,strong/b,em/i,u,span[style],strike,sub,sup,ul,ol,li,a[href|title],img[src|alt|style],pre'
});
