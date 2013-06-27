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
<form action="admin/novius_ftplite/ftplite/import" method="POST" enctype="multipart/form-data" id="<?= $uniqid = uniqid('id_'); ?>">
<p><?= __('You can either upload a zip file which will be unzipped to the root of the site, either a file which will be put to the root.') ?></p>
<table class="fieldset">
<?php
$contexts = \Nos\User\Permission::contexts();
if (sizeof($contexts) > 1) {
    $locales = \Nos\User\Permission::locales();
    $sites = \Nos\User\Permission::sites();
    ?>
        <tr>
            <th><label><?= sizeof($locales) === 1 ? __('Site:') : (sizeof($sites) === 1 ? __('Language:') : __('Context:')) ?></label></th>
            <td><select required="required" name="context">
    <?php
    foreach ($contexts as $context => $urls) {
        $site = \Nos\Tools_Context::site($context);
        $locale = \Nos\Tools_Context::locale($context);
        echo '<option value="', $context, '">', $site['title'], ' ', $locale['title'], '</option>';
    }
    ?>
            </select></td>
        </tr>
    <?php
}
?>
    <tr>
        <th><label><?= __('File:') ?></label></th>
        <td><input type="file" required="required" name="import" /></td>
    </tr>
</table>
<p>
    <?= strtr(__('<button>Import</button> or <a>No, cancel</a>'), array(
        '<button>' => '<button type="submit">',
        '<a>' => '<a href="#">',
    )) ?>
</p>
</form>
<script type="text/javascript">
    require(
        ['jquery-nos'],
        function ($) {
            $(function() {
                var $container = $('#<?= $uniqid ?>');

                $container.find(':submit').addClass('ui-priority-primary')
                    .data({
                        icon: 'circle-arrow-n'
                    });

                $container.find('a:last').click(function(e) {
                    e.preventDefault();
                    $container.nosDialog('close');
                });

                $container.nosFormUI()
                    .nosFormValidate()
                    .nosFormAjax();
            });
        });
</script>
