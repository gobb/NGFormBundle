<?php


namespace NG\FormBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 *
 */
class Configuration implements ConfigurationInterface
{
  /**
   * Get config tree builder
   *
   * @return TreeBuilder
   */
  public function getConfigTreeBuilder()
  {
    // Create a tree builder
    $treeBuilder = new TreeBuilder();
    
    // Create a root node for configuration
    $rootNode = $treeBuilder->root('ng_form');
    
    // CKEditor
    $this->configTreeBuilderCKEditor($rootNode);
    
    // Maps
    $this->configTreeBuilderMaps($rootNode);
    
    return $treeBuilder;
  }
  
  /**
   * Add tree configuration for CKEditor
   *
   * @param ArrayNodeDefinition $rootNode
   */
  private function configTreeBuilderCKEditor(ArrayNodeDefinition $rootNode)
  {
    $rootNode
      ->children()
        ->arrayNode('ckeditor')
          ->canBeUnset()
          ->addDefaultsIfNotSet()
          ->children()
            ->scalarNode('form_core_type')
              ->defaultValue('NG\FormBundle\Form\CKEditor\CKEditorType')
              ->info('Core for control CKEditor form type.')
              ->end()
            ->arrayNode('transformers')
              ->defaultValue(array('strip_js', 'strip_css', 'strip_comments'))
              ->info('Default data transformers for the submitted html.')
              ->prototype('scalar')->end()
              ->end()
            ->arrayNode('toolbar')
              ->defaultValue(array(
                'document', 'clipboard', 'editing', '/',
                'basicstyles', 'paragraph', 'links', '/',
                'insert', 'styles', 'tools'
              ))
              ->info('The default toolbar displayed on the editor.')
              ->prototype('scalar')->end()
              ->end()
            ->arrayNode('toolbar_groups')
              ->defaultValue(array())
              ->info('Groups of icons in the  editor')
              ->prototype('scalar')->end()
              ->end()
            ->booleanNode('startup_outline_blocks')
              ->defaultTrue()
              ->info('Whether to automaticaly enable the "show block" command when the editor loads.')
              ->end()
            ->scalarNode('ui_color')
              ->defaultNull()
              ->info('The base user interface color to be used by the editor. Must be a hex.')
              ->example('#AABBFF')
              ->end()
            ->scalarNode('width')
              ->defaultNull()
              ->info('The editor UI outer width. Must be a integer value or percentage.')
              ->example('500 or 70%')
              ->end()
            ->scalarNode('height')
              ->defaultNull()
              ->info('The height of the editing area (that includes the editor content).')
              ->example('400')
              ->end()
            ->scalarNode('language')
              ->defaultNull()
              ->info('The user interface language localization to use.')
              ->example('en-au')
              ->end()
            ->scalarNode('filebrowser_browse_url')
              ->defaultNull()
              ->info('The location of an external file browser that should be launched when the Browse Server button is pressed.')
              ->end()
            ->scalarNode('filebrowser_upload_url')
              ->defaultNull()
              ->info('The location of the script that handles file uploads.')
              ->end()
            ->scalarNode('filebrowser_image_browse_url')
              ->defaultNull()
              ->info('The location of an external file browser that should be launched when the Browse Server button is pressed in the Image dialog window.')
              ->end()
            ->scalarNode('filebrowser_image_upload_url')
              ->defaultNull()
              ->info('The location of the script yhat handles file uploads in the Image dialog window.')
              ->end()
            ->scalarNode('filebrowser_flash_browse_url')
              ->defaultNull()
              ->info('The location of an external file browser that should be launched when the Browse Server button is pressed in the Flash dialog window.')
              ->end()
            ->scalarNode('filebrowser_flash_upload_url')
              ->defaultNull()
              ->info('The location of the script that handles file uploads in the Flash dialog window.')
              ->end()
            ->scalarNode('skin')
              ->defaultNull()
              ->info('The skin to load. It may be the name of the skin folder inside the editor installation path, or the name and the path separated by a comma.')
              ->end()
            ->arrayNode('format_tags')
              ->defaultValue(array())
              ->info('An array of style names (by default tags) representing the style definition for each entry to be displayed in the Format combo in the toolbar.')
              ->example('[p, h2, h3, pre]')
              ->prototype('scalar')->end()
              ->end()
            ->scalarNode('base_href')
              ->defaultNull()
              ->info('The base href URL used to resolve relative and absolute URLs in the editor content.')
              ->end()
            ->scalarNode('body_class')
              ->defaultNull()
              ->info('Sets the class attribute to be used on the body element of the editing area.')
              ->end()
            ->scalarNode('content_css')
              ->defaultNull()
              ->info('The CSS file(s), to be used to apply style to editor contents.')
              ->end()
            ->arrayNode('external_plugins')
              ->useAttributeAsKey(TRUE)
                ->prototype('array')
                  ->children()
                    ->scalarNode('path')->isRequired()->end()
                    ->scalarNode('file')->defaultValue('plugin.js')->end()
                    ->end()
                ->end()
              ->end()
          ->end()
        ->end()
      ->end()
    ;
  }
  
  /**
   * Add tree configuration for maps
   *
   * @param ArrayNodeDefinition $rootNode
   */
  protected function configTreeBuilderMaps(ArrayNodeDefinition $rootNode)
  {
    $rootNode
      ->children()
        ->arrayNode('yandex_map')
          ->canBeUnset()
          ->addDefaultsIfNotSet()
          ->children()
            ->scalarNode('api_lang')
              ->isRequired()
              ->defaultValue('ru-RU')
              ->info('Language for map')
              ->example('ru-RU')
              ->validate()
                ->ifNotInArray(array('ru-RU', 'en-US', 'tr-TR', 'uk-UA'))
                ->thenInvalid('Invalid language code "%s". Know langs: ru-RU, en-US, tr-TR, uk-UA.')
                ->end()
              ->end()
            ->scalarNode('api_coordorder')
              ->defaultValue('latlong')
              ->info('How to set geographical coordinates functions')
              ->example('latlog')
              ->validate()
                ->ifNotInArray(array('latlong', 'longlat'))
                ->thenInvalid('Invalid geographical coordinates type "%s". Allowed "latlong" and "longlat"')
                ->end()
              ->end()
            ->scalarNode('api_key')
              ->defaultNull()
              ->info('API key for using Yandex map')
              ->end()
            ->arrayNode('api_package')
              ->isRequired()
              ->defaultValue(array('full'))
              ->prototype('scalar')->end()
              ->info('Packages of load Map.')
              ->example('["standard", "geoObjects"]')
              ->end()
            ->scalarNode('api_mode')
              ->isRequired()
              ->defaultValue('release')
              ->info('Mode for load API. Debug or release.')
              ->validate()
                ->ifNotInArray(array('release', 'debug'))
                ->thenInvalid('Invalid mode load "%s". Allowed "release" or "debug" mode.')
                ->end()
              ->end()
            ->scalarNode('api_ns')
              ->defaultValue('ymaps')
              ->info('Namespace for loads api.')
              ->example('myNewNamespace')
              ->end()
            ->scalarNode('api_load_async')
              ->defaultTrue()
              ->info('Async load API file')
              ->end()
            ->scalarNode('map_width')
              ->defaultNull()
              ->info('Width container for map.')
              ->example('400px or 80%')
              ->end()
            ->scalarNode('map_height')
              ->defaultNull()
              ->info('Height container for map.')
              ->example('300px')
              ->end()
            ->scalarNode('map_width_height')
              ->defaultValue('4:3')
              ->info('Proporties for width end height map.')
              ->example('4:3')
              ->end()
            ->scalarNode('center_x')
              ->defaultNull()
              ->info('Center coordinate X.')
              ->example('55.33')
              ->end()
            ->scalarNode('center_y')
              ->defaultNull()
              ->info('Center coordinate Y.')
              ->example('22.77')
              ->end()
            ->scalarNode('zoom')
              ->defaultValue(10)
              ->info('Default zoom for map.')
              ->example('10')
              ->end()
            ->arrayNode('control')
              ->defaultValue(array('mapTools', 'typeSelector', 'zoomControl'))
              ->info('Map controls')
              ->example("['mapTools', 'typeSelector', 'zoomControl']")
              ->prototype('scalar')->end()
              ->end()
          ->end()
        ->end()
      ->end()
    ;
  }
}