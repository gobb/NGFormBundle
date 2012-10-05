<?php


namespace NG\FormBundle\Form\Maps;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\DependencyInjection\ContainerInterface;


abstract class AbstractMapType extends AbstractType implements MapInterface
{
  // Container
  protected $container = NULL;
  
  // JS api loaded
  protected static $__jsApiPath = NULL;
  
  /**
   * Consturct
   *  Set container for load configurations
   *
   * @param ContainerInterface $container
   */
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }
  
  /**
   * Get default options
   *
   * @return array
   */
  protected function _getDefaultOptions()
  {
    return array(
      'map_height',
      'map_width',
      'map_width_height',
      'center_x',
      'center_y',
      'zoom'
    );
  }
  
  /**
   * Build view
   *
   * @param FormView $view
   * @param FormInterface $form
   * @param array $options
   *
   * @return void
   */
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    if (!self::$__jsApiPath) {
      self::$__jsApiPath = $this->getMapApiUrl($options);
      $view->vars['mapJsHref'] = $this->getMapApiUrl($options);
    }
    
    $width = $height = 0;
    
    if ($options['map_width'] || $options['map_height']) {
      // Fix width and height
      if ($options['map_width'] && $options['map_height']) {
        // Generate width
        $width = $options['map_width'];
        
        if (strpos($width, '%') === FALSE && strpos($width, 'px') === FALSE) {
          $width .= 'px';
        }
        
        // Generate height
        $height = $options['map_height'];
        
        if (strpos($height, '%') === FALSE && strpos($height, 'px') === FALSE) {
          $height .= 'px';
        }
      }
      // Auto height
      else if ($options['map_width'] && !$options['map_height']) {
        
        if (!$options['map_width_height']) {
          throw new \LogicException("Key 'map_width_height' can\'t be null! Using auto generate height.");
        }
        
        $prec = explode(':', $options['map_width_height']);
        
        if (count($prec) != 2 || !$prec[0] || !$prec[1]) {
          throw new \InvalidArgumentException(sprintf("Key 'map_width_height' must be formatted 'width:height' (%s). Example: '4:3'.", $options['map_width_height']));
        }
        
        $width = $options['map_width'];
        
        $scaleWidth = intval($width) / $prec[0];
        
        $height = $scaleWidth * $prec[1];
        
        $view->vars['map_auto_size'] = TRUE;
        $view->vars['map_width_scale'] = $scaleWidth;
        $view->vars['map_height_prec'] = $prec[1];
        
        if (strpos($width, 'px') === FALSE) {
          $width .= 'px';
        }
      }
      else {
        throw new \RuntimeException('Can\t generating width and height.');
      }
    }
    // Only prec
    else if (!$options['map_width'] && !$options['map_height'] && $options['map_width_height']) {
      $prec = explode(':', $options['map_width_height']);
      
      if (count($prec) != 2 || !$prec[0] || !$prec[1]) {
        throw new \InvalidArgumentException(sprintf("Key 'map_width_height' must be formatted 'width:height' (%s). Example: '4:3'.", $options['map_width_height']));
      }
      
      $view->vars['map_auto_size'] = TRUE;
      $view->vars['map_width_prec'] = $prec[0];
      $view->vars['map_height_prec'] = $prec[1];
    }
    
    $view->vars['map_width'] = intval($width) ? $width : 0;
    $view->vars['map_height'] = intval($height) ? $height : 0;
    
    // Add coordinates and zoom to map
    $view->vars['center_x'] = $options['center_x'];
    $view->vars['center_y'] = $options['center_y'];
    $view->vars['zoom'] = $options['zoom'];
  }
  
  
  /**
   * Get parent
   */
  public function getParent()
  {
    return 'text';
  }
}