<?php

namespace App\EventSubscriber;

use App\Entity\HistoriqueDemande;
use App\Entity\LigneDemande;
use App\Entity\LigneMouvement;
use App\Entity\MouvementDemande;
use App\Entity\Notification;
use App\Entity\NotificationPrestataire;
use App\Entity\PrestataireService;
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

class DemandeSubscriber implements EventSubscriberInterface
{
  private $em;
  private $sensRepository;
  protected $security;
  protected $magasin;
  protected $articleMagasinRepository;
  protected  $service;
  protected  $workflow;


  public function __construct(EntityManagerInterface $em, \Symfony\Component\Workflow\Registry $workflow)
  {
    ///  $this->security = $security;
    $this->em = $em;
    $this->workflow = $workflow;
  }

  public function handleValidation(TransitionEvent $event): void
  {

    dd("lklsj");
    $transition_name = $event->getTransition()->getName();
    $entity = $event->getSubject();

    //  dd($entity);
    $entity->setDateValidation(new \DateTime());
    $this->em->flush();



    $demande = new PrestataireService();
    $demande->setEtat(true);
    $demande->setCategorie($entity->getCategorie());
    $demande->setPrestataire($entity->getPrestataire());
    $demande->setImage($entity->getImage());
    if ($entity->getSousCategorie() != null)
      $demande->setSousCategorie($entity->getSousCategorie());
    $demande->setService($entity->getService());


    $this->em->persist($demande);
    $this->em->flush();

    $notification = new Notification();
    $notification->setDateCreation(new DateTime())
      ->setEtat(false)
      ->setTitre('Message validation')
      ->setMessage("Nous venons par ce message vous annoncer que votre demande d'ajout d'un nouveau service à été validée avec success");


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
      ->setMessage("Nous venons par ce message vous annoncer que votre demande d'ajout d'un nouveau service à été réjetée");


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
      'workflow.add_prestation_service.transition.passer' => 'handleValidation',
      'workflow.add_prestation_service.transition.rejeter' => 'handleRejeter',

    ];
  }
}
