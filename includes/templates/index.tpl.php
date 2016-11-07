<div class="container">
    <div class="row">
        <div class="text-center">
            <h3>Code IT test task</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading bold">
                    Find Dress and shoes!
                </div>
                <div class="panel-body">
                <?php if ($values['db_available']): ?>
                    
                    <form action="" method="post">
                        <div class="form-group">
                                <label for="findField">Search:</label>
                                <input type="text" class="form-control" name="find" id="findField" required maxlength="100">
                                <small class="form-text text-muted">Min 3 symbols, max 100. Only first 5 words will be used to search.</small>
                            </div>
                            <div class="form-group">
                                <label for="selectBrand">Select brand:</label>
                                <select class="form-control" name="brand" id="selectBrand">
                                    <?php foreach ($values['brands'] as $brand): ?>
                                        <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="selectType">Select type:</label>
                                <select class="form-control" name="type" id="selectType">
                                    <option selected value="1">Dress</option>
                                    <option value="2">Shoes</option>
                                </select>
                                <label for="selectSize">Select size:</label>
                                <select class="form-control" name="size" id="selectSize">
                                    <?php foreach ($values['dress-sizes'] as $size): ?>
                                        <option value="<?= $size ?>"><?= $size ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        <strong>Warning!</strong> Can't find data in DB. Please <a href='?a=initDb'>init</a> DB first.
                    </div>
                <?php endif; ?>
                </div>
            </div>
            <?php if ($values['error']): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $values['error'] ?>
                </div>
            <?php endif; ?>
            <?php if (isset($values['find']) && $values['find']): ?>
                <div class="alert alert-primary" role="alert">
                    <strong>You searched for:</strong> <?= $values['find'] ?>
                </div>
            <?php endif; ?>
            <div>
                <?php if (!empty($values['find_results'])): ?>
                
                    <div class="list-group main-panel">
                        <?php foreach ($values['find_results'] as $result): ?>
                        <div class="list-group-item selectable-row">
                            &nbsp; <?= $result['type'] ? '<strong>Dress:</strong> ' : '<strong>Shoes:</strong> ' ?><?= $result['name'] ?> 
                            <strong>Size:</strong> <?= $result['size'] ?> 
                            <strong>Brand:</strong> 
                                <?php 
                                foreach ($values['brands'] as $brand) {
                                    if ($result['brand'] == $brand['id']) {
                                        echo $brand['name'];
                                        break;
                                    }
                                }
                                ?> 
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


