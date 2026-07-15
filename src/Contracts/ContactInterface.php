<?php
declare(strict_types=1);

namespace Moe\CRM\Contracts;

interface ContactInterface
{
    /**
     * @return string
     */
    public function getFullName(): string;

    /**
     * @return string
     */
    public function getEmail(): string;
}
