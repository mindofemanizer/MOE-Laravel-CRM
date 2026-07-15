<?php
declare(strict_types=1);

namespace Moe\CRM\Contracts;

interface LeadInterface
{
    /**
     * @return string
     */
    public function getStage(): string;

    /**
     * @return float
     */
    public function getExpectedValue(): float;
}
