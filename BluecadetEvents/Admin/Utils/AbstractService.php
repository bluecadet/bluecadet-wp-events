<?php

namespace BluecadetEvents\Admin\Utils;

/**
 * For use with a class that registers hooks
 *
 */
abstract class AbstractService {
  public function register(): void {}
  abstract public function boot(): void;
}
