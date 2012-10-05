<?php

namespace NG\FormBundle\DependencyInjection;


use Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\Config\FileLocator;


/**
 * Dependency injection extension
 */
class NGFormExtension extends Extension
{
  /**
   * Load extension
   *
   * @param array $configs
   * @param ContainerBuilder $container
   *
   * @return void
   */
  public function load(array $configs, ContainerBuilder $container)
  {
    $configs = $this->processConfiguration(new Configuration(), $configs);
    
    // Load config and services
    $this->loadServices($container);
    
    // Extends twig templates
    $this->extendTwigTemplates($container);
    
    
    // Init CKEditor
    $this->loadCKEditor($configs, $container);
    
    // Init maps
    $this->loadMaps($configs, $container);
  }
  
  /**
   * Load services
   *
   * @param ContainerBuilder $container
   *
   * @return void
   */
  protected function loadServices(ContainerBuilder $container)
  {
    // Create loader object
    $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    
    // Load CKEditor
    $loader->load('ckeditor.xml');
    
    // Load Maps
    $loader->load('maps.xml');
    
    // Load twig services
    $loader->load('twig.xml');
  }
  
  /**
   * Extend twig templates
   *
   * @param ContainerBuilder
   *
   * @return void
   */
  protected function extendTwigTemplates(ContainerBuilder $container)
  {
    $container->setParameter('twig.form.resources', array_merge(
      $container->getParameter('twig.form.resources'),
      array(
        'NGFormBundle:Form:editors_widget.html.twig',
        'NGFormBundle:Form:maps_widget.html.twig',
        'NGFormBundle:Form:maps_javascript.html.twig'
      )
    ));
  }
  
  /**
   * Init CKEditor configs and services
   *  Add template widget, parameters to container,
   *  default parameters and validate configuration.
   *
   * @param array $configs
   * @param ContainerBuilder $container
   *
   * @return void
   */
  protected function loadCKEditor(array &$configs, ContainerBuilder $container)
  { 
    // Merge toolbar groups
    $configs['ckeditor']['toolbar_groups'] = array_merge(
      $this->getDefaultCKEditorToolbarGroups(),
      $configs['ckeditor']['toolbar_groups']
    );
    
    $dissalowParameter = array();
    foreach ($configs['ckeditor'] as $keyName => $keyValue) {
      // If key disabled
      if (in_array($keyName, $dissalowParameter)) {
        continue;
      }
      
      $container->setParameter('ng_form.ckeditor.' . $keyName, $keyValue);
    }
  }
  
  /**
   * Load maps
   *
   * @param array $configs
   * @param ContainerBuilder $container
   * @return void
   */
  public function loadMaps(array $configs, ContainerBuilder $container)
  {
    // Maps configuration
    $configMaps = array(
      'yandex_map' => $configs['yandex_map'],
      'gmap' => array()//$configs['gmap']
    );
    
    // Dissalow parameter
    $dissalowParameter = array(
      'yandex_map' => array(),
      'gmap' => array()
    );
    
    foreach ($configMaps as $coreMaps => $configMaps) {
      foreach ($configMaps as $keyName => $keyValue) {
        // If key disabled to add conteiner
        if (in_array($keyName, $dissalowParameter[$coreMaps])) {
          continue;
        }
        
        // Add parameter to container
        $container->setParameter('ng_form.' . $coreMaps . '.' . $keyName, $keyValue);
      }
    }
  }
  
  /**
   * Get default toolbar groups
   *
   * @return array
   */
  protected function getDefaultCKEditorToolbarGroups()
  {
    return array(
      'document' => array(
        'Source', '-', 'Save', '-', 'Templates'
      ),
      
      'clipboard' => array(
        'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'
      ),
      
      'editing' => array(
        'Find', 'Replace', '-', 'SelectAll'
      ),
      
      'basicstyles' => array(
        'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-',
        'RemoveFormat',
      ),
      
      'paragraph'   => array(
        'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft',
        'JustifyCenter', 'JustifyRight', 'JustifyBlock',
      ),
      
      'links'       => array(
        'Link', 'Unlink', 'Anchor',
      ),
      
      'insert'      => array(
        'Image', 'Flash', 'Table', 'HorizontalRule',
      ),
      
      'styles'      => array(
        'Styles', 'Format',
      ),
      
      'tools'       => array(
        'Maximize', 'ShowBlocks',
      )
    );
  }
}