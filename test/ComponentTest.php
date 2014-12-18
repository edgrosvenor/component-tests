<?php

namespace ComponentTests;

abstract class ComponentTest extends \PHPUnit_Framework_TestCase {

  /**
   * Get a list of paths to search over, e.g. __DIR__.
   */
  abstract function getRoots();

  /**
   * May be extended by child classes to define a list of path
   * names that will be excluded by {@link #iterateOver()}.
   */
  function getExcludes() {
    return array("/vendor/");
  }

  function iterateOver($root, $extension, $callback) {
    if ($handle = opendir($root)) {
      while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
          // do we exclude this path?
          if ($this->shouldExclude($root . "/" . $entry)) {
            continue;
          }

          if (is_dir($root . "/" . $entry)) {
            $this->iterateOver($root . "/" . $entry, $extension, $callback);
          } else if (substr(strtolower($entry), -strlen($extension)) == strtolower($extension)) {
            $callback($root . "/" . $entry);
          }
        }
      }
      closedir($handle);
    }
  }

  /**
   * Should the given (absolute or relative) path be excluded based
   * on {@link #getExcludes()}?
   */
  function shouldExclude($path) {
    foreach ($this->getExcludes() as $pattern) {
      if (strpos($path, $pattern) !== false) {
        return true;
      }
    }
    return false;
  }

  /**
   * Test that all JSON files are valid.
   */
  function testJSONLint() {
    foreach ($this->getRoots() as $root) {
      $this->iterateOver($root, ".json", function($filename) {
        $this->assertNotNull(json_decode(file_get_contents($filename)), "File '$filename' was not valid JSON");
      });
    }
  }

  /**
   * @return true if {@link #testPHPLint()} error should be printed to the console
   */
  function printPHPLintErrors() {
    return true;
  }

  /**
   * Test that all PHP files are valid.
   */
  function testPHPLint() {
    foreach ($this->getRoots() as $root) {
      $this->iterateOver($root, ".php", function($filename) {
        $return = 0;
        $output_array = array();
        $output = exec("php -l " . escapeshellarg($filename) . " 2>&1", $output_array, $return);
        if ($return !== 0 && $this->printPHPLintErrors()) {
          echo "[$filename]\n" . implode("\n", $output_array);
        }
        $this->assertFalse(!!$return, "File '$filename' failed lint: '$output' ($return)");
      });
    }
  }

}