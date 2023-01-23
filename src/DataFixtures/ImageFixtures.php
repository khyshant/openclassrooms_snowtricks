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

    private string $uploadDirFixtures;

    private string $uploadDirFixturesTrick;

    private string $uploadDirFixturesAvatar;

    private string $uploadDirFixturesHome;

    public function __construct(string $baseImagesDir, string $uploadDirFixtures, string $uploadDirFixturesTrick, string $uploadDirFixturesAvatar, string $uploadDirFixturesHome)
    {
        $this->baseImagesDir = $baseImagesDir;
        $this->uploadDirFixtures = $uploadDirFixtures;
        $this->uploadDirFixturesTrick = $uploadDirFixturesTrick;
        $this->uploadDirFixturesAvatar = $uploadDirFixturesAvatar;
        $this->uploadDirFixturesHome = $uploadDirFixturesHome;
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
        if(!$filesystem->exists('public/build/images/uploads/fixtures')){
            $filesystem->mkdir('public/build/images/uploads/fixtures/', 0777);
        }
    }
    private function createAvatar($username) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirFixturesAvatar)){
            $filesystem->mkdir($this->uploadDirFixturesAvatar, 0777);
        }
        $filesystem->copy($this->uploadDirFixtures.'originals/avatar.png',$this->uploadDirFixturesAvatar.$username.'.png');
        return '/build/images/uploads/fixtures/avatar/'.$username.'.png';
    }
    private function createtrickImage($trickId,$number) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirFixturesTrick)){
            $filesystem->mkdir($this->uploadDirFixturesTrick, 0777);
        }
        $filesystem->copy($this->uploadDirFixtures.'originals/'.$number.'.png',$this->uploadDirFixturesTrick.$trickId.'/'.$number.'.png');
        return '/build/images/uploads/fixtures/tricks/'.$trickId.'/'.$number.'.png';
    }

    private function createHomeImage($name) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirFixturesHome)){
            $filesystem->mkdir($this->uploadDirFixturesHome, 0777);
        }
        $filesystem->copy($this->uploadDirFixtures.'originals/'.$name.'.png',$this->uploadDirFixturesHome.$name.'.png');
        return '/build/images/uploads/fixtures/home/'.$name.'.png';
    }

    private function CleanFixtureImages() {
        $filesystem = new Filesystem();
        $finder = new finder();
        $findAvatarsFiles = [];
        $findHomeFiles = [];
        $findTricksFolders = [];

        if($filesystem->exists($this->uploadDirFixturesAvatar)){
            $findAvatarsFiles = $finder->in($this->uploadDirFixturesAvatar)->depth(0)->name("*")->sortByName();
        }
        if($filesystem->exists($this->uploadDirFixturesHome)){
            $findHomeFiles = $finder->in($this->uploadDirFixturesHome)->depth(0)->name("*")->sortByName();
        }
        if($filesystem->exists($this->uploadDirFixturesTrick)){
            $findTricksFolders = $finder->in($this->uploadDirFixturesTrick)->sortByName()->getIterator();
        }
        foreach($findAvatarsFiles as $file) {
            $filesystem->remove($this->uploadDirFixturesAvatar.$file->getRelativePathname());
        }
        foreach($findHomeFiles as $homeFile) {
            $filesystem->remove($this->uploadDirFixturesHome.$homeFile->getRelativePathname());
        }
        foreach($findTricksFolders as $folders) {
            $filesystem->remove($this->uploadDirFixturesTrick.$folders->getRelativePathname());
        }
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TrickFixtures::class,
            CommentFixtures::class
        ];
    }
}
