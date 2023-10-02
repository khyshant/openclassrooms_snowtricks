<?php


namespace App\Security\voter;


use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TrickVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const CREATE = 'create';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, mixed $subject) : bool
    {
// if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Trick) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param Trick $subject
     * @param TokenInterface $token
     * @return bool|void
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token) : bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Trick $trick */
        $trick = $subject;
   
        switch ($attribute) {
            case self::CREATE:
                return true; // Laisser la création toujours autorisée, ajustez ultérieurement
            case self::DELETE:
                return $this->canDelete($trick, $user);
    
            case self::EDIT:
                return $this->canEdit($trick, $user);
    
            default:
                throw new \LogicException("Vous n'avez pas l'autorisation de voir ceci");
        }
    }

    private function canEdit(Trick $trick, User $user): bool
    {
        return $user === $trick->getAuthor();
    }

    private function canDelete(Trick $trick, User $user): bool
    {
        $role = "ROLE_ADMIN";
        $return = false;
        if (in_array($role, $user->getRoles())) {
            $return =  true;
        }
        if ($user === $trick->getAuthor()) {
            $return = true;
        }
        return $return;
    }
}