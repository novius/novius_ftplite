<form action="admin/novius_ftplite/ftplite/import" method="POST" enctype="multipart/form-data">
<p>Vous pouvez uploader soit un fichier zip contenant des fichiers statiques à décompresser à la racine, soit un fichier seul à mettre à la racine</p>
<table>
<?php
$contexts = \Nos\User\Permission::contexts();
if (sizeof($contexts) > 1) {
    $locales = \Nos\User\Permission::locales();
    $sites = \Nos\User\Permission::sites();
    ?>
        <tr>
            <th><label><?= sizeof($locales) === 1 ? 'Site' : (sizeof($sites) === 1 ? 'Langues' : 'Contexte') ?>&nbsp;:</label></th>
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
        <th><label>Fichier&nbsp;:</label></th>
        <td><input type="file" required="required" name="import" /></td>
    </tr>
    <tr>
        <th></th>
        <td><button type="submit" class="import">Importer</button></td>
    </tr>
</table>
</form>
