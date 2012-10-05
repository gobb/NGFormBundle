<?php

namespace NG\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface,
    Symfony\Component\DependencyInjection\Reference;

/**
 * Transformer compiler pass
 * For add transformers to CKEditorType form
 */
class TransformerCompilerPass implements CompilerPassInterface
{
  /**
   * Process
   *
   * @param ContainerBuilder $container
   *
   * @return boid
   */
  public function process(ContainerBuilder $container)
  {
    if ($container->hasDefinition('ng.form.ckeditor') === FALSE) {
      return;
    }
    
    $defination = $container->getDefinition('ng.form.ckeditor');
    
    foreach ($container->findTaggedServiceIds('ng_form.transformer') as $id => $attributes) {
      $defination->addMethodCall('addTransformer', array(new Reference($id), $attributes[0]['alias']));
    }
  }
}