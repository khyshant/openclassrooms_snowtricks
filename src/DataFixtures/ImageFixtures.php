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

    private string $baseUploadDirFixture;

    public function __construct(string $baseImagesDir, string $uploadDirFixtures)
    {
        $this->baseImagesDir = $baseImagesDir;
        $this->uploadDirFixtures = $uploadDirFixtures;
        $this->baseUploadDirFixture = $uploadDirFixtures;
    }
    public function load(ObjectManager $manager): void
    {

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

    private function createAvatar($username) {
        $baseUploadDirFixture = $this->baseImagesDir.$this->uploadDirFixtures;
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->baseUploadDirFixture.'avatar/')){
            $filesystem->mkdir($this->baseUploadDirFixture.'avatar/', 0744);
        }
        dump($this->baseImagesDir);
        dump($this->baseUploadDirFixture);

        $filesystem->copy($this->baseUploadDirFixture.'originals/avatar.png',$this->baseUploadDirFixture.'avatar/'.$username.'.png');
        return '/build/images/uploads/fixtures/avatar/'.$username.'.png';
    }
    private function createtrickImage($trickId,$number) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->baseUploadDirFixture.'tricks/')){
            $filesystem->mkdir($this->baseUploadDirFixture.'tricks/', 0744);
        }

        $filesystem->copy($this->baseUploadDirFixture.'originals/'.$number.'.png',$this->baseUploadDirFixture.'tricks/'.$trickId.'/'.$number.'.png');
        return '/build/images/uploads/fixtures/tricks/'.$trickId.'/'.$number.'.png';
    }

    private function createHomeImage($name) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->baseUploadDirFixture.'home/')){
            $filesystem->mkdir($this->baseUploadDirFixture.'home/', 0744);
        }
        $filesystem->copy($this->baseUploadDirFixture.'originals/'.$name.'.png',$this->baseUploadDirFixture.'home/'.$name.'.png');
        return '/build/images/uploads/fixtures/home/'.$name.'.png';
    }

    private function CleanFixtureImages() {
        $filesystem = new Filesystem();
        $finder = new finder();
        $findAvatarsFiles = [];
        $findHomeFiles = [];
        $findTricksFolders = [];

        if($filesystem->exists($this->baseUploadDirFixture.'avatar/')){
            $findAvatarsFiles = $finder->in($this->baseUploadDirFixture.'avatar/')->depth(0)->name("*")->sortByName();
        }
        if($filesystem->exists($this->baseUploadDirFixture.'home/')){
            $findHomeFiles = $finder->in($this->baseUploadDirFixture.'home/')->depth(0)->name("*")->sortByName();
        }
        if($filesystem->exists($this->baseUploadDirFixture.'tricks/')){
            $findTricksFolders = $finder->in($this->baseUploadDirFixture.'tricks/')->sortByName()->getIterator();
        }
        foreach($findAvatarsFiles as $file) {
            $filesystem->remove($this->baseUploadDirFixture.'avatar/'.$file->getRelativePathname());
        }
        foreach($findHomeFiles as $homeFile) {
            $filesystem->remove($this->baseUploadDirFixture.'home/'.$homeFile->getRelativePathname());
        }
        foreach($findTricksFolders as $folders) {
            $filesystem->remove($this->baseUploadDirFixture.'tricks/'.$folders->getRelativePathname());
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
