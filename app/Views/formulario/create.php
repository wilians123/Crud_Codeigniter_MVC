<!DOCTYPE html>
<html>

<head>
    <title><?= esc($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"
        rel="stylesheet">
</head>

<body>
    <?= view('layouts/navbar') ?>
    <div class="container">
        <h4><?= esc($title) ?></h4>
        <form method="post" action="<?= site_url($actionUrl) ?>">
            <?php foreach ($fields as $field): ?>
                <div class="input-field">
                    <input
                        type="<?= $field['type'] ?>"
                        id="<?= $field['name'] ?>"
                        name="<?= $field['name'] ?>"
                        required>
                    <label for="<?= $field['name'] ?>"><?= $field['label'] ?></label>
                </div>
            <?php endforeach; ?>
            <button class="btn waves-effect" type="submit">Guardar</button>
            <a class="btn-flat" href="<?= site_url($backUrl) ?>">⬅️ Volver</a>
        </form>
    </div>

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.updateTextFields();
        });
    </script>
</body>

</html>