<?php

namespace DrupalCodeGenerator\Commands\Drupal_8;

use DrupalCodeGenerator\Commands\BaseGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Implements d8:template command.
 */
class Template extends BaseGenerator {

  protected $name = 'd8:template';
  protected $description = 'Generates a template';
  protected $alias = 'template';

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    $questions = $this->getDefaultQuestions();
    $questions['template_name'] = [
      'Template name', [$this, 'defaultTemplateName'],
    ];
    $questions['create_theme'] = ['Create theme hook?', 'yes'];
    $questions['create_preprocess'] = ['Create preprocess hook?', 'yes'];
    $vars = $this->collectVars($input, $output, $questions);

    $path = $this->createPath('templates/', $vars['template_name'] . '.html.twig', $vars['machine_name']);
    $this->files[$path] = $this->render('d8/template-template.twig', $vars);

    if ($vars['create_theme']) {
      $this->hooks[$vars['machine_name'] . '.module'][] = [
        'file_doc' => $this->render('d8/file-docs/module.twig', $vars),
        'code' => $this->render('d8/template-theme.twig', $vars),
      ];
    }
    if ($vars['create_preprocess']) {
      $this->hooks[$vars['machine_name'] . '.module'][] = [
        'file_doc' => $this->render('d8/file-docs/module.twig', $vars),
        'code' => $this->render('d8/template-preprocess.twig', $vars),
      ];
    }
  }

  /**
   * Return default template name.
   */
  protected function defaultTemplateName($vars) {
    return str_replace('_', '-', $vars['machine_name']) . '-example';
  }

}
