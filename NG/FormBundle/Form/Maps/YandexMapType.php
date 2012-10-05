<?php

namespace NG\FormBundle\Form\Maps;


use Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Form\FormView;

/**
 * Yandex map type
 */
class YandexMapType extends AbstractMapType
{
  /**
   * Get api URI
   */
  public function getMapApiUrl(array $configs)
  {
    $get = function($p) {
      return $this->container->getParameter('ng_form.yandex_map.' . $p);
    };
    
    $uriOptions = array(
      'lang' => $get('api_lang'),
      'coordorder' => $get('api_coordorder'),
      'key' => $get('api_key'),
      'mode' => $get('api_mode'),
      'ns' => $get('api_ns'),
      'load' => in_array('full', $get('api_package')) ? 'package.full' : implode(',', array_map (function($pack){
          if (!is_string($pack)) {
            throw new \InvalidArgumentException(sprintf('Name package must be a string, %s given', gettype($pack)));
          }
          
          if (strpos($pack, 'package.') === 0) {
            return $pack;
          }
          
          return 'package.' . $pack;
        }, $get('api_package')))
    );
    
    $uriQuery = http_build_query($uriOptions);
    
    return 'http://api-maps.yandex.ru/2.0-stable/?' . $uriQuery;
  }
  
  /**
   * Set default options
   *
   * @param OptionsResolverInterface $resolver
   *
   * @return void
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $options = parent::_getDefaultOptions();
    $options[] = 'api_ns';
    $options[] = 'api_load_async';
    $options[] = 'control';
    
    $options = array_flip($options);
    
    foreach ($options as $key => $value){
      $options[$key] = $this->container->getParameter('ng_form.yandex_map.' . $key);
    }
    
    $resolver->setDefaults($options);
    
    // Set allowed types
    $resolver->setAllowedTypes(array(
      'control' => 'array'
    ));
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
    parent::buildView($view, $form, $options);
    
    $optionSets = array(
      'api_ns',
      'api_load_async'
    );
    
    foreach ($options['control'] as $controlName) {
      $view->vars['control_' . strtolower($controlName)] = TRUE;
    }
    
    foreach ($optionSets as $optName) {
      $view->vars[$optName] = $options[$optName];
    }
  }
  
  
  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return 'ng_form_yandex_map';
  }
}