<?php

namespace NG\FormBundle\Twig\Extension;

use Symfony\Bridge\Twig\Extension\FormExtension as BaseFormExtension,
    Symfony\Component\Form\FormView;

/**
 * Map for extension
 */
class MapFormExtension extends BaseFormExtension
{
  /**
   * {@inheritdoc}
   */
  public function getFunctions()
  {
    return array(
      'ng_form_javascript' => new \Twig_Function_Method($this, 'renderJavascript', array('is_safe' => array('html')))
    );
  }
  
  /**
   * Render javascript
   *
   * @param FormView $form
   * @param bool $prototype
   */
  public function renderJavascript(FormView $view, $prototype = FALSE)
  {
    $block = $prototype ? 'javascript_prototype' : 'javascript';
    return $this->renderer->searchAndRenderBlock($view, $block);
  }
  
  /**
   * Get name
   */
  public function getName()
  {
    return 'ng.form.twig.extension';
  }
}