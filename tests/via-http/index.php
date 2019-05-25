<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rundiz\Profiler test</title>
        
        <link rel="stylesheet" href="reset.css">
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <h1>Rundiz\Profiler test</h1>
        <ul>
            <li><a href="test-basic.php">Basic Test</a>: Log, Time Load, Memory usage, Files</li>
            <li><a href="test-input.php">Input Test</a>: Log, Memory usage, Session, Method get data, Method post data</li>
            <li>
                <a href="test-full.php">Full Test</a>: Log, Time Load, Memory usage, Database, Files, Session, Method get data, Method post data<br>
                <small class="help">Please open this file at <?php echo realpath('test-full.php'); ?> and follow instruction in the file before go to &quot;Full Test&quot;.</small>
            </li>
        </ul>

        <h2 style="margin-top: 40px;">Test with CSS frameworks</h2>
        <p>Based on basic test but different styles.</p>
        <ul>
            <li><a href="styles/basic-bootstrap3.php">Bootstrap 3</a></li>
            <li><a href="styles/basic-bootstrap4.php">Bootstrap 4</a></li>
            <li><a href="styles/basic-foundation5.php">Foundation 5</a></li>
            <li><a href="styles/basic-foundation6.php">Foundation 6</a></li>
            <li><a href="styles/basic-skeleton1.php">Skeleton 1</a></li>
            <li><a href="styles/basic-bulma0.php">Bulma 0</a></li>
            <li><a href="styles/basic-pure1.php">Pure 1</a></li>
        </ul>
    </body>
</html>