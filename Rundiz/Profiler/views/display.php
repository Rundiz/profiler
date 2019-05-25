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
<script type="text/javascript">
    //<![CDATA[
<?php rdProfilerLoadCss(); ?> 

<?php 
echo 'if (typeof jQuery == \'undefined\') {'."\n";
rdProfilerLoadJs('jquery');
echo '}'."\n\n";
rdProfilerLoadJs(); 
?> 
    //]]>
</script>
<!-- end javascript for Rundiz/Profiler -->

<div class="rdprofiler">
    <div class="rdprofiler-container">
        <ul class="rdprofiler-log-sections">
            <li><strong><a href="http://rundiz.com" target="vendor" class="rdprofiler-highlight">Rundiz</a></strong>\Profiler</li>
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
        </ul><!--.rdprofiler-log-sections-->
    </div><!--.rdprofiler-container-->
</div><!--.rdprofiler-->
<?php
unset($number);