<?php

declare(strict_types=1);

namespace Intervention\Image\Gd\Commands;

class InvertCommand extends \Intervention\Image\Commands\AbstractCommand
{
    /**
     * Inverts colors of an image
     *
     * @param \Intervention\Image\Image $image
     *
     * @return bool
     */
    public function execute($image)
    {
        return imagefilter($image->getCore(), IMG_FILTER_NEGATE);
    }
}
