<div class="container">
    <div class="row">
        <div class="text-center">
            <h3>Test DB initialization</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading bold">
                    DB status
                </div>
                <div class="panel-body">
                    <p>Item table: <?php echo $values['db_status']['item_table_exists'] ? "Exists" : "Not Exists" ?>
                        <?php if ($values['db_status']['item_table_exists']): ?>
                            <br>Entries in item table: <?php echo $values['db_status']['item_table_count'] ?>
                        <?php endif; ?>
                    </p>
                    
                    <p>Brand table: <?php echo $values['db_status']['brand_table_exists'] ? "Exists" : "Not Exists" ?>
                        <?php if ($values['db_status']['item_table_exists']): ?>
                            <br>Entries in brands table: <?php echo $values['db_status']['brand_table_count'] ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <p><div class="input-group">
                <a class="btn btn-success" href="?a=createTables" <?php echo ($values['db_status']['item_table_exists'] && $values['db_status']['brand_table_exists']) ? "disabled" : "" ?>>Create test tables</a>
                <a class="btn btn-danger" href="?a=dropTables" <?php echo (!$values['db_status']['item_table_exists'] && !$values['db_status']['brand_table_exists']) ? "disabled" : "" ?>>Drop test tables</a>
            </div></p>
            <p><div class="input-group">
                <a class="btn btn-primary" href="?a=addTestData" <?php echo (!$values['db_status']['item_table_exists'] || !$values['db_status']['brand_table_exists']) ? "disabled" : "" ?>>Fill tables with test data</a>
            </div></p>
        </div>
    </div>
</div>


