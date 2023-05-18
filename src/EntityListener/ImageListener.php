<?php

namespace App\EntityListener;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ImageListener
 * @package App\EntityListener
 */
class ImageListener
{
    /**
     * @var string
     */
    private $uploadDirFixturesTrick;
    private RequestStack $requestStack;

    /**
     * ImageListener constructor.
     * @param string $uploadDirFixturesTrick
     * @param RequestStack $requestStack
     */
    public function __construct(string $uploadDirFixturesTrick, RequestStack $requestStack)
    {
        $this->uploadDirFixturesTrick = $uploadDirFixturesTrick;
        $this->requestStack = $requestStack;
    }


    /**
     * @param Image $image
     */
    public function prePersist(Image $image)
    {
        if ($image->getUploadedFile() === null) {
            return;
        }
        //dump($this->requestStack->getMainRequest()->files->);
        $filename = md5(uniqid("", true)) . "." . $image->getUploadedFile()->guessExtension();
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirFixturesTrick.$this->requestStack->getMainRequest()->attributes->get('id'))){
            $filesystem->mkdir($this->uploadDirFixturesTrick.$this->requestStack->getMainRequest()->attributes->get('id'), 0777);
        }
        $filesystem->copy($image->getUploadedFile(),$this->uploadDirFixturesTrick.$this->requestStack->getMainRequest()->attributes->get('id').'/'.$filename);
        $image->setPath($this->uploadDirFixturesTrick.$this->requestStack->getMainRequest()->attributes->get('id').'/'.$filename);
    }
}
