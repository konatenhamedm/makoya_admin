<?php

namespace App\EventSubscriber;

use App\Entity\PublicitePrestataire;
use App\Entity\UtilisateurSimple;
use App\Repository\UserFrontRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class DemandePublicitePrestataireSubscriber implements EventSubscriberInterface
{
  private $em;
  private $sensRepository;
  protected $security;
  protected $magasin;
  protected $repo;
  protected  $service;
  protected  $workflow;
  protected $notificationService;

  private function numero()
  {

    $query = $this->em->createQueryBuilder();
    $query->select("count(a.id)")
      ->from(PublicitePrestataire::class, 'a');

    $nb = $query->getQuery()->getSingleScalarResult();
    if ($nb == 0) {
      $nb = 1;
    } else {
      $nb = $nb + 1;
    }
    return (date("y") . 'PUP' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
  }


  public function __construct(EntityManagerInterface $em, \Symfony\Component\Workflow\Registry $workflow, UserFrontRepository $repo, NotificationService $notificationService)
  {
    ///  $this->security = $security;
    $this->em = $em;
    $this->workflow = $workflow;
    $this->repo = $repo;
    $this->notificationService = $notificationService;
  }

  public function handleValidation(TransitionEvent $event): void
  {

    $transition_name = $event->getTransition()->getName();
    $entity = $event->getSubject();

    //  dd($entity);
    $entity->setDateValidation(new \DateTime());
    $this->em->flush();

    //dd($entity->getPrestataire());
    $publicite = new PublicitePrestataire();
    $publicite->setCode($this->numero());
    $publicite->setLibelle($entity->getLibelle());
    $publicite->setDateCreation(new \DateTime());
    $publicite->setType("Prestataire");
    $publicite->setDateFin($entity->getDateFin());
    $publicite->setDateDebut($entity->getDateDebut());
    $publicite->setUtilisateur($this->repo->findOneBy(array('reference' => $entity->getPrestataire()->getReference())));
    $this->em->persist($publicite);
    $this->em->flush();

    /*     $notification = new Notification();
    $notification->setDateCreation(new DateTime())
      ->setEtat(false)
      ->setTitre('Message validation')
      ->setMessage("Nous venons par ce message vous annoncer que votre demande de publicté  à été validée avec success nous vous contacterons pour plus de details");


    $this->em->persist($notification);
    $this->em->flush();

    $notifcationPrestataire = new NotificationPrestataire();
    $notifcationPrestataire->setPrestataire($entity->getPrestataire());
    $notifcationPrestataire->setNotification($notification);
    $this->em->persist($notifcationPrestataire);
    $this->em->flush(); */

    $this->notificationService->getNotification("Nous venons par ce message vous annoncer que votre demande de publicté  à été validée avec success nous vous contacterons pour plus de details", "Message validation", false, $entity->getPrestataire(), new UtilisateurSimple);
  }

  public function handleRejeter(TransitionEvent $event)
  {
    $transition_name = $event->getTransition()->getName();
    $entity = $event->getSubject();
    $entity->setDateValidation(new \DateTime());
    $this->em->flush();
    /* 
    $notification = new Notification();
    $notification->setDateCreation(new DateTime())
      ->setEtat(false)
      ->setTitre('Message')
      ->setMessage("Nous venons par ce message vous annoncer que votre demande de publicité à été réjetée");


    $this->em->persist($notification);
    $this->em->flush();

    $notifcationPrestataire = new NotificationPrestataire();
    $notifcationPrestataire->setPrestataire($entity->getPrestataire());
    $notifcationPrestataire->setNotification($notification);
    $this->em->persist($notifcationPrestataire);
    $this->em->flush(); */

    $this->notificationService->getNotification("Nous venons par ce message vous annoncer que votre demande de publicité à été réjetée", "Message rejeter", false, $entity->getPrestataire(), new UtilisateurSimple);
  }



  public static function getSubscribedEvents(): array
  {
    return [
      'workflow.add_demande_publicite.transition.passer' => 'handleValidation',
      'workflow.add_demande_publicite.transition.rejeter' => 'handleRejeter',

    ];
  }
}
