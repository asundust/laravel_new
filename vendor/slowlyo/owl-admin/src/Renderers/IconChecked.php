<?php

namespace Slowlyo\OwlAdmin\Renderers;

/**
 * IconChecked
 *
 * @author  slowlyo
 * @version 6.5.0
 */
class IconChecked extends BaseRenderer
{
    public function __construct()
    {


    }

    /**
     *
     */
    public function id($value = '')
    {
        return $this->set('id', $value);
    }

    /**
     *
     */
    public function name($value = '')
    {
        return $this->set('name', $value);
    }

    /**
     *
     */
    public function svg($value = '')
    {
        return $this->set('svg', $value);
    }


}
