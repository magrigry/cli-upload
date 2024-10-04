<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class EmptyFileException extends BadRequestException
{
    public function __construct()
    {
        parent::__construct('File is empty.');
    }
}
