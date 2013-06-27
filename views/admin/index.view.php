<?php
/**
 * FTP Lite is an application for Novius OS for managing static files
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_ftplite
 */
?>
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
            var $container = $('#<?= $uniqid ?>').nosFormUI(),
                $ftplite_content = $container.find('.ftplite_content'),
                dispatcher = $container.closest('.nos-dispatcher')
                    .nosListenEvent({name: 'ftplite'}, function() {
                        $ftplite_content.nostreegrid('reload');
                    });

            $container.nosToolbar('add', $.nosUIElement({
                type: 'button',
                label: <?= \Format::forge(__('Import'))->to_json() ?>,
                icon: 'circle-arrow-n',
                action: {
                    action: 'confirmationDialog',
                    dialog: {
                        ajax: true,
                        contentUrl: 'admin/novius_ftplite/ftplite/import',
                        title: <?= \Format::forge(__('Import static files'))->to_json() ?>
                    }
                }
            }).addClass('primary'));
            $container.nosToolbar('add', $.nosUIElement({
                type: 'link',
                label: <?= \Format::forge(__('Download zip of the current contents'))->to_json() ?>,
                icon: 'circle-arrow-s',
                action: {
                    action: 'window.open',
                    url: 'admin/novius_ftplite/ftplite/export'
                }
            }));
            $container.nosToolbar('add', $.nosUIElement({
                type: 'button',
                label: <?= \Format::forge(__('Remove all files'))->to_json() ?>,
                red: true,
                icon: 'trash',
                action : {
                    action: 'confirmationDialog',
                    dialog: {
                        contentUrl: 'admin/novius_ftplite/ftplite/delete',
                        title: <?= \Format::forge(__('Remove all files'))->to_json() ?>
                    }
                }
            }), true);

            $ftplite_content.nostreegrid({
                sortable : false,
                movable : false,
                initialDepth: 3,
                columnsAutogenerationMode : 'none',
                urlJson : 'admin/novius_ftplite/ftplite/files',
                columns: [
                    {
                        headerText: <?= \Format::forge(__('File'))->to_json() ?>,
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
                                .attr('title', <?= \Format::forge(__('Delete'))->to_json() ?>)
                                .html('<span class="ui-icon ui-icon-trash"></span>');

                            uiAction.appendTo(container.find('tr'))
                                .click(function(e) {
                                    e.stopImmediatePropagation();
                                    e.preventDefault();
                                    uiAction.nosAction({
                                        action: 'confirmationDialog',
                                        dialog: {
                                            contentUrl: 'admin/novius_ftplite/ftplite/delete',
                                            ajaxData: {
                                                file: args.row.data._id
                                            },
                                            title: args.row.data._model == 'file' ? <?= \Format::forge(__('Delete file ‘{{file}}’'))->to_json() ?> : <?= \Format::forge(__('Delete directory ‘{{file}}’'))->to_json() ?>
                                        }
                                    }, args.row.data);
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
