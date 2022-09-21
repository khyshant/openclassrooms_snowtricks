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
    /**
     * @var string
     */
    private $uploadDirAvatar;

    /**
     * @var string
     */
    private $uploadDirTricks;

    /**
     * @var string
     */
    private $uploadDirFixtures;

    /**
     * @param string $uploadDirAvatar
     */
    public function __construct(string $uploadDirAvatar, string $uploadDirTricks,string $uploadDirFixtures)
    {
        $this->uploadDirAvatar = $uploadDirAvatar;
        $this->uploadDirTricks = $uploadDirTricks;
        $this->uploadDirFixtures = $uploadDirFixtures;
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
        $manager->flush();
    }

    private function createAvatar($username) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirFixtures.'avatar/')){
            $filesystem->mkdir($this->uploadDirFixtures.'avatar/', 0744);
        }
        $filesystem->copy($this->uploadDirFixtures.'/originals/avatar.png',$this->uploadDirFixtures.'avatar/'.$username.'.png');
        return $this->uploadDirAvatar.'/'.$username.'.png';
    }
    private function createtrickImage($trickId,$i) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirFixtures.'tricks/')){
            $filesystem->mkdir($this->uploadDirFixtures.'tricks/', 0744);
        }
        if(!$filesystem->exists($this->uploadDirFixtures.'tricks/'.$trickId)){
            $filesystem->mkdir($this->uploadDirFixtures.'tricks/'.$trickId, 0744);
        } else {
            $filesystem->copy($this->uploadDirFixtures.'/originals/'.$i.'.png',$this->uploadDirFixtures.'tricks/'.$trickId.'/'.$i.'.png');
        }
        return $this->uploadDirTricks.'/'.$trickId.'/'.$i.'.png';
    }

    private function CleanFixtureImages() {
        $filesystem = new Filesystem();
        $finder = new finder();
        $findAvatarsFiles = [];
        $findTricksFolders = [];
        if($filesystem->exists($this->uploadDirFixtures.'avatar/')){
            $findAvatarsFiles = $finder->in($this->uploadDirFixtures.'avatar/')->depth(0)->name("*")->sortByName();
        }
        if($filesystem->exists($this->uploadDirFixtures.'tricks/')){
            $findTricksFolders = $finder->in($this->uploadDirFixtures.'tricks/')->sortByName()->getIterator();
        }
        foreach($findAvatarsFiles as $file) {
            $filesystem->remove($this->uploadDirFixtures.'avatar/'.$file->getRelativePathname());
        }
        foreach($findTricksFolders as $folders) {
            $filesystem->remove($this->uploadDirFixtures.'tricks/'.$folders->getRelativePathname());
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
