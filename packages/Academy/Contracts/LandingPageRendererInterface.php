<?php

declare(strict_types=1);

namespace Academy\Contracts;

use Academy\Models\AcademyProgram;

interface LandingPageRendererInterface
{
    /**
     * Render the landing page for a program.
     *
     * @param AcademyProgram $program
     *
     * @return string Rendered HTML
     */
    public function render(AcademyProgram $program): string;

    public function getData(AcademyProgram $program): array;
}
