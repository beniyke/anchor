<?php

declare(strict_types=1);

namespace Intervention\Image\Imagick\Commands;

class GreyscaleCommand extends \Intervention\Image\Commands\AbstractCommand
{
    /**
     * Turns an image into a greyscale version
     *
     * @param \Intervention\Image\Image $image
     *
     * @return bool
     */
    public function execute($image)
    {
        return $image->getCore()->modulateImage(100, 0, 100);
    }
}
