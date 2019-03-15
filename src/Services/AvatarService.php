<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-14
 * Time: 21:02
 */

namespace App\Services;


use App\Entity\UserBaseClass;

class AvatarService
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UserBaseClass $user, $content)
    {
        $image = imagecreatefromstring($content);
        $imageName = $user->getId().'avatar.jpeg';
        imagepng($image,$this->targetDirectory.$imageName);
        $user->setAvatar($imageName);
    }
}
