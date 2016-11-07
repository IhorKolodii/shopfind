<!DOCTYPE html>
<html>
    <head>
        <title><?= $this->title ?></title>
        <?php foreach (Config::valS('js') as $js): ?>
            <?php $jsLink = Config::valS('js-path') . $js . '.js' ?>
            <script src="<?= $jsLink ?>"></script>
        <?php endforeach; ?>
        <?php foreach (Config::valS('css') as $css): ?>
            <?php $cssLink = Config::valS('css-path') . $css . '.css' ?>
            <link rel="stylesheet" type="text/css" href="<?= $cssLink ?>">
        <?php endforeach; ?>
    </head>
    <body>
        <nav class="navbar-inverse navbar-static-top navbar" role="navigation">
            <div class="container">
                <a class="navbar-brand" href="?a=index">CodeIT test task</a>
                <ul class="nav navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="?a=index">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?a=initDb">Init DB</a>
                    </li>
                </ul>
            </div>
        </nav>
        <?= $actionOutput ?>
    </body>
</html>