<?php

namespace ComponentTests;

class FailingTest extends ComponentTest {

  function getRoots() {
    return array(__DIR__ . "/../resources/failures");
  }

  function testJSONLint() {
    try {
      parent::testJSONLint();
    } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
      // expected
      return;
    }

    $this->fail("Expected an assertion failure");
  }

  function testPHPLint() {
    try {
      parent::testPHPLint();
    } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
      // expected
      return;
    }

    $this->fail("Expected an assertion failure");
  }

  /**
   * @return true if {@link #testPHPLint()} error should be printed to the console
   */
  function printPHPLintErrors() {
    return false;
  }

}