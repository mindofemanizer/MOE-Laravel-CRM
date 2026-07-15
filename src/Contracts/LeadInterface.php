<?php
declare(strict_types=1);

namespace Moe\CRM\Contracts;

interface LeadInterface
{
    public function getStage(): string;
    public function getExpectedValue(): float;
}
