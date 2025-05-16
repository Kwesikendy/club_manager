<?php
$test_file = __DIR__ . '/uploads/test.txt';
file_put_contents($test_file, 'Test content');
echo file_exists($test_file) ? 'Success!' : 'Failed';
?>  