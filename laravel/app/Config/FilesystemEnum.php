<?php

namespace App\Config;

enum FilesystemEnum: string
{
    case DISK_LOCAL = 'local';
    case DISK_PUBLIC = 'public';
    case DISK_WAREZ_LOGOS = 'warez_logos';
}
