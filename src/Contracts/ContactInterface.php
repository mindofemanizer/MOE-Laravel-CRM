<?php

namespace Moe\CRM\Contracts;

interface ContactInterface
{
    public function getFullName(): string;
    public function getEmail(): string;
}
