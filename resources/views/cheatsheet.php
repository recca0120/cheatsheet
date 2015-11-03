<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">

        <title>CheatSheet</title>

        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            body {
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2 col-md-3">
                    <ul class="nav nav-pills nav-stacked" role="tablist" style="padding-top: 45px">
                        <?php $i = 0; ?>
                        <?php foreach ($namespaces as $namespace): ?>
                            <li role="presentation" class="<?php echo ($i === 0) ? 'active' : ''; ?>">
                                <a href="#<?php echo strtolower(str_replace('\\', '-', $namespace)) ?>" role="tab" data-toggle="tab">
                                    <?php echo $namespace ?>
                                </a>
                            </li>
                            <?php $i++; ?>
                        <?php endforeach ?>
                    </ul>
                </div>
                <div class="col-lg-10 col-md-9">
                    <div class="tab-content">
                        <?php $i = 0; ?>
                        <?php foreach ($classes as $namespace => $items): ?>
                            <div role="tabpanel" class="tab-pane <?php echo ($i === 0) ? 'active' : ''; ?>" id="<?php echo strtolower(str_replace('\\', '-', $namespace)) ?>">
                                <div class="page-header">
                                    <h2><?php echo $namespace ?></h2>
                                </div>
                                <?php foreach ($items as $class): ?>
                                    <?php echo \Recca0120\Cheatsheet\Parser\Doc::factory($class)->render(); ?>
                                <?php endforeach ?>
                            </div>
                            <?php $i++; ?>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
        <ul>

        </ul>
    </body>
</html>
