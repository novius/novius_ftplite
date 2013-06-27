<div class="page line ftplite" id="<?= $uniqid = uniqid('id_'); ?>">

    <style type="text/css">
        .ftplite p {
            margin: 0.5em 0 0;
        }
        .ftplite b {
            font-weight: bold;
        }
        .ftplite i {
            font-style: italic;
            display: inline-block;
        }
        .ftplite input.import {
            width: auto;
        }
        .ftplite form table {
            width: auto;
        }
        .ftplite th {
            vertical-align: middle;
        }
        .ftplite ul {
            margin: 0.5em 2em;
        }
    </style>

    <div class="col c8">
        <table class="nos-appdesk ftplite_content"></table>
    </div>
    <div class="col c4">
        <?php
        echo render('nos::form/expander', array(
            'title' => ' Import de fichiers statiques',
            'content' => \View::forge('novius_ftplite::admin/import'),
        ));

        echo render('nos::form/expander', array(
            'title' => 'Aide',
            'content' => \View::forge('novius_ftplite::admin/help'),
        ));
?>
    </div>
</div>
<script type="text/javascript">
require(
    ['jquery-nos', 'jquery-nos-treegrid'],
    function ($) {
        $(function() {
            var $container = $('#<?= $uniqid ?>');

            $container.nosToolbar('add', $.nosUIElement({
                type: 'button',
                label: 'Détruire tous les fichiers statiques',
                red: true,
                icon: 'trash',
                bind: {
                    click: function(e) {
                        if (confirm('Êtes-vous sûr de vouloir détruire tous les fichiers statiques ?')) {
                            $($container).nosAjax({
                                url: 'admin/novius_ftplite/ftplite/delete',
                                success: function() {
                                    $container.find('.ftplite_content').nostreegrid('reload');
                                }
                            });
                        }
                    }
                }
            }), true);

            $container.nosToolbar('add', $.nosUIElement({
                type: 'button',
                label: 'Télécharger le zip du contenu actuel des fichiers statiques',
                icon: 'circle-arrow-s',
                bind: {
                    click: function(e) {
                        window.open('admin/novius_ftplite/ftplite/export');
                    }
                }
            }));

            $container.find('button.import')
                .data({
                    icon: 'circle-arrow-n'
                });
            $container.nosFormUI();

            $container.find('form')
                .nosFormValidate()
                .nosFormAjax()
                .bind('ajax_success', function() {
                    $container.find('.ftplite_content').nostreegrid('reload');
                });

            $container.find('.ftplite_content')
                .nostreegrid({
                    sortable : false,
                    movable : false,
                    initialDepth: 3,
                    columnsAutogenerationMode : 'none',
                    urlJson : 'admin/novius_ftplite/ftplite/files',
                    columns: [
                        {
                            headerText: 'Fichier',
                            dataKey: 'file',
                            cellFormatter: function(args) {
                                if (args.row.data._model == 'file') {
                                    args.$container.wrapInner(
                                        $('<a href="' + args.row.data.url + '" target="_blank"></a>')
                                    );
                                }

                                args.$container.prepend(' <img src="' + args.row.data.icon + '" width="16" style="vertical-align: middle;" /> ');
                            }
                        },
                        {
                            headerText: '',
                            ensurePxWidth: true,
                            width: 19,
                            cellFormatter: function(args) {
                                var container = $('<table><tr></tr></table>').addClass('buttontd wijgridtd');
                                var uiAction = $('<th></th>')
                                    .css('white-space', 'nowrap')
                                    .addClass('ui-state-default ui-state-error')
                                    .attr('title', 'Supprimer')
                                    .html('<span class="ui-icon ui-icon-trash"></span>');

                                uiAction.appendTo(container.find('tr'))
                                    .click(function(e) {
                                        e.stopImmediatePropagation();
                                        e.preventDefault();
                                        if (confirm('Êtes-vous sûr de vouloir supprimer ce ' + (args.row.data._model == 'file' ? 'fichier' : 'répertoire') + ' ?')) {
                                            $(uiAction).nosAjax({
                                                url: 'admin/novius_ftplite/ftplite/delete',
                                                data: {
                                                    file: args.row.data._id
                                                },
                                                success: function() {
                                                    $container.find('.ftplite_content').nostreegrid('reload');
                                                }
                                            });
                                        }
                                    })
                                    .hover(
                                        function() {
                                            $(this).addClass("ui-state-hover");
                                        },
                                        function() {
                                            $(this).removeClass("ui-state-hover");
                                        }
                                    )
                                    .find('span').css('float', 'left');

                                container.appendTo(args.$container);

                                args.$container.parent().addClass('buttontd').css({width: 21});

                                return true;
                            }
                        }
                    ]
                });
        });
    });
</script>
</div>
