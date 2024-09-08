<?php

namespace App\Services\Contracts;

use Illuminate\Http\UploadedFile;
interface ILocalStorageService
{
    public function storeLocal(UploadedFile $file): string;
}
