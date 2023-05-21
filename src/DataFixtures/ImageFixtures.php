<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{

    private string $baseImagesDir;

    private string $uploadDir;

    private string $uploadDirTrick;

    private string $uploadDirAvatar;

    private string $uploadDirHome;

    public function __construct(string $baseImagesDir, string $uploadDir, string $uploadDirTrick, string $uploadDirAvatar, string $uploadDirHome)
    {
        $this->baseImagesDir = $baseImagesDir;
        $this->uploadDir = $uploadDir;
        $this->uploadDirTrick = $uploadDirTrick;
        $this->uploadDirAvatar = $uploadDirAvatar;
        $this->uploadDirHome = $uploadDirHome;
    }
    public function load(ObjectManager $manager): void
    {
        self::createFolder();
        self::CleanFixtureImages();
        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->findAllUser();
        foreach($users as $user){
            $username = $user->getUsername();
            $image = new Image();
            $image->setUser($user);
            $image->setPath( self::createAvatar($username));
            $image->setTrick(null);
            $manager->persist($image);
        }
        $trickRepository = $manager->getRepository(Trick::class);
        $tricks = $trickRepository->findAll();

        foreach($tricks as $trick){
            for($i = 1; $i <= 3; $i++){
                $image = new Image();
                $image->setUser(null);
                $image->setPath(self::createtrickImage($trick->getId(),$i));
                $image->setTrick($trick);
                $manager->persist($image);
            }
        }

        for($i = 1; $i <= 8; $i++){
            $image = new Image();
            $image->setUser(null);
            $image->setPath(self::createHomeImage('image'.$i));
            $image->setTrick(null);
            $manager->persist($image);
        }
        $manager->flush();
    }
    private function createFolder(){
        $filesystem = new Filesystem();
        $filesystem->chmod('public/build/', 0777);
        if(!$filesystem->exists('public/build/images')){
            $filesystem->mkdir('public/build/images/', 0777);
        }
        if(!$filesystem->exists('public/build/images/uploads')){
            $filesystem->mkdir('public/build/images/uploads/', 0777);
        }
        if(!$filesystem->exists('public/build/images/uploads/')){
            $filesystem->mkdir('public/build/images/uploads/', 0777);
        }
    }
    private function createAvatar($username) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirAvatar)){
            $filesystem->mkdir($this->uploadDirAvatar, 0777);
        }
        $filesystem->copy($this->uploadDir.'originals/avatar.png',$this->uploadDirAvatar.$username.'.png');
        return $this->uploadDirAvatar.$username.'.png';
    }
    private function createtrickImage($trickId,$number) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirTrick)){
            $filesystem->mkdir($this->uploadDirTrick, 0777);
        }
        $filesystem->copy($this->uploadDir.'originals/'.$number.'.png',$this->uploadDirTrick.$trickId.'/'.$number.'.png');
        return '/build/images/uploads/tricks/'.$trickId.'/'.$number.'.png';
    }

    private function createHomeImage($name) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirHome)){
            $filesystem->mkdir($this->uploadDirHome, 0777);
        }
        $filesystem->copy($this->uploadDir.'originals/'.$name.'.png',$this->uploadDirHome.$name.'.png');
        return '/build/images/uploads/home/'.$name.'.png';
    }

    private function CleanFixtureImages() {
        $filesystem = new Filesystem();
        $finder = new finder();
        $findAvatarsFiles = [];
        $findHomeFiles = [];
        $findTricksFolders = [];

        if($filesystem->exists($this->uploadDirAvatar)){
            $findAvatarsFiles = $finder->in($this->uploadDirAvatar)->depth(0)->name("*")->sortByName();
        }
        if($filesystem->exists($this->uploadDirHome)){
            $findHomeFiles = $finder->in($this->uploadDirHome)->depth(0)->name("*")->sortByName();
        }
        if($filesystem->exists($this->uploadDirTrick)){
            $findTricksFolders = $finder->in($this->uploadDirTrick)->sortByName()->getIterator();
        }
        foreach($findAvatarsFiles as $file) {
            $filesystem->remove($this->uploadDirAvatar.$file->getRelativePathname());
        }
        foreach($findHomeFiles as $homeFile) {
            $filesystem->remove($this->uploadDirHome.$homeFile->getRelativePathname());
        }
        foreach($findTricksFolders as $folders) {
            $filesystem->remove($this->uploadDirTrick.$folders->getRelativePathname());
        }
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TrickFixtures::class,
        ];
    }
}
