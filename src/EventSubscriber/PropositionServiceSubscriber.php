<?php

namespace App\EventSubscriber;

use App\Entity\Historiqueservice;
use App\Entity\Ligneservice;
use App\Entity\LigneMouvement;
use App\Entity\Mouvementservice;
use App\Entity\Notification;
use App\Entity\NotificationPrestataire;
use App\Entity\PrestataireService;
use App\Entity\ServicePrestataire;
use App\Entity\WorkflowServicePrestataire;
use App\Repository\ArticleMagasinRepository;
use App\Repository\MagasinRepository;
use App\Repository\SensRepository;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;
use function Symfony\Component\Config\Definition\Builder\find;

class PropositionServiceSubscriber implements EventSubscriberInterface
{
  private $em;
  private $sensRepository;
  protected $security;
  protected $magasin;
  protected $articleMagasinRepository;
  protected  $service;
  protected  $workflow;

  private function numero()
  {

    $query = $this->em->createQueryBuilder();
    $query->select("count(a.id)")
      ->from(ServicePrestataire::class, 'a');

    $nb = $query->getQuery()->getSingleScalarResult();
    if ($nb == 0) {
      $nb = 1;
    } else {
      $nb = $nb + 1;
    }
    return (date("y") . 'CAT' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
  }


  public function __construct(EntityManagerInterface $em, \Symfony\Component\Workflow\Registry $workflow)
  {
    ///  $this->security = $security;
    $this->em = $em;
    $this->workflow = $workflow;
  }

  public function handleValidation(TransitionEvent $event): void
  {

    $transition_name = $event->getTransition()->getName();
    $entity = $event->getSubject();

    //  dd($entity);
    $entity->setDateValidation(new \DateTime());
    $this->em->flush();


    $service = new ServicePrestataire();
    $service->setCategorie($entity->getCategorie());
    $service->setLibelle($entity->getLibelle());
    $service->setCode($this->numero());
    $this->em->persist($service);
    $this->em->flush();
    //dd($entity->getCategorie());
    $message  = sprintf('Nous venons par ce message vous annoncer que votre proposition de service à été validée avec success et se trouve dans la catgorie %s ', $entity->getCategorie()->getLibelle());
    $notification = new Notification();
    $notification->setDateCreation(new DateTime())
      ->setEtat(false)
      ->setTitre('Message validation')
      ->setMessage($message);


    $this->em->persist($notification);
    $this->em->flush();

    $notifcationPrestataire = new NotificationPrestataire();
    $notifcationPrestataire->setUtilisateur($entity->getPrestataire());
    $notifcationPrestataire->setNotification($notification);
    $this->em->persist($notifcationPrestataire);
    $this->em->flush();
  }

  public function handleRejeter(TransitionEvent $event)
  {
    $transition_name = $event->getTransition()->getName();
    $entity = $event->getSubject();
    $entity->setDateValidation(new \DateTime());
    $this->em->flush();

    $notification = new Notification();
    $notification->setDateCreation(new DateTime())
      ->setEtat(false)
      ->setTitre('Message')
      ->setMessage("Nous venons par ce message vous annoncer que votre proposition d'ajout d'un nouveau service à été réjetée");


    $this->em->persist($notification);
    $this->em->flush();

    $notifcationPrestataire = new NotificationPrestataire();
    $notifcationPrestataire->setUtilisateur($entity->getPrestataire());
    $notifcationPrestataire->setNotification($notification);
    $this->em->persist($notifcationPrestataire);
    $this->em->flush();
  }



  public static function getSubscribedEvents(): array
  {
    return [
      'workflow.add_proposition_service.transition.passer' => 'handleValidation',
      'workflow.add_proposition_service.transition.rejeter' => 'handleRejeter',

    ];
  }
}
