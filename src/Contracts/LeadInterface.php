<?php

namespace Moe\CRM\Contracts;

interface LeadInterface
{
    public function getStage(): string;
    public function getExpectedValue(): float;
}
