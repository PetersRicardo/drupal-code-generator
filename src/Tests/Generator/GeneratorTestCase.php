<?php

namespace DrupalCodeGenerator\Tests\Generator;

use DrupalCodeGenerator\Tests\WorkingDirectoryTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Base class for generators tests.
 */
abstract class GeneratorTestCase extends TestCase {

  use WorkingDirectoryTrait;

  protected $application;

  /**
   * Generator command to be tested.
   *
   * @var \Symfony\Component\Console\Command\Command
   */
  protected $command;

  protected $commandName;

  protected $answers;

  protected $commandTester;

  protected $display;

  protected $fixtures;

  protected $filesystem;

  protected $class;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->initWorkingDirectory();

    $command_class = 'DrupalCodeGenerator\Command\\' . $this->class;
    $this->command = new $command_class();
    $this->commandName = $this->command->getName();

    $this->application = dcg_create_application();
    $this->application->add($this->command);

    $this->mockQuestionHelper();
    $this->commandTester = new CommandTester($this->command);
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown() {
    $this->removeWorkingDirectory();
  }

  /**
   * Mocks question helper.
   */
  protected function mockQuestionHelper() {
    $question_helper = $this->createMock('Symfony\Component\Console\Helper\QuestionHelper');

    // The answers can be either a numeric array or an associated array keyed by
    // keyed by question text.
    if (isset($this->answers[0])) {
      foreach ($this->answers as $key => $answer) {
        $question_helper
          ->expects($this->at($key + 2))
          ->method('ask')
          ->willReturn($answer);
      }
    }
    else {
      $question_helper
        ->method('ask')
        ->will($this->returnCallback(function () {
          preg_match('#<info>(.*)</info>#', func_get_arg(2)->getQuestion(), $match);
          return $this->answers[$match[1]];
        }));
    }

    // We override the question helper with our mock.
    $this->command->getHelperSet()->set($question_helper, 'question');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute() {
    $this->commandTester->execute([
      'command' => $this->command->getName(),
      '--directory' => $this->directory,
    ]);

    $this->display = $this->commandTester->getDisplay();
  }

  /**
   * Checks the file.
   *
   * @param string $file
   *   The file to check.
   * @param string $fixture
   *   The fixture to compare the file content.
   */
  protected function checkFile($file, $fixture) {
    $this->assertFileExists($this->directory . '/' . $file);
    $this->assertFileEquals($this->directory . '/' . $file, $fixture);
  }

  /**
   * Test callback.
   */
  public function testExecute() {
    $this->execute();
    $targets = implode("\n- ", array_keys($this->fixtures));
    $output = "The following directories and files have been created or updated:\n- $targets\n";
    $this->assertEquals($output, $this->commandTester->getDisplay());
    // Tests may provide targets without fixtures.
    foreach (array_filter($this->fixtures) as $target => $fixture) {
      $this->checkFile($target, $fixture);
    }
  }

}
