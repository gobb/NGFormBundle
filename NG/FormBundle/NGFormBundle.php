<?php

namespace NG\FormBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    NG\FormBundle\DependencyInjection\Compiler\TransformerCompilerPass;

class NGFormBundle extends Bundle
{
  /**
   * Build
   *
   * @param ContainerBuilder $container
   *
   * @return void
   */
  public function build(ContainerBuilder $container)
  {
    parent::build($container);
    
    // Add Transformers
    $container->addCompilerPass(new TransformerCompilerPass());
  }
}