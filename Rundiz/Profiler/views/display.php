<?php
if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'functions.php')) {
    include_once __DIR__.DIRECTORY_SEPARATOR.'functions.php';
}

if (class_exists('\\Rundiz\\Number\\Number')) {
    $number = new \Rundiz\Number\Number();
}

// Set document for helper in IDE.
/* @var $this \Rundiz\Profiler\Profiler */
?>

<!-- begins javascript for Rundiz/Profiler -->
<script>
    //<![CDATA[
<?php rdProfilerLoadCss(); ?> 

<?php rdProfilerLoadJs(); ?> 
    //]]>
</script>
<!-- end javascript for Rundiz/Profiler -->

<?php 
ob_start('rdProfilerMinifyOutput', 1);
?>
<div class="rdprofiler" data-nosnippet>
    <div class="rdprofiler-container">
        <ul class="rdprofiler-sections">
            <li><strong><a class="rdprofiler-highlight" href="http://rundiz.com" target="vendor" rel="nofollow">Rundiz</a></strong>\<a href="https://github.com/Rundiz/profiler" target="github" rel="nofollow">Profiler</a></li>
            <?php 
            include 'display-PHP.php';
            if (is_array($this->log_sections)) {
                foreach ($this->log_sections as $section => $data_array) {
                    if (!is_array($data_array)) {
                        break;
                    }

                    $section_to_id = str_replace(['   ', '  ', ' '], '', $section);
                    $section_to_id = strip_tags($section_to_id);

                    switch ($section) {
                        case 'Logs':
                            include 'display-Logs.php';
                            break;
                        case 'Time Load':
                            include 'display-TimeLoad.php';
                            break;
                        case 'Memory Usage':
                            include 'display-MemoryUsage.php';
                            break;
                        case 'Database':
                            include 'display-Database.php';
                            break;
                        case 'Files':
                            include 'display-Files.php';
                            break;
                        case 'Session':
                        case 'Get':
                        case 'Post':
                            include 'display-SessionGetPost.php';
                            break;
                        default:
                            break;
                    }// endswitch;
                    unset($section_to_id);
                }// endforeach $this->log_sections;
                unset($data_array, $section, $summary);
            }
            ?> 
        </ul><!--.rdprofiler-sections-->
    </div><!--.rdprofiler-container-->
</div><!--.rdprofiler-->
<?php
$output = ob_get_clean();
unset($number);
echo $output;