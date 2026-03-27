<?php

declare(strict_types=1);

namespace App\Profile\Mapper;

use App\Auth\Entity\User;
use App\Integration\Phoenix\Dto\PhoenixPhotoData;
use App\Photo\Entity\Photo;

final readonly class ImportedPhoenixPhotoMapper
{
    public function createPhoto(PhoenixPhotoData $data, User $owner): Photo
    {
        $photo = new Photo();
        $photo->setImageUrl($data->getPhotoUrl())
            ->setCamera($data->getCamera())
            ->setDescription($data->getDescription())
            ->setLocation($data->getLocation())
            ->setTakenAt($data->getTakenAt())
            ->setUser($owner);

        return $photo;
    }
}
