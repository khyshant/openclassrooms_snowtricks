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

    private RequestStack $requestStack;
    /**
     * @var string
     */
    private string $uploadDirTrick;

    /**
     * ImageListener constructor.
     * @param string $uploadDirTrick
     * @param RequestStack $requestStack
     */
    public function __construct(string $uploadDirTrick, RequestStack $requestStack)
    {
        $this->uploadDirTrick = $uploadDirTrick;
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
        dump($this->requestStack->getMainRequest()->files);
        $filename = md5(uniqid("", true)) . "." . $image->getUploadedFile()->guessExtension();
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirTrick.$this->requestStack->getMainRequest()->attributes->get('id'))){
            $filesystem->mkdir($this->uploadDirTrick.$this->requestStack->getMainRequest()->attributes->get('id'), 0777);
        }
        dump($filesystem->copy($image->getUploadedFile(),$this->uploadDirTrick.$this->requestStack->getMainRequest()->attributes->get('id').'/'.$filename));
        
        $image->setPath('/build/images/uploads/tricks/'.$this->requestStack->getMainRequest()->attributes->get('id').'/'.$filename);
    }
}
