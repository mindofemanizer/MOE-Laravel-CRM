<?php
declare(strict_types=1);

namespace Moe\CRM\Contracts;

interface SegmentableInterface
{
    /**
     * @return mixed
     */
    public function segments();
}
