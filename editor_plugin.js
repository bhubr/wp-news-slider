function wpnsw_mceplugin() {
    return "<!-- cuthere -->";
}

(function() {

    tinymce.create('tinymce.plugins.wpnsw_mceplugin', {

        init : function(ed, url){
            ed.addButton('wpnsw_mceplugin', {
                title : 'Insérer la balise "Cut Here"',
                onclick : function() {
                    ed.execCommand(
                        'mceInsertContent',
                        false,
                        wpnsw_mceplugin()
                        );
                },
                image: url + "/cut.png"
            });
        },

        getInfo : function() {
            return {
                longname : 'Contnet Mage plugin',
                author : 'Grzegorz Winiarski',
                authorurl : 'http://ditio.net',
                infourl : '',
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add('wpnsw_mceplugin', tinymce.plugins.wpnsw_mceplugin);
    
})();
