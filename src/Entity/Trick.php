<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[ORM\Table(name: 'trick')]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide')]
    #[Assert\Length(min: 5, minMessage: 'Le titre doit faire minimum 5 caractère', max: 70, maxMessage: 'le titre ne doit pas faire plus de 70 caractère')]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaDescription = null;

    #[ORM\Column]
    private ?bool $valid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateUpdate = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\OneToMany(mappedBy: 'Trick', targetEntity: Image::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    private ?GroupTrick $groupTrick = null;

    public function __construct()
    {
        $this->setDateAdd() ;
        $this->setDateUpdate() ;
        $this->setValid(false) ;
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        if(empty($this->getSlug())){
            $this->setSlug();
        }
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(): ?string
    {
        /* One source among others:
                    http://www.tachyonsoft.com/uc0000.htm
                    http://www.tachyonsoft.com/uc0001.htm
                    http://www.tachyonsoft.com/uc0004.htm
                https://www.compart.com/
                */
        $patterns = array(
            /* Lowercase */
            /* a  */ '/[\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}\x{0101}\x{0103}\x{0105}\x{0430}\x{00C0}-\x{00C3}\x{1EA0}-\x{1EB7}]/u',
            /* b  */ '/[\x{0431}]/u',
            /* c  */ '/[\x{00E7}\x{0107}\x{0109}\x{010D}\x{0446}]/u',
            /* d  */ '/[\x{010F}\x{0111}\x{0434}\x{0110}\x{00F0}]/u',
            /* e  */ '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}\x{0435}\x{044D}\x{00C8}-\x{00CA}\x{1EB8}-\x{1EC7}]/u',
            /* f  */ '/[\x{0444}]/u',
            /* g  */ '/[\x{011F}\x{0121}\x{0123}\x{0433}\x{0491}]/u',
            /* h  */ '/[\x{0125}\x{0127}]/u',
            /* i  */ '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}\x{0438}\x{0456}\x{00CC}\x{00CD}\x{1EC8}-\x{1ECB}\x{0128}]/u',
            /* j  */ '/[\x{0135}\x{0439}]/u',
            /* k  */ '/[\x{0137}\x{0138}\x{043A}]/u',
            /* l  */ '/[\x{013A}\x{013C}\x{013E}\x{0140}\x{0142}\x{043B}]/u',
            /* m  */ '/[\x{043C}]/u',
            /* n  */ '/[\x{00F1}\x{0144}\x{0146}\x{0148}\x{0149}\x{014B}\x{043D}]/u',
            /* o  */ '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{014D}\x{014F}\x{0151}\x{043E}\x{00D2}-\x{00D5}\x{01A0}\x{01A1}\x{1ECC}-\x{1EE3}]/u',
            /* p  */ '/[\x{043F}]/u',
            /* r  */ '/[\x{0155}\x{0157}\x{0159}\x{0440}]/u',
            /* s  */ '/[\x{015B}\x{015D}\x{015F}\x{0161}\x{0441}]/u',
            /* ss */ '/[\x{00DF}]/u',
            /* t  */ '/[\x{0163}\x{0165}\x{0167}\x{0442}]/u',
            /* u  */ '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{0169}\x{016B}\x{016D}\x{016F}\x{0171}\x{0173}\x{0443}\x{00D9}-\x{00DA}\x{0168}\x{01AF}\x{01B0}\x{1EE4}-\x{1EF1}]/u',
            /* v  */ '/[\x{0432}]/u',
            /* w  */ '/[\x{0175}]/u',
            /* y  */ '/[\x{00FF}\x{0177}\x{00FD}\x{044B}\x{1EF2}-\x{1EF9}\x{00DD}]/u',
            /* z  */ '/[\x{017A}\x{017C}\x{017E}\x{0437}]/u',
            /* ae */ '/[\x{00E6}]/u',
            /* ch */ '/[\x{0447}]/u',
            /* kh */ '/[\x{0445}]/u',
            /* oe */ '/[\x{0153}]/u',
            /* sh */ '/[\x{0448}]/u',
            /* shh*/ '/[\x{0449}]/u',
            /* ya */ '/[\x{044F}]/u',
            /* ye */ '/[\x{0454}]/u',
            /* yi */ '/[\x{0457}]/u',
            /* yo */ '/[\x{0451}]/u',
            /* yu */ '/[\x{044E}]/u',
            /* zh */ '/[\x{0436}]/u',

            /* Uppercase */
            /* A  */ '/[\x{0100}\x{0102}\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}\x{0410}]/u',
            /* B  */ '/[\x{0411}]/u',
            /* C  */ '/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}\x{0426}]/u',
            /* D  */ '/[\x{010E}\x{0110}\x{0414}\x{00D0}]/u',
            /* E  */ '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}\x{0415}\x{042D}]/u',
            /* F  */ '/[\x{0424}]/u',
            /* G  */ '/[\x{011C}\x{011E}\x{0120}\x{0122}\x{0413}\x{0490}]/u',
            /* H  */ '/[\x{0124}\x{0126}]/u',
            /* I  */ '/[\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}\x{0418}\x{0406}]/u',
            /* J  */ '/[\x{0134}\x{0419}]/u',
            /* K  */ '/[\x{0136}\x{041A}]/u',
            /* L  */ '/[\x{0139}\x{013B}\x{013D}\x{0139}\x{0141}\x{041B}]/u',
            /* M  */ '/[\x{041C}]/u',
            /* N  */ '/[\x{00D1}\x{0143}\x{0145}\x{0147}\x{014A}\x{041D}]/u',
            /* O  */ '/[\x{00D3}\x{014C}\x{014E}\x{0150}\x{041E}]/u',
            /* P  */ '/[\x{041F}]/u',
            /* R  */ '/[\x{0154}\x{0156}\x{0158}\x{0420}]/u',
            /* S  */ '/[\x{015A}\x{015C}\x{015E}\x{0160}\x{0421}]/u',
            /* T  */ '/[\x{0162}\x{0164}\x{0166}\x{0422}]/u',
            /* U  */ '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{0168}\x{016A}\x{016C}\x{016E}\x{0170}\x{0172}\x{0423}]/u',
            /* V  */ '/[\x{0412}]/u',
            /* W  */ '/[\x{0174}]/u',
            /* Y  */ '/[\x{0176}\x{042B}]/u',
            /* Z  */ '/[\x{0179}\x{017B}\x{017D}\x{0417}]/u',
            /* AE */ '/[\x{00C6}]/u',
            /* CH */ '/[\x{0427}]/u',
            /* KH */ '/[\x{0425}]/u',
            /* OE */ '/[\x{0152}]/u',
            /* SH */ '/[\x{0428}]/u',
            /* SHH*/ '/[\x{0429}]/u',
            /* YA */ '/[\x{042F}]/u',
            /* YE */ '/[\x{0404}]/u',
            /* YI */ '/[\x{0407}]/u',
            /* YO */ '/[\x{0401}]/u',
            /* YU */ '/[\x{042E}]/u',
            /* ZH */ '/[\x{0416}]/u',

            /* ~ */ '/[\x{007E}]/u',
            /* " */ '/[\x{0022}]/u',
            /* # */ '/[\x{0023}]/u',
            /* ' */ '/[\x{0027}]/u',
            /* ( */ '/[\x{0028}]/u',
            /* ` */ '/[\x{0060}]/u',
            /* _ */ '/[\x{005F}]/u',
            /* ) */ '/[\x{0029}]/u',
            /* = */ '/[\x{003D}]/u',
            /* . */ '/[\x{002E}]/u',
            /* , */ '/[\x{002C}]/u',
            /* ! */ '/[\x{0021}]/u',
            /* ? */ '/[\x{003F}]/u',
            /*   */ '/[\x{0020}]/u',
            /* ° */ '/[\x{00B0}]/u',
        );

        // ö to oe
        // å to aa
        // ä to ae

        $replacements = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 'ss', 't', 'u', 'v', 'w', 'y', 'z', 'ae', 'ch', 'kh', 'oe', 'sh', 'shh', 'ya', 'ye', 'yi', 'yo', 'yu', 'zh',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'w', 'y', 'z', 'ae', 'ch', 'kh', 'oe', 'sh', 'shh', 'ya', 'ye', 'yi', 'yo', 'yu', 'zh',
            '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-',  '-',  '-',
        );
        $this->slug =  strtolower(preg_replace($patterns, $replacements, $this->getTitle()));
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(): void
    {
        $this->dateAdd = new \DateTime();
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(): void
    {
        $this->dateUpdate = new \DateTime();
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setTrick($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getTrick() === $this) {
                $image->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    public function getGroupTrick(): ?GroupTrick
    {
        return $this->groupTrick;
    }

    public function setGroupTrick(?GroupTrick $groupTrick): self
    {
        $this->groupTrick = $groupTrick;

        return $this;
    }
}
