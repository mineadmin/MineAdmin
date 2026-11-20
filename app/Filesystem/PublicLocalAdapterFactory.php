<?php

declare(strict_types=1);

namespace App\Filesystem;

use Hyperf\Filesystem\Contract\AdapterFactoryInterface;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;

class PublicLocalAdapterFactory implements AdapterFactoryInterface
{
    public function make(array $options)
    {
        return new LocalFilesystemAdapter(
            $options['root'],
            new PortableVisibilityConverter(
                directoryPublic: 0755,
                directoryPrivate: 0755,
                defaultForDirectories: Visibility::PUBLIC,
            ),
        );
    }
}
