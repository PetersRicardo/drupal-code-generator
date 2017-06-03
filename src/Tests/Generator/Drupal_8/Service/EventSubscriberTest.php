<?php

namespace DrupalCodeGenerator\Tests\Generator\Drupal_8\Service;

use DrupalCodeGenerator\Tests\Generator\GeneratorTestCase;

/**
 * Test for d8:service/event-subscriber command.
 */
class EventSubscriberTest extends GeneratorTestCase {

  protected $class = 'Drupal_8\Service\EventSubscriber';

  protected $answers = [
    'Foo',
    'foo',
    TRUE,
  ];

  protected $fixtures = [
    'src/EventSubscriber/FooSubscriber.php' => __DIR__ . '/_event_subscriber.php',
    'tests.services.yml' => __DIR__ . '/_event_subscriber.services.yml',
  ];

}
