<?php

namespace NG\FormBundle\Form\CKEditor;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Form\DataTransformerInterface;

/**
 * Form type for control CKEditor wysiwyg
 */
class CKEditorType extends AbstractType
{
  // Container
  protected $container = NULL;
  
  // Transformers
  protected $transformers = array();
  
  /**
   * Construct
   *
   * @param ContainerInterface $container
   */
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
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
    $options = array(
      'transformers',
      'toolbar',
      'toolbar_groups',
      'startup_outline_blocks',
      'ui_color',
      'width',
      'height',
      'language',
      'filebrowser_browse_url',
      'filebrowser_upload_url',
      'filebrowser_image_browse_url',
      'filebrowser_image_upload_url',
      'filebrowser_flash_browse_url',
      'filebrowser_flash_upload_url',
      'skin',
      'format_tags',
      'base_href',
      'body_class',
      'content_css',
      'external_plugins'
    );
    
    $options = array_flip($options);
    
    foreach ($options as $key => $value) {
      $options[$key] = $this->container->getParameter('ng_form.ckeditor.' . $key);
    }
    
    // Set default
    $resolver->setDefaults($options);
    
    // Set allowed types
    $resolver->setAllowedTypes(array(
      'transformers' => 'array',
      'toolbar' => 'array',
      'toolbar_groups' => 'array',
      'format_tags' => 'array'
    ));
  }
  
  /**
   * Add transfromer to form
   *
   */
  public function addTransformer(DataTransformerInterface $transformer, $alias)
  {
    if (isset($this->transformers[$alias])) {
      throw new \LogicException(sprintf('Transformer alias must be unique (%s)', $alias));
    }
    
    $this->transformers[$alias] = $transformer;
  }
  
  /**
   * Form build
   *
   * @param FormBuilderInterface $builder
   * @param array $options
   *
   * @return void
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    foreach ($options['transformers'] as $transformerAlias) {
      if (isset($this->transformers[$transformerAlias])) {
        $builder->addViewTransformer($this->transformers[$transformerAlias]);
      }
      else {
        throw new \InvalidArgumentException(sprintf('"%s" is not valid transformer.', $transformerAlias));
      }
    }
    
    $options['toolbar_groups'] = array_merge(
      $this->container->getParameter('ng_form.ckeditor.toolbar_groups'),
      $options['toolbar_groups']
    );
  }
  
  /**
   * Build form view
   *
   * @param FormView $view
   * @param FormInterface $form
   * @param array $options
   *
   * @return void
   */
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    // Toolbar group must be exists
    if (!is_array($options['toolbar_groups']) || !count($options['toolbar_groups'])) {
      throw new \InvalidArgumentException('You must supply at least 1 toolbar group.');
    }
    
    $toolbarGroupKeys = array_keys($options['toolbar_groups']);
    
    $toolbar = array();
    foreach ($options['toolbar'] as $toolbarId){
      if ($toolbarId == '/') {
        $toolbar[] = $toolbarId;
      }
      else {
        if (!in_array($toolbarId, $toolbarGroupKeys)) {
          throw new \RuntimeException(sprintf('The toolbar "%s" does not exists. Kmow options are "%s"', $toolbarId. implode('", "', $toolbarGroupKeys)));
        }
        
        $toolbar[] = array(
          'name' => $toolbarId,
          'items' => $options['toolbar_groups'][$toolbarId]
        );
      }
    }
    
    $view->vars['toolbar'] = $toolbar;
    
    $optionsSets = array(
      'startup_outline_blocks',
      'ui_color',
      'width',
      'height',
      'language',
      'filebrowser_browse_url',
      'filebrowser_upload_url',
      'filebrowser_image_browse_url',
      'filebrowser_image_upload_url',
      'filebrowser_flash_browse_url',
      'filebrowser_flash_upload_url',
      'skin',
      'format_tags',
      'base_href',
      'body_class',
      'content_css',
      'external_plugins'
    );
    
    foreach ($optionsSets as $optSet) {
      $view->vars[$optSet] = $options[$optSet];
    }
  }
  
  /**
   * Get parent
   */
  public function getParent()
  {
    return 'textarea';
  }
  
  /**
   * Get name
   */
  public function getName()
  {
    return 'ng_form_ckeditor';
  }
}