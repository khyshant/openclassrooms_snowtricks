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
    private $uploadDirAvatarAbsolutePath;

    /**
     * @var string
     */
    private $uploadDirTricksAbsolutePath;

    /**
     * @var string
     */
    private $uploadDirFixturesAbsolutePath;

    /**

     * @param string $uploadDirAvatarAbsolutePath
     */
    public function __construct(string $uploadDirAvatarAbsolutePath, string $uploadDirTricksAbsolutePath,string $uploadDirFixturesAbsolutePath)
    {
        $this->uploadDirAvatarAbsolutePath = $uploadDirAvatarAbsolutePath;
        $this->uploadDirTricksAbsolutePath = $uploadDirTricksAbsolutePath;
        $this->uploadDirFixturesAbsolutePath = $uploadDirFixturesAbsolutePath;
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
        if(!$filesystem->exists($this->uploadDirFixturesAbsolutePath.'avatar/')){
            $filesystem->mkdir($this->uploadDirFixturesAbsolutePath.'avatar/', 0744);
        }
        $filesystem->copy($this->uploadDirFixturesAbsolutePath.'/originals/avatar.png',$this->uploadDirFixturesAbsolutePath.'avatar/'.$username.'.png');
        return $this->uploadDirAvatarAbsolutePath.'/'.$username.'.png';
    }
    private function createtrickImage($trickId,$i) {
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->uploadDirFixturesAbsolutePath.'tricks/')){
            $filesystem->mkdir($this->uploadDirFixturesAbsolutePath.'tricks/', 0744);
        }
        if(!$filesystem->exists($this->uploadDirFixturesAbsolutePath.'tricks/'.$trickId)){
            $filesystem->mkdir($this->uploadDirFixturesAbsolutePath.'tricks/'.$trickId, 0744);
        } else {
            $filesystem->copy($this->uploadDirFixturesAbsolutePath.'/originals/'.$i.'.png',$this->uploadDirFixturesAbsolutePath.'tricks/'.$trickId.'/'.$i.'.png');
        }
        return $this->uploadDirTricksAbsolutePath.'/'.$trickId.'/'.$i.'.png';
    }

    private function CleanFixtureImages() {
        $filesystem = new Filesystem();
        $finder = new finder();
        $findAvatarsFiles = [];
        $findTricksFolders = [];
        if($filesystem->exists($this->uploadDirFixturesAbsolutePath.'avatar/')){
            $findAvatarsFiles = $finder->in($this->uploadDirFixturesAbsolutePath.'avatar/')->depth(0)->name("*")->sortByName();
        }
        if($filesystem->exists($this->uploadDirFixturesAbsolutePath.'tricks/')){
            $findTricksFolders = $finder->in($this->uploadDirFixturesAbsolutePath.'tricks/')->sortByName()->getIterator();
        }
        foreach($findAvatarsFiles as $file) {
            $filesystem->remove($this->uploadDirFixturesAbsolutePath.'avatar/'.$file->getRelativePathname());
        }
        foreach($findTricksFolders as $folders) {
            $filesystem->remove($this->uploadDirFixturesAbsolutePath.'tricks/'.$folders->getRelativePathname());
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
