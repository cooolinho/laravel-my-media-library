<?php

namespace App\Config;

enum FilesystemEnum: string
{
    case DISK_LOCAL = 'local';
    case DISK_PUBLIC = 'public';
}
