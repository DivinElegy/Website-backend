<?php
/**
 * @package DIExample
 * @copyright 2013 Internet Brands, Inc. All Rights Reserved.
 */
namespace Domain\Entities;

/**
 * Bar
 *
 * @author Michael Funk <mfunk@internetbrands.com>
 */
class Bar implements BarInterface
{

    /**
     * just returns a hardcoded string
     *
     * @return string
     */
    public function returnMe()
    {
        return 'This is from the bar class';
    }
}