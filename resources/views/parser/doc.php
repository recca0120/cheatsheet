<h2>
    <?php echo $name; ?>
</h2>
<?php if (count($constants) > 0): ?>
    <h3>constants</h3>
    <ul>
    <?php foreach ($constants as $constant): ?>
        <li>
            <?php echo $constant->render(); ?>
        </li>
    <?php endforeach ?>
    </ul>
<?php endif ?>

<?php if (count($properties) > 0): ?>
    <h3>properties</h3>
    <ul>
        <?php foreach ($properties as $property): ?>
            <li>
                <?php echo $property->render(); ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<?php if (count($methods) > 0): ?>
    <h3>methods</h3>
    <ul>
        <?php foreach ($methods as $method): ?>
            <li>
                <?php echo $method->render(); ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
