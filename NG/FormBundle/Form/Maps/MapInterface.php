<?php

namespace NG\FormBundle\Form\Maps;

/**
 * Interface for control map field type
 */
interface MapInterface {
  /**
   * Get map api URL
   *
   * @return string
   */
  public function getMapApiUrl(array $configs);
  
}