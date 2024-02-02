<?php


namespace App\Controller;


use App\Controller\FileTrait;
use App\Service\Menu;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Registry;

class BaseController extends AbstractController
{
    use FileTrait;

    protected const UPLOAD_PATH = 'media_entreprise';
    protected $em;
    protected $security;
    protected $menu;
    protected $hasher;
    protected $workflow;
    protected $notificationService;


    public function __construct(EntityManagerInterface $em, Menu $menu, Security $security, UserPasswordHasherInterface $hasher, Registry $workflow, NotificationService $notificationService)
    {
        $this->em = $em;
        $this->notificationService = $notificationService;
        $this->security = $security;
        $this->menu = $menu;
        $this->hasher = $hasher;
        $this->workflow = $workflow;
    }
}
